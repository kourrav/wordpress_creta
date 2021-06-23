<?php
/**
 * This is the Afterpay - WooCommerce Payment Gateway Class.
 */
if (!class_exists('WC_Gateway_Afterpay')) {
	class WC_Gateway_Afterpay extends WC_Payment_Gateway
	{
		/**
		 * Private variables.
		 *
		 * @var		string	$include_path			Path to where this class's includes are located. Populated in the class constructor.
		 * @var		array	$environments			Keyed array containing the name and API/web URLs for each environment. Populated in the
		 *											class constructor by parsing the values in "environments.ini".
		 * @var		string	$token					The token to render on the preauth page.
		 * @var		array 	$assets					Static text content used for front end presentation based on currency/region.
		 * @var		array 	$express_base_error_config		Basic error config for express
		 */
		private $include_path, $environments, $token, $assets, $express_base_error_config;

		/**
		 * Protected static variables.
		 *
		 * @var		WC_Gateway_Afterpay	$instance		A static reference to a singleton instance of this class.
		 */
		protected static $instance = null;

		/**
		 * Public static variables.
		 *
		 * @var		WC_Logger		$log			An instance of the WC_Logger class.
		 */
		public static $log = false;

		/**
		 * Class constructor. Called when an object of this class is instantiated.
		 *
		 * @since	2.0.0
		 * @uses	plugin_basename()					Available as part of the WordPress core since 1.5.
		 * @uses	WC_Payment_Gateway::init_settings()	If the user has not yet saved their settings, it will extract the
		 *												default values from $this->form_fields defined in an ancestral class
		 *												and overridden below.
		 */
		public function __construct() {
			$this->include_path			= dirname( __FILE__ ) . '/WC_Gateway_Afterpay';
			$this->environments 		= include "{$this->include_path}/environments.php";
			$this->assets 				= include "{$this->include_path}/assets.php";

			$this->id					= 'afterpay';
			$this->has_fields        	= false;
			$this->description			= __( 'Credit cards accepted: Visa, Mastercard', 'woo_afterpay' );
			$this->method_title			= __( 'Afterpay', 'woo_afterpay' );
			$this->method_description	= __( 'Use Afterpay as a credit card processor for WooCommerce.', 'woo_afterpay' );
			//$this->icon; # Note: This URL is ignored; the WC_Gateway_Afterpay::filter_woocommerce_gateway_icon() method fires on the "woocommerce_gateway_icon" Filter hook and generates a complete HTML IMG tag.
			$this->supports				= array('products', 'refunds');
			$this->express_base_error_config = array(
				'log' => false,
				'redirect_url' => false
			);

			$this->init_form_fields();
			$this->init_settings();
			$this->refresh_cached_configuration();

			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = 'no';
			}
		}

		/**
		 * This is triggered when customers confirm payment and return from the gateway
		 * Note:	Hooked onto the "woocommerce_api_wc_gateway_afterpay" action.
		 * @since	3.0.0
		 */
		public function capture_payment() {
			if (!empty($_GET) && !empty($_GET['orderToken']) &&
				isset($_GET['status']) && 'SUCCESS' === $_GET['status']
			) {
				$order = false;
				$merchant = new Afterpay_Plugin_Merchant;

				$afterpay_order = $merchant->get_order_by_v1_token($_GET['orderToken']);
				if (is_object($afterpay_order) && !property_exists($afterpay_order, 'errorCode')) {
					/**
					 * Temporary conditional for consumers caught mid update from v3.0.2
					 *
					 * @since 3.1.0 custom not used
					 */
					if (isset($afterpay_order->custom->orderId)) {
						$order_id = $afterpay_order->custom->orderId;
					} else {
						$order_id = $afterpay_order->merchantReference;
					}

					$order = wc_get_order($order_id);
				}

				if (!$order || $order->is_paid() || $order->get_payment_method() != $this->id) {
					wp_die( 'Could not get order details for token: ' . $_GET['orderToken'], 'Afterpay', array( 'response' => 500 ) );
				}

				$order_number = $order->get_order_number();
				if ($order_number != $order_id) {
					self::log("Updating merchantReference from {$order_id} to {$order_number} for token: " . $_GET['orderToken']);
				}

				$response = $merchant->direct_payment_capture_compatibility_mode($_GET['orderToken'], $order_number);

				if (is_object($response)) {
					if ($response->status == 'APPROVED') {
						self::log("Payment APPROVED for WooCommerce Order #{$order_number} (Afterpay Order #{$response->id}).");

						$order->add_order_note( sprintf(__( 'Payment approved. Afterpay Order ID: %s.', 'woo_afterpay' ), $response->id) );
						$order->payment_complete($response->id);

						if (wp_redirect( $this->get_return_url($order) )) {
							exit;
						}
					} elseif ($response->status == 'DECLINED') {
						self::log("Payment DECLINED for WooCommerce Order #{$order_number} (Afterpay Order #{$response->id}).");

						$order->update_status( 'failed', sprintf(__( 'Payment declined. Afterpay Order ID: %s.', 'woo_afterpay' ), $response->id) );
						wc_add_notice( sprintf(__( 'Your payment was declined for Afterpay Order #%s. Please try again. For more information, please contact the Afterpay Customer Service team on '.$this->assets['cs_number'].'.', 'woo_afterpay' ), $response->id), 'error' );

						if (wp_redirect( $order->get_checkout_payment_url() )) {
							exit;
						}
					}
				} else {
					self::log("Updating status of WooCommerce Order #{$order_number} to \"Failed\", because response is not an object.");

					$order->update_status( 'failed', __( 'Afterpay payment failed.', 'woo_afterpay' ) );

					wc_add_notice( __( 'Payment failed. Please try again.', 'woo_afterpay' ), 'error' );
					if (wp_redirect( $order->get_checkout_payment_url() )) {
						exit;
					}
				}
			}
			wp_die( 'Invalid request to Afterpay callback', 'Afterpay', array( 'response' => 500 ) );
		}

		/**
		 * Initialise Gateway Settings Form Fields.
		 */
		public function init_form_fields() {
			$this->form_fields = include "{$this->include_path}/form_fields.php";
		}

		/**
		 * Refresh cached configuration. This method updates the properties of the class instance.
		 * Called from the constructor and after settings are saved. As an extension of WC_Payment_Gateway,
		 * `$this->settings` is automatically refreshed when settings are saved, but our custom properties
		 * are not. So this method is attached to a WooCommerce hook to ensure properties are up to date
		 * when the cron jobs run.
		 *
		 * Note:	Hooked onto the "woocommerce_update_options_payment_gateways_afterpay" Action.
		 *
		 * @since	2.1.0
		 */
		public function refresh_cached_configuration() {
			if (array_key_exists('title', $this->settings)) {
				$this->title = $this->settings['title'];
			}
		}

		/**
		 * Logging method. Using this to log a string will store it in a file that is accessible
		 * from "WooCommerce > System Status > Logs" in the WordPress admin. No FTP access required.
		 *
		 * @param 	string	$message	The message to log.
		 * @uses	WC_Logger::add()
		 */
		public static function log($message) {
			if (empty(self::$log)) {
				self::$log = new WC_Logger;
			}
			if (is_array($message)) {
				/**
				 * @since 2.1.0
				 * Properly expand Arrays in logs.
				 */
				$message = print_r($message, true);
			} elseif(is_object($message)) {
				/**
				 * @since 2.1.0
				 * Properly expand Objects in logs.
				 *
				 * Only use the Output Buffer if it's not currently active,
				 * or if it's empty.
				 *
				 * Note:	If the Output Buffer is active but empty, we write to it,
				 * 			read from it, then discard the contents while leaving it active.
				 *
				 * Otherwise, if $message is an Object, it will be logged as, for example:
				 * (foo Object)
				 */
				$ob_get_length = ob_get_length();
				if (!$ob_get_length) {
					if ($ob_get_length === false) {
						ob_start();
					}
					var_dump($message);
					$message = ob_get_contents();
					if ($ob_get_length === false) {
						ob_end_clean();
					} else {
						ob_clean();
					}
				} else {
					$message = '(' . get_class($message) . ' Object)';
				}
			}
			self::$log->add( 'afterpay', $message );
		}

		/**
		 * Instantiate the class if no instance exists. Return the instance.
		 *
		 * @since	2.0.0
		 * @return	WC_Gateway_Afterpay
		 */
		public static function getInstance()
		{
			if (is_null(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Is the gateway configured? This method returns true if any of the credentials fields are not empty.
		 *
		 * @since	2.0.0
		 * @return	bool
		 * @used-by	self::render_admin_notices()
		 */
		private function is_configured() {
			if (!empty($this->settings['prod-id']) ||
				!empty($this->settings['prod-secret-key']) ||
				!empty($this->settings['test-id']) ||
				!empty($this->settings['test-secret-key']))
			{
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Add the Afterpay gateway to WooCommerce.
		 *
		 * Note:	Hooked onto the "woocommerce_payment_gateways" Filter.
		 *
		 * @since	2.0.0
		 * @see		AfterpayPlugin::__construct()	For hook attachment.
		 * @param	array	$methods				Array of Payment Gateways.
		 * @return	array							Array of Payment Gateways, with Afterpay added.
		 **/
		public function add_afterpay_gateway($methods) {
			$methods[] = 'WC_Gateway_Afterpay';
			return $methods;
		}

		/**
		 * Check if the gateway is available for use.
		 *
		 * @return bool
		 */
		public function is_available() {
			$is_available = ( 'yes' === $this->enabled );

			$total = $this->get_order_total();
			$limit_min = floatval( $this->settings['pay-over-time-limit-min'] );
			$limit_max = floatval( $this->settings['pay-over-time-limit-max'] );

			if ($total < $limit_min ||
				$total > $limit_max ||
				$total == 0)
			{
				$is_available = false;
			}

			return $is_available;
		}

		/**
		 * Display Afterpay Assets on Normal Products
		 * Note:	Hooked onto the "woocommerce_get_price_html" Filter.
		 *
		 * @since	2.0.0
		 * @see		Afterpay_Plugin::__construct()	For hook attachment.
		 * @param 	float $price
		 * @param 	WC_Product $product
		 * @uses	self::print_info_for_listed_products()
		 * @return	string
		 */
		function filter_woocommerce_get_price_html($price, $product) {
			if (is_object($product) && $product instanceof WC_Product_Variation) {
				ob_start();
				$this->print_info_for_listed_products($product);
				$afterpay_html = ob_get_clean();

				return $price . $afterpay_html;
			}
			return $price;
		}

		/**
		 * The WC_Payment_Gateway::$icon property only accepts a string for the image URL. Since we want
		 * to support high pixel density screens and specifically define the width and height attributes,
		 * this method attaches to a Filter hook so we can build our own HTML markup for the IMG tag.
		 *
		 * Note:	Hooked onto the "woocommerce_gateway_icon" Filter.
		 *
		 * @since	2.0.0
		 * @see		Afterpay_Plugin::__construct()	For hook attachment.
		 * @param	string 	$icon_html		Icon HTML
		 * @param	string 	$gateway_id		Payment Gateway ID
		 * @return	string
		 */
		public function filter_woocommerce_gateway_icon($icon_html, $gateway_id) {
			if ($gateway_id != 'afterpay') {
				return $icon_html;
			}

			$static_url = $this->get_static_url();

			ob_start();

			?><img src="<?php echo $static_url; ?>integration/checkout/logo-afterpay-colour-120x25.png" srcset="<?php echo $static_url; ?>integration/checkout/logo-afterpay-colour-120x25.png 1x, <?php echo $static_url; ?>integration/checkout/logo-afterpay-colour-120x25@2x.png 2x, <?php echo $static_url; ?>integration/checkout/logo-afterpay-colour-120x25@3x.png 3x" width="120" height="25" alt="Afterpay" /><?php

			return ob_get_clean();
		}

		/**
		 * Render admin notices if applicable. This will print an error on every page of the admin if the cron failed to
		 * authenticate on its last attempt.
		 *
		 * Note:	Hooked onto the "admin_notices" Action.
		 * Note:	This runs BEFORE WooCommerce fires its "woocommerce_update_options_payment_gateways_<gateway_id>" actions.
		 *
		 * @since	2.0.0
		 * @uses	get_transient()			Available in WordPress core since 2.8.0
		 * @uses	delete_transient()		Available in WordPress core since 2.8.0
		 * @uses	admin_url()				Available in WordPress core since 2.6.0
		 * @uses	delete_option()
		 * @uses	self::is_configured()
		 */
		public function render_admin_notices() {
			/**
			 * Also change the activation message to include a link to the plugin settings.
			 *
			 * Note:	We didn't add the "is-dismissible" class here because we continually show another
			 *			message similar to this until the API credentials are entered.
			 *
			 * @see		./wp-admin/plugins.php	For the markup that this replaces.
			 * @uses	get_transient()			Available in WordPress core since 2.8.0
			 * @uses	delete_transient()		Available in WordPress core since 2.8.0
			 */
			if (function_exists('get_transient') && function_exists('delete_transient')) {
				if (get_transient( 'afterpay-admin-activation-notice' )) {
					?>
					<div class="updated notice">
						<p><?php _e( 'Plugin <strong>activated</strong>.' ) ?></p>
						<p><?php _e( 'Thank you for choosing Afterpay.', 'woo_afterpay' ); ?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=afterpay' ); ?>"><?php _e( 'Configure Settings.', 'woo_afterpay' ); ?></a></p>
						<p><?php _e( 'Don&rsquo;t have an Afterpay Merchant account yet?', 'woo_afterpay' ); ?> <a href="<?php echo $this->assets['retailer_url'] ?>" target="_blank"><?php _e( 'Apply online today!', 'woo_afterpay' ); ?></a></p>
					</div>
					<?php
					if (array_key_exists('activate', $_GET) && $_GET['activate'] == 'true') {
						unset($_GET['activate']); # Prevent the default "Plugin *activated*." notice.
					}
					delete_transient( 'afterpay-admin-activation-notice' );
					# No need to decide whether to render any API errors. We've only just activated the plugin.
					return;
				}
			}

			if (array_key_exists('woocommerce_afterpay_enabled', $_POST)) {
				# Since this runs before we handle the POST, we can clear any stored error here.
				delete_option( 'woocommerce_afterpay_api_error' );

				# If we're posting changes to the Afterpay settings, don't pull anything out of the database just yet.
				# This runs before the POST gets handled by WooCommerce, so we can wait until later.
				# If the updated settings fail, that will trigger its own error later.
				return;
			}

			$show_link = true;
			if (array_key_exists('page', $_GET) && array_key_exists('tab', $_GET) && array_key_exists('section', $_GET)) {
				if ($_GET['page'] == 'wc-settings' && $_GET['tab'] == 'checkout' && $_GET['section'] == 'afterpay') {
					# We're already on the Afterpay gateway's settings page. No need for the circular link.
					$show_link = false;
				}
			}

			$error = get_option( 'woocommerce_afterpay_api_error' );
			if (is_object($error) && $this->settings['enabled'] == 'yes') {
				?>
				<div class="error notice">
					<p>
						<strong><?php _e( "Afterpay API Error #{$error->code}:", 'woo_afterpay' ); ?></strong>
						<?php _e( $error->message, 'woo_afterpay' ); ?>
						<?php if (property_exists($error, 'id') && $error->id): ?>
							<em><?php _e( "(Error ID: {$error->id})", 'woo_afterpay' ); ?></em>
						<?php endif; ?>
						<?php if ($show_link): ?>
							<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=afterpay' ); ?>"><?php _e( 'Please check your Afterpay Merchant settings here.', 'woo_afterpay' ); ?></a>
						<?php endif; ?>
					</p>
				</div>
				<?php
				return;
			}

			# Also include a link to the plugin settings if they haven't been saved yet,
			# unless they have unchecked the Enabled checkbox in the settings.
			if (!$this->is_configured() && $this->settings['enabled'] == 'yes' && $show_link) {
				?>
				<div class="updated notice">
					<p><?php _e( 'Thank you for choosing Afterpay.', 'woo_afterpay' ); ?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=afterpay' ); ?>"><?php _e( 'Configure Settings.', 'woo_afterpay' ); ?></a></p>
					<p><?php _e( 'Don&rsquo;t have an Afterpay Merchant account yet?', 'woo_afterpay' ); ?> <a href="<?php echo $this->assets['retailer_url'] ?>" target="_blank"><?php _e( 'Apply online today!', 'woo_afterpay' ); ?></a></p>
				</div>
				<?php
				return;
			}
			if(isset($this->settings['afterpay-plugin-version']) && $this->settings['afterpay-plugin-version'] != Afterpay_Plugin::$version){
					?>
					<div class='updated notice'>
					<p>Afterpay Gateway for WooCommerce has updated from <?=$this->settings['afterpay-plugin-version']?> to <?=Afterpay_Plugin::$version?>. Please review and re-save your settings <?php if ($show_link){ ?><a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=afterpay' ); ?>"><?php _e( 'here', 'woo_afterpay' ); ?></a><?php } else { _e( 'below', 'woo_afterpay' );} ?>.</p>
					</div>
					<?php
			}
			else if(!isset($this->settings['afterpay-plugin-version'])){
				?>
				<div class='updated notice'><p>Afterpay Gateway for WooCommerce has updated to version <?=Afterpay_Plugin::$version?>. Please review and re-save your settings <?php if ($show_link){ ?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=afterpay' ); ?>"><?php _e( 'here', 'woo_afterpay' ); ?></a><?php } else { _e( 'below', 'woo_afterpay' );} ?>.</p></div>
				<?php
			}

			if(!get_option('afterpay_rate_notice_dismiss') || (get_option('afterpay_rate_notice_dismiss') && get_option('afterpay_rate_notice_dismiss')!='yes')){
				if(get_option('afterpay_rating_notification_timestamp')){
					$changeDate = date_create(date("Y-m-d",get_option('afterpay_rating_notification_timestamp')));
					$dateDiff   = date_diff($changeDate,date_create());
					if($dateDiff->format("%a") >= 14){
					?>
					<div class="notice notice-warning afterpay-rating-notice">
						<a class="notice-dismiss afterpay-notice-dismiss" style="position:relative;float:right;padding:9px 0px 9px 9px 9px;text-decoration:none;"></a>
						<p><?php _e( 'What do you think of the Afterpay plugin? Share your thoughts and experience to help improve future plugin releases.', 'woo_afterpay' ); ?></p><p> <a href="https://wordpress.org/support/plugin/afterpay-gateway-for-woocommerce/reviews/" class="afterpay_rate_redirect afterpay-notice-dismiss button button-primary"><?php _e( 'Rate now', 'woo_afterpay' ); ?></a></p>
					</div>
					<?php
					}
				}
			}
		}

		/**
		 * Admin Panel Options. Overrides the method defined in the parent class.
		 *
		 * @since	2.0.0
		 * @see		WC_Payment_Gateway::admin_options()			For the method that this overrides.
		 * @uses	WC_Settings_API::generate_settings_html()
		 */
		public function admin_options() {
			?>
			<h3><?php _e( 'Afterpay Gateway', 'woo_afterpay' ); ?></h3>

			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
			<?php
		}

		/**
		 * Generate WYSIWYG input field. This is a pseudo-magic method, called for each form field with a type of "wysiwyg".
		 *
		 * @since	2.0.0
		 * @see		WC_Settings_API::generate_settings_html()	For where this method is called from.
		 * @param	mixed		$key
		 * @param	mixed		$data
		 * @uses	esc_attr()									Available in WordPress core since 2.8.0.
		 * @uses	wp_editor()									Available in WordPress core since 3.3.0.
		 * @return	string										The HTML for the table row containing the WYSIWYG input field.
		 */
		public function generate_wysiwyg_html($key, $data) {
			$html = '';

			$id = str_replace('-', '', $key);
			$class = array_key_exists('class', $data) ? $data['class'] : '';
			$css = array_key_exists('css', $data) ? ('<style>' . $data['css'] . '</style>') : '';
			$name = "{$this->plugin_id}{$this->id}_{$key}";
			$title = array_key_exists('title', $data) ? $data['title'] : '';
			$value = array_key_exists($key, $this->settings) ? esc_attr( $this->settings[$key] ) : '';
			$description = array_key_exists('description', $data) ? $data['description'] : '';

			ob_start();

			include "{$this->include_path}/wysiwyg.html.php";

			$html = ob_get_clean();

			return $html;
		}

		/**
		 * Get the current API URL based on our user settings. Defaults to the Sandbox URL.
		 *
		 * @since	2.0.0
		 * @return	string
		 * @uses 	get_option('woocommerce_currency')
		 * @used-by	Afterpay_Plugin_Merchant::__construct()
		 */
		public function get_api_url() {

			$currency = get_option('woocommerce_currency');
			$target_mode = 'api_url';

			if ($currency == "USD" || $currency == "CAD") {
				$target_mode = 'api_us_url';
			}

			$api_url = $this->environments[$this->settings['testmode']][$target_mode];

			if (empty($api_url)) {
				$api_url = $this->environments['sandbox'][$target_mode];
			}

			return $api_url;
		}

		/**
		 * Get the current web URL based on our user settings. Defaults to the Sandbox URL.
		 *
		 * @since	2.0.0
		 * @return	string
		 * @uses 	get_option('woocommerce_currency')
		 */
		public function get_web_url() {

			$currency = get_option('woocommerce_currency');
			$target_mode = 'web_url';

			if ($currency == "USD" || $currency == "CAD") {
				$target_mode = 'web_us_url';
			}

			$web_url = $this->environments[$this->settings['testmode']][$target_mode];

			if (empty($web_url)) {
				$web_url = $this->environments['sandbox'][$target_mode];
			}

			return $web_url;
		}

		/**
		 * Get the current static URL based on our user settings. Defaults to the Sandbox URL.
		 *
		 * @since	2.1.7
		 * @return	string
		 */
		public function get_static_url() {
			$static_url = $this->environments[$this->settings['testmode']]['static_url'];

			if (empty($static_url)) {
				$static_url = $this->environments['sandbox']['static_url'];
			}

			return $static_url;
		}

		/**
		 * Get the Merchant ID from our user settings. Uses the Sandbox account for all environments except Production.
		 *
		 * @since	2.0.0
		 * @return	string
		 * @used-by	Afterpay_Plugin_Merchant::get_authorization_header()
		 */
		public function get_merchant_id() {
			if ($this->settings['testmode'] == 'production') {
				return $this->settings['prod-id'];
			}
			return $this->settings['test-id'];
		}

		/**
		 * Get the Secret Key from our user settings. Uses the Sandbox account for all environments except Production.
		 *
		 * @since	2.0.0
		 * @return	string
		 * @used-by	Afterpay_Plugin_Merchant::get_authorization_header()
		 */
		public function get_secret_key() {
			if ($this->settings['testmode'] == 'production') {
				return $this->settings['prod-secret-key'];
			}
			return $this->settings['test-secret-key'];
		}

		/**
		 * Get API environment based on our user settings.
		 *
		 * @since 2.2.0
		 * @return string
		 */
		public function get_api_env() {
			return $this->settings['testmode'];
		}

		/**
		 * Get locale based on currency.
		 *
		 * @since 2.2.0
		 * @return string
		 */
		public function get_js_locale() {
			$locale_by_currency = array(
				'AUD' => 'en_AU',
				'CAD' => 'en_CA',
				'NZD' => 'en_NZ',
				'USD' => 'en_US',
			);
			$currency = get_option('woocommerce_currency');
			return $locale_by_currency[$currency];
		}

		/**
		 * Convert the global $post object to a WC_Product instance.
		 *
		 * @since	2.0.0
		 * @global	WP_Post	$post
		 * @uses	wc_get_product()	Available as part of the WooCommerce core plugin since 2.2.0.
		 *								Also see:	WC()->product_factory->get_product()
		 *								Also see:	WC_Product_Factory::get_product()
		 * @return	WC_Product
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		private function get_product_from_the_post() {
			global $post;

			if (function_exists('wc_get_product')) {
				$product = wc_get_product( $post->ID );
			} else {
				$product = new WC_Product( $post->ID );
			}

			return $product;
		}

		/**
		 * Is the given product supported by the Afterpay gateway?
		 *
		 * Note:	Some products may not be allowed to be purchased with Afterpay unless
		 *			combined with other products to lift the cart total above the merchant's
		 *			minimum. By default, this function will not check the merchant's
		 *			minimum. Set $alone to true to check if the product can be
		 *			purchased on its own.
		 *
		 * @since	2.0.0
		 * @param	WC_Product	$product									The product in question, in the form of a WC_Product object.
		 * @param	bool		$alone										Whether to view the product on its own.
		 *																	This affects whether the minimum setting is considered.
		 * @uses	WC_Product::get_type()									Possibly available as part of the WooCommerce core plugin since 2.6.0.
		 * @uses	WC_Product::get_price()									Possibly available as part of the WooCommerce core plugin since 2.6.0.
		 * @uses	apply_filters()											Available in WordPress core since 0.17.
		 * @return	bool													Whether or not the given product is eligible for Afterpay.
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		private function is_product_supported($product, $alone = false) {
			if (!isset($this->settings['enabled']) || $this->settings['enabled'] != 'yes') {
				return false;
			}

			if (!is_object($product)) {
				return false;
			}

			$product_type = $product->get_type();
			if (preg_match('/subscription/', $product_type)) {
				# Subscription products are not supported by Afterpay.
				return false;
			}

			# Allow other plugins to exclude Afterpay from products that would otherwise be supported.
			return (bool)apply_filters( 'afterpay_is_product_supported', true, $product, $alone );
		}

		/**
		 * Is the given product within Payment Limits?
		 *
		 *
		 * @since	2.0.0
		 * @param	WC_Product	$product									The product in question, in the form of a WC_Product object.
		 * @param	bool		$alone										Whether to view the product on its own.
		 *																	This affects whether the minimum setting is considered.
		 * @return	bool													Whether or not the given product is eligible for Afterpay.
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		private function is_product_within_limits($product, $alone = false) {

			$price= $this->get_product_final_price($product);

			/* Check for API Failure */
			if( $this->settings['pay-over-time-limit-min'] == 'N/A' && $this->settings['pay-over-time-limit-max'] == 'N/A' ) {
				return false;
			}

			if ( $price < 0.04 || $price > floatval( $this->settings['pay-over-time-limit-max'] ) ) {
				# Free items are not supported by Afterpay.
				# If the price exceeds the maximum for this merchant, the product is not supported.
				return false;
			}

			if ( $alone && $price < floatval( $this->settings['pay-over-time-limit-min'] ) ) {
				# If the product is viewed as being on its own and priced lower that the merchant's minimum, it will be considered as not supported.
				return false;
			}

			return true;
		}
		/**
		 * Get Minimum Child Product Price within the Afterpay Limit
		 *
		 *
		 * @since	2.1.2
		 * @param	$child_product_ids										The child product ids of the product.
		 *																	This affects whether the minimum setting is considered.
		 * @return	string													The minimum product variant price within limits.
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		private function get_child_product_price_within_limits($child_product_ids) {

			$min_child_product_price = NAN;

			foreach ($child_product_ids as $child_product_id) {
				$child_product = wc_get_product($child_product_id );

				$child_product_price= $this->get_product_final_price($child_product);

				if ($this->is_price_within_limits($child_product_price)) {
					if (is_nan($min_child_product_price) || $child_product_price < $min_child_product_price) {
						$min_child_product_price = $child_product_price;
					}
				}
			}
			return $min_child_product_price;
		}
		/**
		 * Is Price within the Afterpay Limit?
		 *
		 *
		 * @since	2.1.2
		 * @param	$amount													The price to be checked.
		 * @return	bool													Whether or not the given price is ithin the Afterpay Limits.
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		private function is_price_within_limits($amount) {

			/* Check for API Failure */

			if(($this->settings['pay-over-time-limit-min'] == 'N/A' && $this->settings['pay-over-time-limit-max'] == 'N/A')
				|| (empty($this->settings['pay-over-time-limit-min']) && empty($this->settings['pay-over-time-limit-max']))) {
				return false;
			}

			if ($amount >= 0.04 && $amount >= floatval($this->settings['pay-over-time-limit-min']) && $amount <= floatval($this->settings['pay-over-time-limit-max'])){
				return true;
			}
			else{
				return false;
			}
		}

		/**
		 * Check if this gateway is available in the user's country based on currency.
		 *
		 * @return bool
		 */
		public function is_valid_for_use() {
			return in_array(
				get_option('woocommerce_currency'),
				array( 'AUD', 'CAD', 'NZD', 'USD' ),
				true
			);
		}

		/**
		 * Process the HTML for the Afterpay Modal Window
		 *
		 * @since	2.0.0-rc3
		 * @param	string	$html
		 * @return	string
		 * @uses	get_option('woocommerce_currency')	determine website currency
		 * @used-by	process_and_print_afterpay_paragraph()
		 * @used-by	render_schedule_on_cart_page()
		 * @used-by	payment_fields()
		 */
		private function apply_modal_window($html) {
			$currency				=	get_option('woocommerce_currency');

			$modal_window_asset		=	"<span style='display:none' id='modal-window-currency' currency='" . $currency . "'></span>";

			return $html . $modal_window_asset;
		}

		/**
		 * Process the HTML from one of the rich text editors and output the converted string.
		 *
		 * @since	2.0.0
		 * @param	string				$html								The HTML with replace tags such as [AMOUNT].
		 * @param	string				$output_filter
		 * @param	WC_Product|null		$product							The product for which to print instalment info.
		 * @uses	self::get_product_from_the_post()
		 * @uses	self::is_product_supported()
		 * @uses	self::apply_modal_window()
		 * @uses	wc_get_price_including_tax()							Available as part of the WooCommerce core plugin since 3.0.0.
		 * @uses	WC_Abstract_Legacy_Product::get_price_including_tax()	Possibly available as part of the WooCommerce core plugin since 2.6.0. Deprecated in 3.0.0.
		 * @uses	WC_Product::get_price()									Possibly available as part of the WooCommerce core plugin since 2.6.0.
		 * @uses	self::display_price_html()
		 * @uses	apply_filters()											Available in WordPress core since 0.17.
		 * @used-by	self::print_info_for_product_detail_page()
		 * @used-by	self::print_info_for_listed_products()
		 */
		private function process_and_print_afterpay_paragraph($html, $output_filter, $product = null) {
			if (is_null($product)) {
				$product = $this->get_product_from_the_post();
			}

			/*Check if the currency is supported*/
			if(get_option('woocommerce_currency') != get_woocommerce_currency()){
				return;
			}
			if (!$this->is_product_supported($product, true)) {
				# Don't display anything on the product page if the product is not supported when purchased on its own.
				return;
			}

			if (!$product->get_price()){
				return;
			}

			if( ($this->settings['pay-over-time-limit-min'] == 'N/A' && $this->settings['pay-over-time-limit-max'] == 'N/A')
				|| empty($this->settings['pay-over-time-limit-min']) && empty($this->settings['pay-over-time-limit-max'])) {
				return;
			}

			$of_or_from = 'of';
			$from_price = NAN;
			$price = NAN;

			/**
			 * Note: See also: WC_Product_Variable::get_variation_price( $min_or_max = 'min', $include_taxes = false )
			 */
			 $child_product_ids=[];
			if ($product->has_child()){
				$parent_product=$product;
				$child_product_ids = $parent_product->get_children();
			}
            else if($product->get_type()=="variation" && !$this->is_product_within_limits($product, true)){
				$parent_product = wc_get_product($product->get_parent_id());
				$child_product_ids = $parent_product->get_children();
			}

			if (count($child_product_ids) > 1) {
				if ($parent_product->is_type('variable')) {
					$min_variable_price = $parent_product->get_variation_price('min', true);
					$max_variable_price = $parent_product->get_variation_price('max', true);

					if ($this->is_price_within_limits($min_variable_price)) {
						$from_price = $min_variable_price;
					}
					elseif (!is_nan($this->get_child_product_price_within_limits($child_product_ids))) {
						$from_price = $this->get_child_product_price_within_limits($child_product_ids);
					}

					if (!is_nan($from_price) && $from_price < $max_variable_price) {
						$of_or_from = 'from';
					}
				}
				elseif (!is_nan($this->get_child_product_price_within_limits($child_product_ids))) {
					$of_or_from = 'from';
					$from_price = $this->get_child_product_price_within_limits($child_product_ids);
				}
			}

			$show_outside_limit = (!isset($this->settings['show-outside-limit-on-product-page']) || $this->settings['show-outside-limit-on-product-page'] == 'yes');

			if (
				$output_filter == 'afterpay_html_on_individual_product_pages' &&
				!$this->is_product_within_limits($product, true)
			) {
				if ( is_nan($from_price) && $show_outside_limit ) {
					//Individual Product Pages fallback
					if ($this->settings['pay-over-time-limit-min'] != 0) {
						$fallback_asset	= $this->assets['fallback_asset'];
					} else {
						$fallback_asset = $this->assets['fallback_asset_2'];
					}
					$html = $fallback_asset;
					$html = str_replace(array(
						'[MIN_LIMIT]',
						'[MAX_LIMIT]'
					), array(
						$this->display_price_html( floatval( $this->settings['pay-over-time-limit-min'] ) ),
						$this->display_price_html( floatval( $this->settings['pay-over-time-limit-max'] ) )
					), $html);
				}
				elseif (!is_nan($from_price))
				{
					$amount = $this->display_price_html(round($from_price/4, 2));
					$html = str_replace(array(
						'[OF_OR_FROM]',
						'[AMOUNT]'
					), array(
						$of_or_from,
						$amount
					), $html);
				}
				else {
					return;
				}
			}
			elseif (
				$output_filter == 'afterpay_html_on_product_variants' &&
				!$this->is_product_within_limits($product, true) &&
				$show_outside_limit
			) {
				if (is_nan($price)) {
					$price= $this->get_product_final_price($product);
				}

				if ($this->settings['pay-over-time-limit-min'] != 0) {
					$fallback_asset = $this->assets['product_variant_fallback_asset'];
				} else {
					$fallback_asset	= $this->assets['product_variant_fallback_asset_2'];
				}

				$html = $fallback_asset;
				$html = str_replace(array(
					'[MIN_LIMIT]',
					'[MAX_LIMIT]'
				), array(
					$this->display_price_html( floatval( $this->settings['pay-over-time-limit-min'] ) ),
					$this->display_price_html( floatval( $this->settings['pay-over-time-limit-max'] ) )
				), $html);
			}
			elseif (
				$output_filter == 'afterpay_html_on_product_thumbnails' &&
				!is_nan($from_price)
			) {
				$amount = $this->display_price_html(round($from_price/4, 2));
				$html = str_replace(array(
					'[OF_OR_FROM]',
					'[AMOUNT]'
				), array(
					$of_or_from,
					$amount
				), $html);
			}
			else{
				if (is_nan($price)) {
					$price= $this->get_product_final_price($product);
				}
				if ($this->is_price_within_limits($price)) {
					$amount = $this->display_price_html( round($price / 4, 2) );
					$html = str_replace(array(
					'[OF_OR_FROM]',
					'[AMOUNT]'
					), array(
						$of_or_from,
						$amount
					), $html);
				}
				else{
					return;
				}

			}

			# Execute shortcodes on the string after running internal replacements,
			# but before applying filters and rendering.
			$html = do_shortcode( "<p class=\"afterpay-payment-info\">{$html}</p>" );

			# Add the Modal Window to the page
			# Website Admin have no access to the Modal Window codes for data integrity reasons
			$html = $this->apply_modal_window($html);


			# Allow other plugins to maniplulate or replace the HTML echoed by this funtion.
			echo apply_filters( $output_filter, $html, $product, $price );
		}

		/**
		 * Print a paragraph of Afterpay info onto the individual product pages if enabled and the product is valid.
		 *
		 * Note:	Hooked onto the "woocommerce_single_product_summary" Action.
		 *
		 * @since	2.0.0
		 * @see		Afterpay_Plugin::__construct()							For hook attachment.
		 * @param	WC_Product|null		$product							The product for which to print instalment info.
		 * @uses	self::process_and_print_afterpay_paragraph()
		 */
		public function print_info_for_product_detail_page($product = null) {
			if (!isset($this->settings['show-info-on-product-pages'])
				|| $this->settings['show-info-on-product-pages'] != 'yes'
				|| empty($this->settings['product-pages-info-text'])) {
				# Don't display anything on product pages unless the "Payment info on individual product pages"
				# box is ticked and there is a message to display.
				return;
			}

			$this->process_and_print_afterpay_paragraph($this->settings['product-pages-info-text'], 'afterpay_html_on_individual_product_pages', $product);
		}

		/**
		 * Print a paragraph of Afterpay info onto each product item in the shop loop if enabled and the product is valid.
		 *
		 * Note:	Hooked onto the "woocommerce_after_shop_loop_item_title" Action.
		 *
		 * @since	2.0.0
		 * @see		Afterpay_Plugin::__construct()							For hook attachment.
		 * @param	WC_Product|null		$product							The product for which to print instalment info.
		 * @uses	self::process_and_print_afterpay_paragraph()
		 * @uses 	is_single()
		 * @uses 	WC_Product::is_in_stock()
		 * @used-by	self::filter_woocommerce_get_price_html()
		 */
		public function print_info_for_listed_products($product = null) {
			# Product Pages

			# get the global wp_query to fetch the current product
			global $wp_query;

			# handle the Variant Product of this Product Single Page
			if (
				is_single()
				&& !empty($product)
				&& (
					(method_exists($product, 'get_parent_id') && $product->get_parent_id() == $wp_query->post->ID)
					|| (property_exists($product, 'parent_id') && $product->parent_id == $wp_query->post->ID)
				)
			) {
				if (
					isset($this->settings['show-info-on-product-variant'])
					&& $this->settings['show-info-on-product-variant'] == 'yes'
					&& $product->is_in_stock()
				) {
					$this->process_and_print_afterpay_paragraph($this->settings['product-variant-info-text'], 'afterpay_html_on_product_variants', $product);
				}
			}
			else {
				# Category Pages & Related Products
				if (isset($this->settings['show-info-on-category-pages'])
					&& $this->settings['show-info-on-category-pages'] == 'yes'
					&& !empty($this->settings['category-pages-info-text'])) {
					# Don't display anything on product items within the shop loop unless
					# the "Payment info on product listing pages" box is ticked
					# and there is a message to display.
					$this->process_and_print_afterpay_paragraph($this->settings['category-pages-info-text'], 'afterpay_html_on_product_thumbnails', $product);
				}

			}
		}

		/**
		 * Format float as currency.
		 *
		 * @since	2.0.0
		 * @param	float $price
		 * @return	string The formatted price HTML.
		 * @used-by	self::process_and_print_afterpay_paragraph()
		 * @used-by	self::render_schedule_on_cart_page()
		 */
		private function display_price_html($price) {
			if (function_exists('wc_price')) {
				return wc_price($price);
			} elseif (function_exists('woocommerce_price')) {
				return woocommerce_price($price);
			}
			return '$' . number_format($price, 2, '.', ',');
		}

		/**
		 * Instalment calculation.
		 *
		 * @since	2.0.0
		 * @see		PaymentScheduleManager::generateSchedule()	From java core infrastructure.
		 * @param	float	$order_amount						The order amount in dollars.
		 * @param	int		$number_of_payments					The number of payments. Defaults to 4.
		 * @return	array										The instalment amounts in dollars.
		 * @used-by	self::render_schedule_on_cart_page()
		 * @used-by	self::payment_fields()
		 */
		private function generate_payment_schedule($order_amount, $number_of_payments = 4) {
			$order_amount_in_cents = $order_amount * 100;
			$instalment_amount_in_cents = round($order_amount_in_cents / $number_of_payments, 0, PHP_ROUND_HALF_UP);
			$cents_left_over = $order_amount_in_cents - ($instalment_amount_in_cents * $number_of_payments);

			$schedule = array();

			for ($i = 0; $i < $number_of_payments; $i++) {
				$schedule[$i] = $instalment_amount_in_cents / 100;
			}

			$schedule[$i - 1] += $cents_left_over / 100;

			return $schedule;
		}

		private function currency_is_supported() {
			return get_option('woocommerce_currency') == get_woocommerce_currency();
		}

		private function payment_is_enabled() {
			return array_key_exists('enabled', $this->settings) && $this->settings['enabled'] == 'yes';
		}

		private function cart_total_is_positive() {
			return WC()->cart->total > 0;
		}

		private function cart_products_are_supported() {
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$product = $cart_item['data'];
				if (!$this->is_product_supported($product)) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Checks that the API is still available by checking against settings
		 *
		 * @used-by render_cart_page_elements()
		 */
		private function api_is_ok() {
			return !($this->settings['pay-over-time-limit-min'] == 'N/A' && $this->settings['pay-over-time-limit-max'] == 'N/A'
			|| empty($this->settings['pay-over-time-limit-min']) && empty($this->settings['pay-over-time-limit-max']));
		}

		/**
		 * Calls functions that render Afterpay elements on Cart page
		 * 		- logo
		 * 		- payment schedule
		 * 		- express button.
		 *
		 * This is dependant on all of the following criteria being met:
		 * 		- The currency is supported
		 *		- The Afterpay Payment Gateway is enabled.
		 *		- The cart total is valid and within the merchant payment limits.
		 *		- All of the items in the cart are considered eligible to be purchased with Afterpay.
		 *
		 * Note:	Hooked onto the "woocommerce_cart_totals_after_order_total" Action.
		 *
		 * @since	3.1.0
		 * @see		Afterpay_Plugin::__construct()								For hook attachment.
		 * @uses	self::currency_is_supported()
		 * @uses	self::payment_is_enabled()
		 * @uses	self::cart_total_is_positive()
		 * @uses	self::cart_products_are_supported()
		 * @uses	self::api_is_ok()
		 * @uses	self::render_schedule_on_cart_page()
		 */
		public function render_cart_page_elements() {
			$merchant = new Afterpay_Plugin_Merchant;
			if( $this->currency_is_supported()
				&& $this->payment_is_enabled()
				&& $this->cart_total_is_positive()
				&& $this->cart_products_are_supported()
				&& $this->api_is_ok()
			) {
				$cart_total = WC()->cart->total;
				$this->render_schedule_on_cart_page($cart_total);
				$this->render_express_checkout_on_cart_page();
			}
		}

		/**
		 * Render Afterpay elements (logo and payment schedule) on Cart page.
		 *
		 * This is dependant on the following criteria being met:
		 *		- The "Payment Info on Cart Page" box is ticked and there is a message to display.
		 *
		 * @since	2.0.0
		 * @uses	self::generate_payment_schedule()
		 * @uses	self::display_price_html()
		 * @uses	self::apply_modal_window()
		 * @uses	apply_filters()												Available in WordPress core since 0.17.
		 * @used-by	self::render_cart_page_elements()
		 */
		public function render_schedule_on_cart_page($total) {
			if (!isset($this->settings['show-info-on-cart-page']) || $this->settings['show-info-on-cart-page'] != 'yes' || empty($this->settings['cart-page-info-text'])) {
				return;
			}

			if( $total < floatval( $this->settings['pay-over-time-limit-min'] ) || $total > floatval( $this->settings['pay-over-time-limit-max'] ) ) {
				//Cart Fallback Flow
				$fallback_asset = $this->assets['fallback_asset'];

				$html = '<tr><td colspan="100%">' . $fallback_asset . '</td></tr>';

				$html = str_replace(array(
					'[MIN_LIMIT]',
					'[MAX_LIMIT]'
				), array(
					$this->display_price_html( floatval( $this->settings['pay-over-time-limit-min'] ) ),
					$this->display_price_html( floatval( $this->settings['pay-over-time-limit-max'] ) )
				), $html);
			}
			else {
				//Normal Cart Flow
				$schedule = $this->generate_payment_schedule(WC()->cart->total);
				$amount = $this->display_price_html($schedule[0]);

				$html = str_replace(array(
					'[AMOUNT]'
				), array(
					$amount
				), $this->settings['cart-page-info-text']);
			}

			# Execute shortcodes on the string before applying filters and rendering it.
			$html = do_shortcode( $html );

			# Add the Modal Window to the page
			# Website Admin have no access to the Modal Window codes for data integrity reasons
			$html = $this->apply_modal_window($html);

			# Allow other plugins to maniplulate or replace the HTML echoed by this funtion.
			echo apply_filters( 'afterpay_html_on_cart_page', $html );
		}
		/**
		 * Render the express checkout elements on Cart page.
		 *
		 * This is dependant on the following criteria being met:
		 *		- The "Show express on cart page" box is ticked.
		 *
		 * @since	3.1.0
		 * @used-by	self::render_cart_page_elements()
		 */
		public function render_express_checkout_on_cart_page() {
			$cart_total = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();

			if (
				!isset($this->settings['show-express-on-cart-page']) ||
				$this->settings['show-express-on-cart-page'] != 'yes' ||
				$cart_total < $this->settings['pay-over-time-limit-min'] ||
				$cart_total > $this->settings['pay-over-time-limit-max']
			) {
				return;
			}

			echo str_replace('[THEME]', $this->settings['express-button-theme'], $this->assets['cart_page_express_button']);
		}

		/**
		 * Get's the country code from assets
		 *
		 * @since 3.1.0
		 * @used-by Afterpay_Plugin::init_website_assets()
		 */
		public function get_country_code () {
			return $this->assets['name'];
		}

		/**
		 * Display as a payment option on the checkout page.
		 *
		 * Note:	This overrides the method defined in the parent class.
		 *
		 * @since	2.0.0
		 * @see		WC_Payment_Gateway::payment_fields()						For the method that this overrides.
		 * @uses	WC()														Available in WooCommerce core since 2.1.0.
		 * @uses	Afterpay_Plugin_Merchant::get_payment_types_for_amount()	If configured to use API v0.
		 * @uses	get_option('woocommerce_currency')								Available in WooCommerce core since 2.6.0.
		 * @uses	self::generate_payment_schedule()
		 * @uses	self::apply_modal_window()
		 * @uses	apply_filters()												Available in WordPress core since 0.17.
		 */
		public function payment_fields() {
			$order_total = $this->get_order_total();

			$instalments = $this->generate_payment_schedule($order_total);

			# Give other plugins a chance to manipulate or replace the HTML echoed by this funtion.
			ob_start();
			include "{$this->include_path}/instalments.html.php";

			$html = ob_get_clean();

			# Add the Modal Window to the page
			# Website Admin have no access to the Modal Window codes for data integrity reasons
			$html = $this->apply_modal_window($html);

			echo apply_filters( 'afterpay_html_at_checkout', $html, $order_total, $instalments );
		}

		/**
		 * Function for encoding special data for storage as WP Post Meta.
		 *
		 * @since	2.0.5
		 * @see		special_decode
		 * @param	mixed		$data
		 * @uses	serialize
		 * @uses	base64_encode
		 * @return	string
		 */
		private function special_encode($data)
		{
			return base64_encode(serialize($data));
		}

		/**
		 * Function for decoding special data from storage as WP Post Meta.
		 *
		 * @since	2.0.5
		 * @see		special_encode
		 * @param	string		$string
		 * @uses	base64_decode
		 * @uses	unserialize
		 * @return	mixed
		 */
		private function special_decode($string)
		{
			return unserialize(base64_decode($string));
		}

		/**
		 * This is called by the WooCommerce checkout via AJAX, if Afterpay was the selected payment method.
		 *
		 * Note:	This overrides the method defined in the parent class.
		 *
		 * @since	2.0.0
		 * @see		WC_Payment_Gateway::process_payment()	For the method we are overriding.
		 * @param	int	$order_id					The ID of the order.
		 * @uses	Afterpay_Plugin_Merchant::get_order_token_for_wc_order_in_v1
		 * @uses	wp_send_json()							Available as part of WordPress core since 3.5.0
		 * @return	array									May also render JSON and exit.
		 */
		public function process_payment($order_id) {
			$order = wc_get_order( $order_id );

			$order_number = $order->get_order_number();
			self::log("Processing payment for WooCommerce Order #{$order_number}...");

			$merchant = new Afterpay_Plugin_Merchant;
			$token = $merchant->get_order_token_for_wc_order_in_v1($order);

			if ($token) {
				update_post_meta( $order_id, '_afterpay_token', $token );

				/*
				** Mimic the afterpay.js to manually generate the redirect url.
				** Once upgraded to use API v2, we can simply use the returned url.
				*/
				$redirect_url = $this->environments[$this->settings['testmode']]['web_url'] . strtolower($this->assets['name']) . '/checkout/?token=' . $token;

				$result = array(
					'result' => 'success',
					'redirect' => $redirect_url
				);
			} else {
				self::log("Unable to create an order token for order ID {$order_id}");
				$result = array(
					'result' => 'failure',
					'redirect' => $order->get_checkout_payment_url(true) // TBC
				);
			}

			return $result;
		}

		/**
		 * Can the order be refunded?
		 *
		 * @since	1.0.0
		 * @param	WC_Order	$order
		 * @return	bool
		 */
		public function can_refund_order($order) {
			$has_api_creds = false;

			if ($this->settings['testmode'] == 'production') {
				$has_api_creds = $this->settings['prod-id'] && $this->settings['prod-secret-key'];
			} else {
				$has_api_creds = $this->settings['test-id'] && $this->settings['test-secret-key'];
			}

			return $order && $order->get_transaction_id() && $has_api_creds;
		}

		/**
		 * Process a refund if supported.
		 *
		 * Note:	This overrides the method defined in the parent class.
		 *
		 * @since	1.0.0
		 * @see		WC_Payment_Gateway::process_refund()		For the method that this overrides.
		 * @param	int			$order_id
		 * @param	float		$amount							Optional. The amount to refund. This cannot exceed the total.
		 * @param	string		$reason							Optional. The reason for the refund. Defaults to an empty string.
		 * @uses	Afterpay_Plugin_Merchant::create_refund()
		 * @return	bool
		 */
		public function process_refund($order_id, $amount = null, $reason = '') {
			$order = wc_get_order( $order_id );

			if (!$this->can_refund_order($order)) {
				return new WP_Error( 'error', __( 'Refund failed.', 'woocommerce' ) );
			}

			$order_number = $order->get_order_number();
			self::log("Refunding WooCommerce Order #{$order_number} for \${$amount}...");

			$merchant = new Afterpay_Plugin_Merchant;
			$success = $merchant->create_refund($order, $amount);

			if ($success) {
				$order->add_order_note( __( "Refund of \${$amount} sent to Afterpay. Reason: {$reason}", 'woo_afterpay' ) );
				return true;
			}

			$order->add_order_note( __( "Failed to send refund of \${$amount} to Afterpay.", 'woo_afterpay' ) );
			return false;
		}

		/**
		 * Return the current settings for Afterpay Plugin
		 *
		 * @since	2.1.0
		 * @used-by	generate_category_hooks(), generate_product_hooks()
		 * @return 	array 	settings array values
		 */
		public function getSettings() {
			return $this->settings;
		}
		/**
		 * Returns Default Customisation Settings of Afterpay Plugin
		 *
		 * Note:	Hooked onto the "wp_ajax_afterpay_action" Action.
		 *
		 * @since	2.1.2
		 * @uses	get_form_fields()   returns $this->form_fields() array
		 * @return 	array               default afterpay customization settings
		 */
		public function reset_settings_api_form_fields() {
				$afterpay_default_settings = $this->get_form_fields();

				$settings_to_replace = array(
					'show-info-on-category-pages'           => $afterpay_default_settings['show-info-on-category-pages']['default'],
					'category-pages-info-text'              => $afterpay_default_settings['category-pages-info-text']['default'],
					'category-pages-hook'                   => $afterpay_default_settings['category-pages-hook']['default'],
					'category-pages-priority'               => $afterpay_default_settings['category-pages-priority']['default'],
					'show-info-on-product-pages'            => $afterpay_default_settings['show-info-on-product-pages']['default'],
					'product-pages-info-text'               => $afterpay_default_settings['product-pages-info-text']['default'],
					'product-pages-hook'                    => $afterpay_default_settings['product-pages-hook']['default'],
					'product-pages-priority'                => $afterpay_default_settings['product-pages-priority']['default'],
					'show-info-on-product-variant'          => $afterpay_default_settings['show-info-on-product-variant']['default'],
					'product-variant-info-text'             => $afterpay_default_settings['product-variant-info-text']['default'],
					'show-outside-limit-on-product-page'    => $afterpay_default_settings['show-outside-limit-on-product-page']['default'],
					'show-info-on-cart-page'                => $afterpay_default_settings['show-info-on-cart-page']['default'],
					'cart-page-info-text'                   => $afterpay_default_settings['cart-page-info-text']['default'],
					'show-express-on-cart-page'             => $afterpay_default_settings['show-express-on-cart-page']['default'],
					'express-button-theme'                  => $afterpay_default_settings['express-button-theme']['default'],
				);

				wp_send_json($settings_to_replace);
		}
		/**
		 * Adds/Updates 'afterpay-plugin-version' in Afterpay settings
		 *
		 * Note:	Hooked onto the "woocommerce_update_options_payment_gateways_" Action.
		 *
		 * @since	2.1.2
		 * @uses	get_option()      returns option value
		 * @uses	update_option()   updates option value
		 */
		public function process_admin_options() {
			$saved = parent::process_admin_options();

			$updated_afterpay_settings = array_replace(
				$this->settings,
				array(
					'afterpay-plugin-version' => Afterpay_Plugin::$version
				)
			);
			update_option($this->get_option_key(), $updated_afterpay_settings);

			return $saved;
		}
		/**
		 * Returns final price of the given product
		 *
		 * @since	2.1.2
		 * @param	WC_Product	$product									The product in question, in the form of a WC_Product object.
		 *																	This affects whether the minimum setting is considered.
		 * @uses	wc_get_price_to_display()								Available as part of the WooCommerce core plugin since 3.0.0.
		 * @uses	WC_Abstract_Legacy_Product::get_display_price()			Possibly available as part of the WooCommerce core plugin since 2.6.0. Deprecated in 3.0.0.
		 * @uses	WC_Product::get_price()									Possibly available as part of the WooCommerce core plugin since 2.6.0.
		 * @return	float | string											Final price of the product.
		 * @used-by self::process_and_print_afterpay_paragraph()
		 */
		public function get_product_final_price($product){
			if (function_exists('wc_get_price_to_display')) {
				$price = wc_get_price_to_display( $product );
			} elseif (method_exists($product, 'get_display_price')) {
				$price = $product->get_display_price();
			} elseif (method_exists($product, 'get_price')) {
				$price = $product->get_price();
			} else {
				$price = 0.00;
			}
			return $price;
		}

		/**
		 * Hides the Afterpay notice
		 *
		 * Note:	Hooked onto the "wp_ajax_afterpay_dismiss_action" Action.
		 *
		 * @since	2.1.4
		 * @uses	get_option('afterpay_rate_notice_dismiss')
		 * @uses	update_option('afterpay_rate_notice_dismiss')
		 * @uses	add_option('afterpay_rate_notice_dismiss')
		 * @return 	bool
		 */
		public function afterpay_notice_dismiss(){
			if(get_option('afterpay_rate_notice_dismiss')){
				update_option('afterpay_rate_notice_dismiss','yes');
			}
			else{
				add_option('afterpay_rate_notice_dismiss','yes');
			}

			wp_send_json(true);
		}
		/**
		 * Provide a shortcode for rendering the standard Afterpay paragraph for theme builders.
		 *
		 * E.g.:
		 * 	- [afterpay_paragraph] OR [afterpay_paragraph type="product"] OR [afterpay_paragraph id="99"]
		 *
		 * @since	2.1.5
		 * @see		Afterpay_Plugin::__construct()		For shortcode definition.
		 * @param	array	$atts			            Array of shortcode attributes.
		 * @uses	shortcode_atts()
		 * @return	string
		 */
		public function shortcode_afterpay_paragraph($atts) {
			$atts = shortcode_atts( array(
				'type' => 'product',
				'id'   => 0
			), $atts );

			if(array_key_exists('id',$atts) &&  $atts['id']!=0){
				if (function_exists('wc_get_product')) {
					$product = wc_get_product( $atts['id'] );
				} else {
					$product = new WC_Product( $atts['id'] );
				}
			}
			else{
				$product = $this->get_product_from_the_post();
			}

			ob_start();
			if($atts['type'] == "product" && !is_null($product)){
				$this->print_info_for_product_detail_page($product);
			}
			return ob_get_clean();
		}
		/**
		 * Function for null check of data.
		 *
		 * @since	2.1.5
		 * @param	mixed		$value
		 * @param	mixed		$default_value
		 * @uses	is_null
		 * @return	mixed
		 */
		private function check_null($value,$default_value="")
		{
			return is_null($value)?$default_value:$value;
		}

		public function generate_express_token()
		{
			try {
				if (
					$_SERVER['REQUEST_METHOD'] != 'POST' ||
					!wp_verify_nonce($_POST['nonce'], "ec_start_nonce")
				) {
					wc_add_notice(__( 'Invalid request made', 'woo_afterpay' ), 'error' );
					throw new Exception('Invalid request', 2);
				}

				$merchant = new Afterpay_Plugin_Merchant;
				$token = $merchant->get_token_for_express_checkout();
				if(!$token) {
					wc_add_notice( __( 'Something went wrong. Please try again.', 'woo_afterpay' ), 'error' );
					throw new Exception('Couldn\'t get token for Express Checkout', 3);
				}

				$response = array(
					'success' => true,
					'token'  => $token
				);
			}
			catch (Exception $e) {
				$response = $this->express_error_handler($e);
			}

			wp_send_json($response);
		}

		public function fetch_express_shipping()
		{
			// Refer to WC_Shortcode_Cart::calculate_shipping()
			try {
				if (
					$_SERVER['REQUEST_METHOD'] != 'POST' ||
					!wp_verify_nonce($_POST['nonce'], "ec_change_nonce") ||
					!array_key_exists('address', $_POST)
				) {
					throw new Exception('Invalid request');
				}

				WC()->shipping()->reset_shipping();

				$address = wc_clean(wp_unslash($_POST['address']));
				$address['country'] = $address['countryCode'];
				$address['phone'] = $address['phoneNumber'];
				$address['city'] = $address['suburb'];

				if ( $address['postcode'] && ! WC_Validation::is_postcode( $address['postcode'], $address['country'] ) ) {
					throw new Exception( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ) );
				} elseif ( $address['postcode'] ) {
					$address['postcode'] = wc_format_postcode( $address['postcode'], $address['country'] );
				}

				if ( $address['country'] ) {
					$names = $this->name_split($address['name']);

					if ( ! WC()->customer->get_billing_first_name() ) {
						WC()->customer->set_billing_first_name($names->first);
						WC()->customer->set_billing_last_name($names->last);
					}
					if ( ! WC()->customer->get_billing_postcode() ) {
						WC()->customer->set_billing_location( $address['country'], $address['state'], $address['postcode'], $address['city'] );
						WC()->customer->set_billing_address_1($address['address1']);
						WC()->customer->set_billing_address_2($address['address2']);
						WC()->customer->set_billing_phone($address['phone']);
					}

					WC()->customer->set_shipping_location( $address['country'], $address['state'], $address['postcode'], $address['city'] );
					WC()->customer->set_shipping_first_name($names->first);
					WC()->customer->set_shipping_last_name($names->last);
					WC()->customer->set_shipping_address_1($address['address1']);
					WC()->customer->set_shipping_address_2($address['address2']);
				} else {
					WC()->customer->set_billing_address_to_base();
					WC()->customer->set_shipping_address_to_base();
				}

				WC()->customer->set_calculated_shipping( true );
				WC()->customer->save();

				//do_action( 'woocommerce_calculated_shipping' );

				WC()->cart->calculate_totals();

				// Refer to wc_cart_totals_shipping_html() at /wp-content/plugins/woocommerce/includes/wc-cart-functions.php
				$packages = WC()->shipping()->get_packages();
				$methods = $packages[0]['rates'];

				if (empty($methods)) {
					throw new Exception('Shipping is unavailable for this address.', 4);
				}

				$response = array();
				$currency = get_option('woocommerce_currency');
				$totals = WC()->cart->get_totals();
				$cart_total = $totals['cart_contents_tax'] + (float)$totals['cart_contents_total'];
				$maximum = floatval($this->settings['pay-over-time-limit-max']);

				foreach ($methods as $method) {
					$shipping_cost = (float)$method->get_cost() + $method->get_shipping_tax();
					$total = $cart_total + $shipping_cost;

					if ($total <= $maximum) {
						$response[] = array(
							'id' => $method->get_id(),
							'name' => $method->get_label(),
							'description' => $method->get_label(),
							'shippingAmount' => array(
								'amount' => number_format($shipping_cost, 2, '.', ''),
								'currency' => $currency
							),
							'orderAmount' => array(
								'amount' => number_format($total, 2, '.', ''),
								'currency' => $currency
							),
						);
					}
				}

				if (empty($response)) {
					throw new Exception('All shipping options exceed Afterpay order limit.', 4);
				}
			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					$shipping_error_response = array(
						'error' => true,
						'message' => $e->getMessage(),
					);

					$response = array_merge($this->express_error_handler($e), $shipping_error_response);
				} else {
					$response = array(
						'error' => true,
						'message' => 'Unknown error',
					);
				}
			}

			wp_send_json($response);
		}

		/**
		 * function to handle express errors
		 *
		 * Error notes:
		 * 	- If log is true, it will log the error message in the afterpay logs
		 *  - Code 1 will write to log and redirect to the pay for order page for the specific order (this requires the $order to be passed in as 2nd param)
		 * 	- Code 2 will not write to logs and will redirect to the cart page
		 * 	- Code 3 will write to logs and will redirect to the cart page
		 * 	- All other errors will not write to log or redirect anywhere
		 *
		 * @since 3.1.0
		 *
		 * @uses get_checkout_payment_url()
		 * @uses wc_get_cart_url()
		 * @uses get_checkout_payment_url()
		 * @uses self::log()
		 * @used-by self::create_order_and_capture_endpoint()
		 * @used-by self::generate_express_token()
		 *
		 * @return array
		 */
		private function express_error_handler($e, $order = null) {
			$response = array();

			switch ($e->getCode()) {
				case 1:
					$error_code_conf = array(
						'log'						=> true,
						'redirect_url' 	=> $order->get_checkout_payment_url()
					);
					break;
				case 2:
					$error_code_conf = array(
						'redirect_url' => wc_get_cart_url()
					);
					break;
				case 3:
					$error_code_conf = array(
						'log' => true,
						'redirect_url' => wc_get_cart_url()
					);
					break;
				case 4:
					$error_code_conf = array(
						'log' => true
					);
					break;
				default:
					$error_code_conf = array();
			}

			$err_conf = (object)array_merge($this->express_base_error_config, $error_code_conf);

			if ($err_conf->log) {
				self::log('[EC] ' . $e->getMessage());
			}

			if ($err_conf->redirect_url) {
				$response['redirectUrl'] = $err_conf->redirect_url;
			}

			return $response;
		}

		/**
		 * Endpoint for creating a WC order from a V1 Afterpay order and capturing.
		 *
		 * Notes:	Hooked onto the "wp_ajax_afterpay_express_complete" Action.
		 * 				Hooked onto the "wp_ajax_nopriv_afterpay_express_complete" Action
		 *
		 * @since	3.1.0
		 * @uses 	self::create_wc_order_from_afterpay_order
		 * @uses  Afterpay_Plugin_Merchant::get_order_by_v1_token
		 * @uses 	wp_send_json
		 * @uses  wp_die
		 * @uses 	self::create_wc_order_from_afterpay_order
		 * @uses 	self::capture_payment_express_checkout
		 * @return	void
		 */
		public function create_order_and_capture_endpoint() {
			try {
				if (
					$_SERVER['REQUEST_METHOD'] != 'POST' ||
					!wp_verify_nonce($_POST['nonce'], "ec_complete_nonce") ||
					!array_key_exists('token', $_POST)
				) {
					wc_add_notice(__( 'Invalid request made', 'woo_afterpay' ), 'error' );
					throw new Exception('Invalid request', 2);
				}

				$merchant = new Afterpay_Plugin_Merchant;
				$afterpay_order = $merchant->get_order_by_v1_token($_POST['token']);
				if (!$afterpay_order) {
					wc_add_notice( __( 'Something went wrong. Please try again.', 'woo_afterpay' ), 'error' );
					throw new Exception("Couldn't get Afterpay Order. Token requested: {$_POST['token']}", 3);
				} else {
					// Transaction Integrity Check
					$latest_cart = array(
						'items' => array(),
						'discounts' => array(),
						'totalAmount' => array()
					);
					$currency = get_option('woocommerce_currency');

					$items = WC()->cart->get_cart();
					foreach ($items as $item) {
						if ($item['variation_id']) {
							$product = wc_get_product($item['variation_id']);
						} else {
							$product = wc_get_product($item['product_id']);
						}
						$latest_cart['items'][] = array(
							'name' => $product->get_name(),
							'sku' => $product->get_sku(),
							'quantity' => $item['quantity'],
							'price' => array(
								'amount' => number_format((float)(($item['line_subtotal']+$item['line_subtotal_tax'])/$item['quantity']), 2, '.', ''),
								'currency' => $currency
							)
						);
					}
					if (json_encode($latest_cart['items']) !== json_encode($afterpay_order->items)) {
						wc_add_notice( __( 'Cart items were changed unexpectedly. Please try again.', 'woo_afterpay' ), 'error' );
						throw new Exception("Cart items were changed unexpectedly.", 3);
					}

					$coupons = WC()->cart->get_applied_coupons();
					foreach ($coupons as $coupon_code) {
						$latest_cart['discounts'][] = array(
							'displayName' => $coupon_code,
							'amount' => array(
								'amount' => number_format((float)WC()->cart->get_coupon_discount_amount($coupon_code, false), 2, '.', ''),
								'currency' => $currency
							)
						);
					}
					if (json_encode($latest_cart['discounts']) !== json_encode($afterpay_order->discounts)) {
						wc_add_notice( __( 'Cart coupons were changed unexpectedly. Please try again.', 'woo_afterpay' ), 'error' );
						throw new Exception("Cart coupons were changed unexpectedly.", 3);
					}

					$chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');

					if(!isset($afterpay_order->shippingOptionIdentifier)) {
						if (!$this->cart_is_virtual()) {
							wc_add_notice( __( 'Product types were changed unexpectedly. Please try again.', 'woo_afterpay' ), 'error' );
							throw new Exception("Product types were changed unexpectedly.", 3);
						}
					} else if (empty($chosen_shipping_methods) || $chosen_shipping_methods[0] !== $afterpay_order->shippingOptionIdentifier) {
						wc_add_notice( __( 'Shipping method was changed unexpectedly. Please try again.', 'woo_afterpay' ), 'error' );
						throw new Exception("Shipping method was changed unexpectedly.", 3);
					}

					WC()->cart->calculate_totals();
					$totals = WC()->cart->get_totals();
					$latest_cart['totalAmount'] = array(
						'amount' => number_format((float)$totals['total'], 2, '.', ''),
						'currency' => $currency
					);
					if (json_encode($latest_cart['totalAmount']) !== json_encode($afterpay_order->totalAmount)) {
						wc_add_notice( __( 'Cart totals were changed unexpectedly. Please try again.', 'woo_afterpay' ), 'error' );
						throw new Exception("Cart totals were changed unexpectedly.", 3);
					}
				}

				if( ! WC()->customer->get_billing_email() ) {
					WC()->customer->set_billing_email($afterpay_order->consumer->email);
				}

				$order = $this->create_wc_order_from_cart();
				if (!$order) {
					wc_add_notice( __( 'Something went wrong. Please try again.', 'woo_afterpay' ), 'error' );
					throw new Exception("Couldn't create Woocommmerce order. Afterpay token: {$_POST['token']}", 3);
				}
				$order->add_order_note( __( "Afterpay order token: {$_POST['token']}", 'woo_afterpay' ) );

				$capture = $this->capture_payment_express_checkout($order, $afterpay_order);
				if (!$capture) {
					$order->update_status('failed');
					wc_add_notice( __( 'Payment failed. Please try again.', 'woo_afterpay' ), 'error' );
					throw new Exception("Couldn't capture for Afterpay order. Token: {$_POST['token']}", 1);
				}

				$response = array(
					'redirectUrl' => $this->get_return_url($order)
				);
			} catch (Exception $e) {
				$response = $this->express_error_handler($e, $order);
			}

			wp_send_json($response);
			wp_die();
		}

		/**
		 * Adds classes to cart items based on if an item needs shipping
		 *
		 * Note:	Hooked onto the "woocommerce_cart_item_class" Filter.
		 *
		 * @since 3.1.0
		 *
		 * @return string
		 */
		public function add_shippable_class_to_cart_item($classes, $values, $values_keys) {
			if ($values['data']->is_virtual()) {
				$classes .= ' requires-shipping--false';
			} else {
				$classes .= ' requires-shipping--true';
			}

			return $classes;
		}

		/**
		 * Function for creating a WC order from a V1 Afterpay order
		 *
		 * @since	3.1.0
		 * @used-by create_order_and_capture_endpoint
		 * @return	object
		 */
		private function create_wc_order_from_cart() {
			try {
				WC()->cart->calculate_totals();
				$checkout = WC()->checkout();
				$order_id = $checkout->create_order(array());
				$order = wc_get_order($order_id);
				$order->calculate_totals();
				$order->set_customer_id(WC()->customer->get_id());
				$order->set_address(WC()->customer->get_billing(), 'billing');
				$order->set_address(WC()->customer->get_shipping(), 'shipping');

				$order->set_payment_method($this);
			} catch(Exception $e) {
				wc_add_notice( __( 'Your order couldn\'t be created. Please try again.', 'woo_afterpay' ), 'error' );
				throw new Exception("Woocommerce couldn't create the order: {$e->getMessage()}", 3);
			}

			$order->save();

			return $order;
		}

		/**
		 * splits a full name into an object with first and last name
		 *
		 * @since 3.1.0
		 *
		 * @param string $name
		 * @used-by self::fetch_express_shipping()
		 *
		 * @return object
		 */
		private function name_split($name) {
			$full_name = explode(' ', $name);
			$last_name = array_pop($full_name);
			if (empty($full_name)) {
				$first_name = $last_name; // if $afterpay_order->shipping->name contains only one word
				$last_name = '';
			} else {
				$first_name = implode(' ', $full_name);
			}

			return (object)array(
				'first' => $first_name,
				'last' 	=> $last_name
			);
		}

		/**
		 * Checks that all items in cart are virtual
		 *
		 * @since 3.1.0
		 *
		 * @return boolean
		 */
		private function cart_is_virtual() {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if (!$cart_item['data']->is_virtual()) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Function for creating capturing.
		 *
		 * @since	3.1.0
		 * @param	object	$afterpay_order
		 * @param	object	$order
		 * @used-by create_order_and_capture_endpoint
		 * @return	array
		 */
		private function capture_payment_express_checkout($order, $afterpay_order) {
			$merchant = new Afterpay_Plugin_Merchant;
			$afterpay_token = $afterpay_order->token;

			$order_number = $order->get_order_number();

			$amount = array(
				'amount' => number_format((float)($order->get_total()), 2, '.', ''),
				'currency' => $order->get_currency()
			);

			$response = $merchant->direct_payment_capture_compatibility_mode($afterpay_token, $order_number, $amount);

			if (is_object($response)) {
				if ($response->status == 'APPROVED') {
					self::log("Payment APPROVED for WooCommerce Order #{$order_number} (Afterpay Order #{$response->id}).");

					$order->add_order_note( sprintf(__( 'Payment approved. Afterpay Order ID: %s.', 'woo_afterpay' ), $response->id) );
					$order->payment_complete($response->id);

					return $response;
				} elseif ($response->status == 'DECLINED') {
					$order->update_status( 'failed', sprintf(__( 'Payment declined. Afterpay Order ID: %s.', 'woo_afterpay' ), $response->id) );
					wc_add_notice( sprintf(__( 'Your payment was declined for Afterpay Order #%s. Please try again. For more information, please contact the Afterpay Customer Service team on '.$this->assets['cs_number'].'.', 'woo_afterpay' ), $response->id), 'error' );
					throw new Exception("Payment DECLINED for WooCommerce Order #{$order_number} (Afterpay Order #{$response->id}).", 1);
				}
			} else {
				$order->update_status( 'failed', __( 'Afterpay payment failed.', 'woo_afterpay' ) );
				wc_add_notice( __( 'Something went wrong. Please try again.', 'woo_afterpay' ), 'error' );
				throw new Exception("Updating status of WooCommerce Order #{$order_number} to \"Failed\", because response is not an object. Afterpay Token: {$afterpay_token}", 1);
			}

			$order->update_status( 'failed', __( 'Afterpay payment failed.', 'woo_afterpay' ) );
			wc_add_notice( __( 'Something went wrong. Please try again.', 'woo_afterpay' ), 'error' );
			throw new Exception("Updating status of WooCommerce Order #{$order_number} to \"Failed\", because response object is invalid. Afterpay Token: {$afterpay_token}", 1);
		}

		/**
		 * Function for handling express change shipping method event.
		 *
		 * @since	3.1.0
		 * @uses 	wp_send_json
		 * @uses  wp_die
		 * @uses  wp_verify_nonce
		 *
		 * @return	void
		 */
		public function express_update_wc_shipping() {
			try {
				if (
					$_SERVER['REQUEST_METHOD'] != 'POST' ||
					!wp_verify_nonce($_POST['nonce'], 'ec_change_shipping_nonce') ||
					!array_key_exists('shipping', $_POST)
				) {
					throw new Exception('Invalid request');
				}

				WC()->session->set( 'chosen_shipping_methods', array($_POST['shipping']));
				wp_send_json(array(
					'status' => 'SUCCESS'
				));
				wp_die();
			} catch (Exception $e) {
				wp_send_json(array(
					'status' => 'ERROR',
					'error' => $e->getMessage()
				));
				wp_die();
			}
		}
	}
}
