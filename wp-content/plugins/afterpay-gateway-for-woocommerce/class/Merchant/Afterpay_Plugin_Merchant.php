<?php
/**
* Afterpay Plugin Merchant Handler Class
*/

class Afterpay_Plugin_Merchant {
	/**
	 * Protected variables.
	 *
	 * @var		WC_Gateway_Afterpay	$gateway					A reference to the WooCommerce Payment Gateway for Afterpay.
	 * 															This is used for retreiving user settings, such as the API URL.
	 * @var		string				$payment_types_url			The full URL to the "payment-types" API endpoint (v0).
	 * @var		string				$configuration_url			The full URL to the "configuration" API endpoint (v1).
	 * @var		string				$orders_url					The full URL to the "orders" API endpoint (v0 and v1).
	 * @var		string				$direct_payment_capture_url	The full URL to the "orders" API endpoint (v1).
	 */
	protected $gateway, $payment_types_url, $configuration_url, $orders_url, $direct_payment_capture_url;

	/**
	 * Class constructor. Called when an object of this class is instantiated.
	 *
	 * @since	2.0.0
	 * @uses	WC_Gateway_Afterpay::get_api_url()
	 */
	public function __construct() {
		include('endpoints.php');
		$this->gateway = WC_Gateway_Afterpay::getInstance();
		$api_url = $this->gateway->get_api_url();
		$this->configuration_url = $api_url . $endpoints['v1']['configuration'];
		$this->orders_url = $api_url . $endpoints['v1']['orders'];
		$this->payments_url = $api_url . $endpoints['v1']['payments'];
		$this->direct_payment_capture_url = $api_url . $endpoints['v1']['capture-payment'];
	}

	/**
	 * Filters the string used for Merchant IDs & Secret Keys.
	 *
	 * @since	2.0.0
	 * @param	string	$str
	 * @return	string
	 * @used-by	self::build_authorization_header()
	 */
	private function cleanup_string($str) {
		return preg_replace('/[^a-z0-9]+/i', '', $str);
	}

	/**
	 * Build the Afterpay Authorization header for use with the APIs.
	 *
	 * @since	2.0.0
	 * @uses	self::cleanup_string()
	 * @uses	WC_Gateway_Afterpay::get_merchant_id()
	 * @uses	WC_Gateway_Afterpay::get_secret_key()
	 * @return	string
	 * @used-by	self::get_from_api()
	 * @used-by	self::post_to_api()
	 */
	private function build_authorization_header() {
		$cleaned_merchant_id = $this->cleanup_string($this->gateway->get_merchant_id());
		$cleaned_secret_key = $this->cleanup_string($this->gateway->get_secret_key());

		return 'Basic ' . base64_encode($cleaned_merchant_id . ':' . $cleaned_secret_key);
	}

	/**
	 * Build the Afterpay User-Agent header for use with the APIs.
	 *
	 * @since	2.0.0
	 * @global	string	$wp_version
	 * @uses	WC()
	 * @return	string
	 * @used-by	self::get_from_api()
	 * @used-by	self::post_to_api()
	 */
	private function build_user_agent_header() {
		global $wp_version;

		$plugin_version = Afterpay_Plugin::$version;
		$php_version = PHP_VERSION;
		$woocommerce_version = WC()->version;
		$merchant_id = $this->gateway->get_merchant_id();

		$extra_detail_1 = '';
		$extra_detail_2 = '';

		$matches = array();
		if (array_key_exists('SERVER_SOFTWARE', $_SERVER) && preg_match('/^[a-zA-Z0-9]+\/\d+(\.\d+)*/', $_SERVER['SERVER_SOFTWARE'], $matches)) {
			$s = $matches[0];
			$extra_detail_1 .= "; {$s}";
		}

		if (array_key_exists('REQUEST_SCHEME', $_SERVER) && array_key_exists('HTTP_HOST', $_SERVER)) {
			$s = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
			$extra_detail_2 .= " {$s}";
		}

		return "Afterpay Gateway for WooCommerce/{$plugin_version} (PHP/{$php_version}; WordPress/{$wp_version}; WooCommerce/{$woocommerce_version}; Merchant/{$merchant_id}{$extra_detail_1}){$extra_detail_2}";
	}

