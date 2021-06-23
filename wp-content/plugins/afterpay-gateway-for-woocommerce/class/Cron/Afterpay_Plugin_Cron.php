<?php
/**
* Afterpay Plugin CRON Handler Class
*/
class Afterpay_Plugin_Cron
{
	/**
	 * Create a new WP-Cron job scheduling interval so jobs can run "Every 15 minutes".
	 *
	 * Note:	Hooked onto the "cron_schedules" Filter.
	 *
	 * @since	2.0.0
	 * @param	array	$schedules	The current array of cron schedules.
	 * @return	array				Array of cron schedules with 15 minutes added.
	 **/
	public static function edit_cron_schedules($schedules) {
		$schedules['15min'] = array(
			'interval' => 15 * 60,
			'display' => __( 'Every 15 minutes', 'woo_afterpay' )
		);
		return $schedules;
	}

	/**
	 * Schedule the WP-Cron job for Afterpay.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::activate_plugin()
	 * @uses	wp_next_scheduled()
	 * @uses	wp_schedule_event()
	 **/
	public static function create_jobs() {
		$timestamp = wp_next_scheduled( 'afterpay_do_cron_jobs' );
		if ($timestamp == false) {
			wp_schedule_event( time(), '15min', 'afterpay_do_cron_jobs' );
		}
	}

	/**
	 * Delete the Afterpay WP-Cron job.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::deactivate_plugin()
	 * @uses	wp_clear_scheduled_hook()
	 **/
	public static function delete_jobs() {
		wp_clear_scheduled_hook( 'afterpay_do_cron_jobs' );
	}

	/**
	 * Fire the Afterpay WP-Cron job.
	 *
	 * Note:	Hooked onto the "afterpay_do_cron_jobs" Action, which exists
	 *			because we scheduled a cron under that key when the plugin was activated.
	 *
	 * Note:	Hooked onto the "woocommerce_update_options_payment_gateways_afterpay"
	 *			Action as well.
	 *
	 * @since	2.0.0
	 * @see		Afterpay_Plugin::__construct()	For hook attachment.
	 * @see		self::create_jobs()				For initial scheduling (on plugin activation).
	 * @uses	is_admin()
	 * @uses	WC_Gateway_Afterpay::log()
	 * @uses	self::update_payment_limits()
	 */
	public static function fire_jobs() {
		if (defined('DOING_CRON') && DOING_CRON === true) {
			$fired_by = 'schedule';
		} elseif (is_admin()) {
			$fired_by = 'admin';
		} else {
			$fired_by = 'unknown';
		}
		WC_Gateway_Afterpay::log("Firing cron by {$fired_by}...");

		self::update_payment_limits();
	}

	/**
	 * Load Afterpay Settings
	 *
	 * Note:	Get the plugin settings to be processed within teh CRON
	 *
	 *
	 * @since	2.0.0-rc3
	 * @return 	string
	 *
	 * @uses	WC_Gateway_Afterpay::get_option_key()	Getting the Plugin Settings Key in DB
	 * @used-by	self::update_payment_limits()
	 */
	private static function get_settings_key() {
		$gateway = new WC_Gateway_Afterpay;
		$settings_key = $gateway->get_option_key();
		return $settings_key;
	}

	/**
	 * Load this merchant's payment limits from the API.
	 *
	 * Note:	If this fails, an error will be stored in the database, which should display throughout
	 *			the admin area until resolved.
	 *
	 * @since	2.0.0
	 * @uses	Afterpay_Plugin_Merchant::get_payment_types()	If configured to use v0.
	 * @uses	WC_Gateway_Afterpay::log()
	 * @uses	Afterpay_Plugin_Merchant::get_configuration()	If configured to use v1.
	 * @uses	self::get_settings_key()
	 * @used-by	self::fire_jobs()
	 */
	private static function update_payment_limits() {

		$settings_key = self::get_settings_key();
		$settings = get_option( $settings_key );

		if ($settings['enabled'] == 'yes') {
			if ($settings['testmode'] == 'production') {
				if (empty($settings['prod-id']) && empty($settings['prod-secret-key'])) {
					# Don't hit the Production API without Production creds.
					return false;
				}
			} elseif ($settings['testmode'] == 'sandbox') {
				if (empty($settings['test-id']) && empty($settings['test-secret-key'])) {
					# Don't hit the Sandbox API without Sandbox creds.
					return false;
				}
			}
		} else {
			# Don't hit any API when the gateway is not Enabled.
			return false;
		}

		$merchant = new Afterpay_Plugin_Merchant;
		$settings_changed = false;

		$payment_configurations = $merchant->get_configuration();

		if (is_array($payment_configurations)) {
			foreach ($payment_configurations as $payment_configuration) {
				if ($payment_configuration->type == 'PAY_BY_INSTALLMENT') {
					$old_min = floatval($settings['pay-over-time-limit-min']);
					$old_max = floatval($settings['pay-over-time-limit-max']);
					$new_min = (property_exists($payment_configuration, 'minimumAmount') && is_object($payment_configuration->minimumAmount)) ? $payment_configuration->minimumAmount->amount : '0.00';
					$new_max = (property_exists($payment_configuration, 'maximumAmount') && is_object($payment_configuration->maximumAmount)) ? $payment_configuration->maximumAmount->amount : '0.00';
					if ($new_min != $old_min) {
						$settings_changed = true;
						WC_Gateway_Afterpay::log("Cron changing payment limit MIN from '{$old_min}' to '{$new_min}'.");
						$settings['pay-over-time-limit-min'] = $new_min;
					}
					if ($new_max != $old_max) {
						$settings_changed = true;
						WC_Gateway_Afterpay::log("Cron changing payment limit MAX from '{$old_max}' to '{$new_max}'.");
						$settings['pay-over-time-limit-max'] = $new_max;
					}
				}
			}
		}
		else {
			# Only change the values if getting 401
			if($payment_configurations == 401) {
				$settings_changed = true;
				$settings['pay-over-time-limit-min'] = 'N/A';
				$settings['pay-over-time-limit-max'] = 'N/A';
			}
		}

		if ($settings_changed) {
			update_option( $settings_key, $settings );
		}
	}
}