	/**
	 * Build an Afterpay-compatible Money object for use in the API posts.
	 *
	 * @since	2.0.0
	 * @param	float	$amount
	 * @param	string	$currency
	 * @uses	get_option('woocommerce_currency')
	 * @return	array
	 * @used-by	self::get_payment_types_for_amount()
	 * @used-by	self::create_refund()
	 */
	private function build_money_object($amount, $currency = '') {
		if (empty($currency)) {
			$currency = get_option('woocommerce_currency');
		}

		return array(
			'amount' => number_format((float)$amount, 2, '.', ''),
			'currency' => $currency
		);
	}

	/**
	 * Store an API error. If the last request to an API resulted in an error, store it in the options table
	 * so we can show it throughout the admin.
	 *
	 * @since	2.0.0
	 * @param	mixed	$error
	 * @return	bool
	 */
	private function store_api_error($error) {
		return update_option( 'woocommerce_afterpay_api_error', $error );
	}

	/**
	 * Clear the last API error. This should be called each time an API call executes successfully.
	 *
	 * @since	2.0.0
	 * @return	bool
	 */
	private function clear_api_error() {
		return delete_option( 'woocommerce_afterpay_api_error' );
	}

	/**
	 * Retrieve the Content-Type, HTTP_CORRELATION_ID and CF-Ray headers from the HTTP response.
	 *
	 * @since	2.1.0
	 * @param	array	$response	The array returned from wp_remote_get or wp_remote_post.
	 * @uses	wp_remote_retrieve_headers
	 * @return	array				An array of header values with lowercased keys.
	 * @used-by	self::parse_response
	 */
	private function get_response_headers($response) {
		$headers_obj = wp_remote_retrieve_headers( $response );
		return $headers_obj->getAll();
	}

	/**
	 * Parse the response from an Afterpay API.
	 * Returns an object if the response is JSON, and if it can be parsed.
	 *
	 * @since	2.1.0
	 * @param	array	$response	The array returned from wp_remote_get or wp_remote_post.
	 * @uses	wp_remote_retrieve_response_code
	 * @uses	self::get_response_headers
	 * @uses	wp_remote_retrieve_body
	 * @uses	WC_Gateway_Afterpay::log
	 * @return	StdClass|false		An object on success, or false on failure.
	 * @used-by	self::get_from_api
	 * @used-by	self::post_to_api
	 */
	private function parse_response($response) {
		$response_code = wp_remote_retrieve_response_code( $response );
		$headers_arr = $this->get_response_headers( $response );
		$body_str = wp_remote_retrieve_body( $response );

		if (array_key_exists('content-type', $headers_arr) && strpos(strtolower($headers_arr['content-type']), 'application/json') === 0) {
			$body_obj = json_decode($body_str);

			if (is_null($body_obj)) {
				WC_Gateway_Afterpay::log("API {$response_code} RESPONSE - PARSING ERROR #" . json_last_error() . ': ' . json_last_error_msg());
			}
		} else {
			$body_obj = null;

			WC_Gateway_Afterpay::log("API {$response_code} RESPONSE - UNEXPECTED FORMAT! content-type: " . (array_key_exists('content-type', $headers_arr) ? $headers_arr['content-type'] : 'null') . '; http_correlation_id: ' . (array_key_exists('http_correlation_id', $headers_arr) ? $headers_arr['http_correlation_id'] : 'null') . '; cf-ray: ' . (array_key_exists('cf-ray', $headers_arr) ? $headers_arr['cf-ray'] : 'null'));
		}

		if ($body_obj) {
			$return_obj = new \StdClass;
			$return_obj->body = $body_obj;

			return $return_obj;
		}

		return false;
	}

	/**
	 * GET from an API endpoint.
	 *
	 * @since	2.0.0
	 * @param	string	$url
	 * @uses	wp_remote_get()
	 * @uses	self::build_authorization_header()
	 * @uses	self::build_user_agent_header()
	 * @uses	self::parse_response
	 * @uses	WC_Gateway_Afterpay::log()
	 * @return	StdClass|WP_Error|false
	 * @used-by	self::get_payment_types()
	 * @used-by	self::get_configuration()
	 */
	private function get_from_api($url) {
		WC_Gateway_Afterpay::log("GET {$url}");

		$response = wp_remote_get( $url, array(
			'timeout' => 80,
			'headers' => array(
				'Authorization' => $this->build_authorization_header(),
				'User-Agent' => $this->build_user_agent_header(),
				'Accept' => 'application/json'
			)
		));

		if (!is_wp_error( $response )) {
			return $this->parse_response( $response );
		} else {
			# Unable to establish a secure connection with the Afterpay API endpoint.
			# Likely a TLS or network error.
			# Log the error details.
			foreach ($response->errors as $code => $messages_arr) {
				$messages_str = implode("\n", $messages_arr);
				WC_Gateway_Afterpay::log("API NETWORK ERROR! Code: \"{$code}\"; Message(s):\n" . $messages_str);
			}

			# Return the WP_Error object.
			return $response;
		}

		return false;
	}

	/**
	 * POST JSON to an API endpoint and load the response.
	 *
	 * @since	2.0.0
	 * @param	string	$url	The full URL to the API Endpoint.
	 * @param	mixed	$data	The jsonifiable data to be posted to the API.
	 * @uses	wp_remote_post()
	 * @uses	self::build_authorization_header()
	 * @uses	self::build_user_agent_header()
	 * @uses	self::parse_response
	 * @uses	WC_Gateway_Afterpay::log()
	 * @return	StdClass|false
	 * @used-by	self::get_payment_types_for_amount()
	 * @used-by	self::create_refund()
	 */
	private function post_to_api($url, $data) {
		WC_Gateway_Afterpay::log("POST {$url}");

		$data_str = json_encode($data);

		$response = wp_remote_post( $url, array(
			'timeout' => 80,
			'headers' => array(
				'Authorization' => $this->build_authorization_header(),
				'User-Agent' => $this->build_user_agent_header(),
				'Content-Type' => 'application/json',
				'Content-Length' => strlen($data_str),
				'Accept' => 'application/json'
			),
			'body' => $data_str
		) );

		if (!is_wp_error( $response )) {
			return $this->parse_response( $response );
		} else {
			# Unable to establish a secure connection with the Afterpay API endpoint.
			# Likely a TLS or network error.
			# Log the error details.
			foreach ($response->errors as $code => $messages_arr) {
				$messages_str = implode("\n", $messages_arr);
				WC_Gateway_Afterpay::log("API NETWORK ERROR! Code: \"{$code}\"; Message(s):\n" . $messages_str);
			}

			# Return the WP_Error object.
			return $response;
		}

		return false;
	}

	/**
	 * Get the valid payment types for this merchant.
	 *
	 * Note:	This is only for API v0.
	 *
	 * @since	2.0.0
	 * @uses	self::get_from_api()
	 * @uses	self::store_api_error()
	 * @uses	WC_Admin_Settings::add_error()
	 * @uses	self::clear_api_error()
	 * @uses	WC_Gateway_Afterpay::log()
	 * @return	array|false					The list of available types, or false on error.
	 */
	public function get_payment_types() {
		$response = $this->get_from_api($this->payment_types_url);

		if (is_wp_error( $response )) {
			# Unable to establish a secure connection with the Afterpay API endpoint.
			# Likely a TLS or network error.
			# Show the error throughout the admin until corrected.
			$error_codes_arr = $response->get_error_codes();
			$error = new \StdClass;
			$error->timestamp = date('Y-m-d H:i:s');
			$error->message = 'The Afterpay Gateway for WooCommerce plugin cannot communicate with the Afterpay API.';
			if (count($error_codes_arr) > 0) {
				$error->message .= ' Error code(s) returned: "' . implode('", "', $error_codes_arr) . '".';
			}
			$this->store_api_error($error);
			if (is_admin()) {
				# Admin has just saved the settings. The "admin_notices" action has already triggered, so we need
				# to implement WooCommerce's secondary notice reporting function instead.
				WC_Admin_Settings::add_error(__( $error->message, 'woo_afterpay' ));
			}
		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_array($body)) {
				$this->clear_api_error();
				return $body;
			} elseif (is_object($body) && property_exists($body, 'errorCode')) {
				# Log the error details.
				WC_Gateway_Afterpay::log("API ERROR #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");

				# Also display a simplified version of the error to the admin.
				$error = new \StdClass;
				$error->timestamp = date('Y-m-d H:i:s');
				$error->code = $body->httpStatusCode;
				if ($error->code == 401) {
					$error->message = 'Your Afterpay API credentials are incorrect.';
				} else {
					$error->message = 'The Afterpay Gateway for WooCommerce plugin cannot communicate with the Afterpay API.';
				}
				if (property_exists($body, 'errorId') && $body->errorId) {
					$error->id = $body->errorId;
				}
				$this->store_api_error($error);
				if (is_admin()) {
					# Admin has just saved the settings. The "admin_notices" action has already triggered, so we need
					# to implement WooCommerce's secondary notice reporting function instead.
					$text = __( "Afterpay API Error #{$error->code}: {$error->message}", 'woo_afterpay' );
					if (property_exists($error, 'id') && $error->id) {
						$text .= __( " (Error ID: {$error->id})", 'woo_afterpay' );
					}
					WC_Admin_Settings::add_error($text);
				}
			}
		} elseif ($response === false) {
			# Response is false
		}

		return false;
	}

	/**
	 * Get the configuration (valid payment types and limits) for this merchant.
	 *
	 * Note:	This is only for API v1.
	 *
	 * @since	2.0.0
	 * @uses	self::get_from_api()
	 * @uses	self::store_api_error()
	 * @uses	WC_Admin_Settings::add_error()
	 * @uses	self::clear_api_error()
	 * @uses	WC_Gateway_Afterpay::log()
	 * @return	array|false					The list of available payment types, or false on error.
	 */
	public function get_configuration() {
		$response = $this->get_from_api($this->configuration_url);

		if (is_wp_error( $response )) {
			# Unable to establish a secure connection with the Afterpay API endpoint.
			# Likely a TLS or network error.
			# Show the error throughout the admin until corrected.
			$error_codes_arr = $response->get_error_codes();
			$error = new \StdClass;
			$error->timestamp = date('Y-m-d H:i:s');
			$error->message = 'The Afterpay Gateway for WooCommerce plugin cannot communicate with the Afterpay API.';
			if (count($error_codes_arr) > 0) {
				$error->message .= ' Error code(s) returned: "' . implode('", "', $error_codes_arr) . '".';
			}
			$this->store_api_error($error);
			if (is_admin()) {
				# Admin has just saved the settings. The "admin_notices" action has already triggered, so we need
				# to implement WooCommerce's secondary notice reporting function instead.
				WC_Admin_Settings::add_error(__( $error->message, 'woo_afterpay' ));
			}
		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_array($body)) {
				$this->clear_api_error();
				return $body;
			} elseif (is_object($body) && property_exists($body, 'errorCode')) {
				# Log the error details.
				WC_Gateway_Afterpay::log("API ERROR #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");

				# Also display a simplified version of the error to the admin.
				$error = new \StdClass;
				$error->timestamp = date('Y-m-d H:i:s');
				$error->code = $body->httpStatusCode;
				if ($error->code == 401) {
					$error->message = 'Your Afterpay API credentials are incorrect.';
				} else {
					$error->message = 'The Afterpay Gateway for WooCommerce plugin cannot communicate with the Afterpay API.';
				}
				if (property_exists($body, 'errorId') && $body->errorId) {
					$error->id = $body->errorId;
				}
				$this->store_api_error($error);
				if (is_admin()) {
					# Admin has just saved the settings. The "admin_notices" action has already triggered, so we need
					# to implement WooCommerce's secondary notice reporting function instead.
					$text = __( "Afterpay API Error #{$error->code}: {$error->message}", 'woo_afterpay' );
					if (property_exists($error, 'id') && $error->id) {
						$text .= __( " (Error ID: {$error->id})", 'woo_afterpay' );
					}
					WC_Admin_Settings::add_error($text);
				}
				# Return Error Code only if 401 - to reset the Payment Limits
				if ($error->code = 401) {
					return $error->code;
				}
			}
		} elseif ($response === false) {
			# Response is false
		}

		return false;
	}

	/**
	 * Get the valid payment types available from Afterpay for this amount.
	 *
	 * Note:	This is only for API v0.
	 *
	 * @since	2.0.0
	 * @param	float	$order_total	Order Total Amount
	 * @uses	self::build_money_object()
	 * @uses	self::post_to_api()
	 * @return	array|false					The list of available types, or false on error.
	 */
	public function get_payment_types_for_amount($order_total) {
		$response = $this->post_to_api($this->payment_types_url, array(
			'orderAmount' => $this->build_money_object($order_total)
		));

		if (is_wp_error( $response )) {
			# WP Error Detected
		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_array($body)) {
				return $body;
			} elseif (is_object($body) && property_exists($body, 'errorCode')) {
				# Log the error details.
				WC_Gateway_Afterpay::log("API ERROR #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");
			}
		} elseif ($response === false) {
			# Response is false
		}

		return false;
	}

	/**
	 * Request an order token from Afterpay.
	 *
	 * Note:	This is only for API v1 in Compatibility Mode.
	 *
	 * @since	2.1.0
	 * @param	WC_Order|null				$order		An instance of WC_Order or null.
	 * @uses	self::build_money_object()
	 * @uses	WC_Order::get_address_prop()			Available in WooCommerce core since 3.0.0.
	 * @return	string|bool								Returns the token string, or false if no order token
	 *													could be generated.
	 * @used-by	WC_Gateway_Afterpay::process_payment()
	 */
	public function get_order_token_for_wc_order_in_v1($order = null) {
		if (!($order instanceof WC_Order)) {
			return false;
		}

		$data = array(
			'totalAmount' => $this->build_money_object($order->get_total())
		);

		$data['consumer'] = array(
			'phoneNumber' => $order->get_billing_phone(),
			'givenNames' => $order->get_billing_first_name(),
			'surname' => $order->get_billing_last_name(),
			'email' => $order->get_billing_email()
		);
		$data['billing'] = array(
			'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'line1' => $order->get_billing_address_1(),
			'line2' => $order->get_billing_address_2(),
			'suburb' => $order->get_billing_city(),
			'state' => $order->get_billing_state(),
			'postcode' => $order->get_billing_postcode(),
			'countryCode' => $order->get_billing_country(),
			'phoneNumber' => $order->get_billing_phone()
		);
		if ($order->needs_shipping_address()) {
			$data['shipping'] = array(
				'name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
				'line1' => $order->get_shipping_address_1(),
				'line2' => $order->get_shipping_address_2(),
				'suburb' => $order->get_shipping_city(),
				'state' => $order->get_shipping_state(),
				'postcode' => $order->get_shipping_postcode(),
				'countryCode' => $order->get_shipping_country()
			);
		}
		/**
		 * @todo Retrieve courier information from WC version 3.0.0+ and send to Afterpay
		 */
		//$data['courier'] = array();
		$data['merchant'] = array(
			'redirectConfirmUrl' => WC()->api_request_url( 'WC_Gateway_Afterpay' ),
			'redirectCancelUrl' => $order->get_checkout_payment_url()
		);
		$data['merchantReference'] = $order->get_id();
		$data['taxAmount'] = $this->build_money_object($order->get_total_tax());
		$data['shippingAmount'] = $this->build_money_object((float)$order->get_shipping_total() + (float)$order->get_shipping_tax());

		$order_items = $order->get_items();

		if (count($order_items)) {
			$data['items'] = array();
			foreach ($order_items as $item) {
				$product = $item->get_product();
				if (!$product) {
					continue;
				}
				$data['items'][] = array(
					'name' => $product->get_name(),
					'sku' => $product->get_sku(),
					'quantity' => $item->get_quantity(),
					'price' => $this->build_money_object( wc_get_price_including_tax($product) )
				);
			}
		}

		if (method_exists($order, 'get_coupons')) {
			$coupons = $order->get_coupons();
		} else {
			$coupons = $order->get_items('coupon'); // fallback for pre WC 3.7
		}

		if ($coupons) {
			$data['discounts'] = array();
			foreach ($coupons as $coupon) {
				$data['discounts'][] = array(
					'displayName' => $coupon->get_name(),
					'amount' => $this->build_money_object((float)$coupon->get_discount() + (float)$coupon->get_discount_tax())
				);
			}
		}

		$response = $this->post_to_api($this->orders_url, $data);

		if (is_object($response) && !is_wp_error($response)) {
			$body = $response->body;

			if (is_object($body)) {
				if (property_exists($body, 'token') && is_string($body->token) && strlen($body->token) > 0) {
					$api_response_str = "Afterpay order token: {$body->token}";

					WC_Gateway_Afterpay::log($api_response_str);
					$order->add_order_note( __( $api_response_str, 'woo_afterpay' ) );

					return $body->token;
				} elseif (property_exists($body, 'errorCode')) {
					$api_response_str = "API Error #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})";

					WC_Gateway_Afterpay::log($api_response_str);
					$order->add_order_note( __( $api_response_str, 'woo_afterpay' ) );

					wc_add_notice( __( "Sorry, there was a problem preparing your payment. (Error #{$body->httpStatusCode}: {$body->message})", 'woo_afterpay' ), 'error' );
				}
			}
		}

		return false;
	}

	/**
	 * Request an order token from Afterpay for express checkout.
	 *
	 * @since	3.1.0
	 * @return	string|bool								Returns the token string, or false if no order token
	 *													could be generated.
	 */
	public function get_token_for_express_checkout() {
		$totals = WC()->cart->get_totals();

		$data = array(
			'mode' => 'express',
			'merchant' => array(
				'redirectConfirmUrl' => get_home_url(),
				'redirectCancelUrl' => wc_get_cart_url()
            ),
            'items' => array(),
            'discounts' => array(),
			'totalAmount' => $this->build_money_object($totals['cart_contents_tax'] + (float)$totals['cart_contents_total']), // includes coupon discounts, but not shipping
		);

		$items = WC()->cart->get_cart();
		foreach ($items as $item) {
			if ($item['variation_id']) {
				$product = wc_get_product($item['variation_id']);
			} else {
				$product = wc_get_product($item['product_id']);
			}
			$data['items'][] = array(
				'name' => $product->get_name(),
				'sku' => $product->get_sku(),
				'quantity' => $item['quantity'],
				'price' => $this->build_money_object(($item['line_subtotal'] + $item['line_subtotal_tax']) / $item['quantity'])
			);
		}

		$coupons = WC()->cart->get_applied_coupons();
		foreach ($coupons as $coupon_code) {
			$data['discounts'][] = array(
				'displayName' => $coupon_code,
				'amount' => $this->build_money_object(WC()->cart->get_coupon_discount_amount($coupon_code, false))
			);
		}

		$response = $this->post_to_api($this->orders_url, $data);

		if (is_object($response) && !is_wp_error($response)) {
			$body = $response->body;
			if (is_object($body)) {
				if (property_exists($body, 'token') && is_string($body->token) && strlen($body->token) > 0) {
					WC_Gateway_Afterpay::log("Afterpay express order token: {$body->token}");

					return $body->token;
				} elseif (property_exists($body, 'errorCode')) {
					wc_add_notice( __( "Something went wrong, please try again later.", 'woo_afterpay' ), 'error' );
					throw new Exception("API Error #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})", 3);
				}
			}
		}
		// All other failed scenarios
		wc_add_notice( __( "Something went wrong, please try again later.", 'woo_afterpay' ), 'error' );
		throw new Exception("Unable to create an order token for express checkout", 3);
	}

	/**
	 * Post a direct payment capture request for a given token in compatibility mode.
	 *
	 * Note:	This is only for API v1 in Compatibility Mode.
	 *
	 * @since	2.1.0
	 * @param	string			$token							The token that Afterpay gave us for the order.
	 * @param	string			$merchantReference				The new WC order number (not necessarily the post id) if updated since token creation.
	 * @return	StdClass|false									Either an object with id and status of "APPROVED"
	 *															or "DECLINED", or false.
	 * @used-by	WC_Gateway_Afterpay::on_redirect_confirm_url
	 */
	public function direct_payment_capture_compatibility_mode($token, $merchantReference = '', $amount = null) {
		$data = array(
			'token' => $token
		);
		if (!empty($merchantReference)) {
			$data['merchantReference'] = $merchantReference;
		}

		if($amount) {
			$data['amount'] = $amount;
		}

		$response = $this->post_to_api($this->direct_payment_capture_url, $data);

		if (is_wp_error( $response )) {
			# A WP Error is encountered
		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_object($body)) {
				if (property_exists($body, 'id') && property_exists($body, 'status')) {
					# Note: `$body->status` can only be "APPROVED".

					# Log the response.
					WC_Gateway_Afterpay::log("PAYMENT {$body->status} (Afterpay Order ID: {$body->id})");

					# Return the id and status in response.

					$response_obj = new \StdClass;
					$response_obj->id = $body->id;
					$response_obj->status = $body->status;

					return $response_obj;
				} elseif (property_exists($body, 'httpStatusCode') && $body->httpStatusCode == 402) {
					# Note: If the payment is declined, the API will throw a 402 error instead of a Payment object with a status of "DECLINED".

					# Log the decline.
					WC_Gateway_Afterpay::log("API Error #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");

					# Use the token to find out the Afterpay Order ID.

					$get_payment_response = $this->get_from_api("{$this->payments_url}/token:{$token}");

					if (!is_wp_error( $get_payment_response ) && is_object($get_payment_response)) {
						$get_payment_response_body = $get_payment_response->body;
					} else {
						$get_payment_response_body = false;
					}

					# Return the id and status in response.

					$response_obj = new \StdClass;
					$response_obj->id = 0;
					$response_obj->status = 'DECLINED';
					if (is_object($get_payment_response_body)) {
						if (property_exists($get_payment_response_body, 'id')) {
							$response_obj->id = $get_payment_response_body->id;
						}
						if (property_exists($get_payment_response_body, 'status')) {
							$response_obj->status = $get_payment_response_body->status;
						}
					}

					return $response_obj;
				} elseif (property_exists($body, 'errorCode')) {
					# Log the error details.
					WC_Gateway_Afterpay::log("API Error #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");
				} else {
					# Empty Body
				}
			} else {
				# Body is not an object
			}
		} elseif ($response === false) {
			# Response is not an object
		} else {
			# Response is not false, not an object and no WP Error
		}

		return false;
	}

	/**
	 * Post a refund request for a given payment.
	 *
	 * @since	2.0.0
	 * @param	WC_Order	$order	The WooCommerce order that this refund relates to.
	 * @param	float		$amount	The amount to be refunded.
	 * @uses	self::build_money_object()
	 * @uses	self::post_to_api()
	 * @uses	property_exists()
	 * @uses	is_object()
	 * @uses	is_wp_error()
	 * @used-by	WC_Gateway_Afterpay::process_refund()
	 */
	public function create_refund($order, $amount) {
		$afterpay_order_id = $order->get_transaction_id();

		if (method_exists($order, 'get_currency')) {
		    $currency = $order->get_currency();
		} else {
		    $currency = $order->get_order_currency();
		}

		$response = $this->post_to_api("{$this->payments_url}/{$afterpay_order_id}/refund", array(
			'amount' => $this->build_money_object($amount, $currency)
		));
		$refund_id_property = 'refundId';

		if (is_wp_error($response) || $response === false) {

			# WP Error
			$response = print_r($response, true);
			WC_Gateway_Afterpay::log("API ERROR. WP Error: {$response}");

			return false;

		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_object($body)) {
				if (property_exists($body, $refund_id_property) && $body->{$refund_id_property}) {
					# Log the ID.
					WC_Gateway_Afterpay::log("Refund succesful. Refund ID: {$body->{$refund_id_property}}.");

					# Return true.
					return true;
				} elseif (property_exists($body, 'errorCode')) {
					# Log the error details.
					WC_Gateway_Afterpay::log("API ERROR #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");
				} else {
					# Missing required Body Response
					$response = print_r($response, true);
					WC_Gateway_Afterpay::log("API ERROR. Missing required Body Response: {$response}");
				}
			} else {
				# Response body is not an object
				# Log the unexpected response
				$response = print_r($response, true);
				WC_Gateway_Afterpay::log("API ERROR. Response is not an Object: {$response}");
			}
		}

		return false;
	}

	/**
	 * Get the Afterpay Order Details
	 * Note:	This is only for API v1.
	 *
	 * @since	2.1.0
	 * @param	string			$token					The Afterpay order token.
	 * @uses	self::get_from_api()
	 * @return	StdClass|false
	 */
	public function get_order_by_v1_token($token) {
		$endpoint_url = "{$this->orders_url}/{$token}";

		$response = $this->get_from_api($endpoint_url);

		if (is_wp_error( $response )) {
			# WP Error Detected
		} elseif (is_object($response)) {
			$body = $response->body;

			if (is_object($body)) {
				if (property_exists($body, 'errorCode')) {
					# Log the error details.
					WC_Gateway_Afterpay::log("API ERROR #{$body->httpStatusCode} \"{$body->errorCode}\": {$body->message} (Error ID: {$body->errorId})");
				}

				return $body;
			} else {
				# Body is not an Object
			}
		} elseif ($response === false) {
			# Response is not an Object
		}

		return false;
	}
}
