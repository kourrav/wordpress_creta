=== Afterpay Gateway for WooCommerce ===
Contributors: afterpayit
Tags: woocommerce, afterpay
Requires at least: 4.8.3
Tested up to: 5.7.2
Stable tag: 3.1.0
License: GNU Public License
License URI: https://www.gnu.org/licenses/

Provide Afterpay as a payment option for WooCommerce orders.

== Description ==

Give your customers the option to buy now and pay later with Afterpay. The "Afterpay Gateway for WooCommerce" plugin provides the option to choose Afterpay as the payment method at the checkout. It also provides the functionality to display the Afterpay logo and instalment calculations below product prices on category pages, individual product pages, and on the cart page. For each payment that is approved by Afterpay, an order will be created inside the WooCommerce system like any other order. Automatic refunds are also supported.

== Installation ==

This section outlines the steps to install the Afterpay plugin.

> Please note: If you are upgrading to a newer version of the Afterpay plugin, it is considered best practice to perform a backup of your website - including the WordPress database - before commencing the installation steps. Afterpay recommends all system and plugin updates to be tested in a staging environment prior to deployment to production.

1. Login to your WordPress admin.
1. Navigate to "Plugins > Add New".
1. Type "Afterpay" into the Keyword search box and press the Enter key.
1. Find the "Afterpay Gateway for WooCommerce" plugin. Note: the plugin is made by "Afterpay".
1. Click the "Install Now" button.
1. Click the "Activate" button.
1. Navigate to "WooCommerce > Settings".
1. Click the "Checkout" tab.
1. Click the "Afterpay" sub-tab.
1. Enter the Merchant ID and Secret Key that were provided by Afterpay for Production use.
1. Save changes.

== Frequently Asked Questions ==

= What do I do if I need help? =

Please refer to the [User Guide](https://developers.afterpay.com/afterpay-online/docs/woocommerce). Most common questions are answered in the [FAQ](https://developers.afterpay.com/afterpay-online/docs/woocommerce-faq). There is also the option to create a support ticket in the official [Afterpay Help Centre](https://help.afterpay.com/hc) if necessary.

== Changelog ==

= 3.1.0 =
*Release Date: Monday, 24 May 2021*

* Introduced an implementation of Afterpay Express Checkout on the cart page.
* Improved display of payment declined messaging for registering users.
* Other minor enhancements.

= 3.0.2 =
*Release Date: Wednesday, 05 May 2021*

* Improved reliability of the WooCommerce Order lookup process after consumers confirm payment and return from Afterpay.
* Tested and verified support for WordPress 5.7.1 and WooCommerce 5.2.2.

= 3.0.1 =
*Release Date: Wednesday, 28 Apr 2021*

* Improved compatibility with customized order numbers.
* Tested and verified support for WordPress 5.7.1 and WooCommerce 5.2.2.

= 3.0.0 =
*Release Date: Thursday, 22 Apr 2021*

* Revised transaction flow to more closely follow WooCommerce recommendations.
* Allow customers to pay using Afterpay for existing (unpaid) orders, with or without traversing through the WooCommerce checkout.
* Tested and verified support for WordPress 5.7 and WooCommerce 5.2.

= 2.2.2 =
*Release Date: Monday, 19 Oct 2020*

* Tested and verified support for WordPress 5.5.1 and WooCommerce 4.6.0.
* Improved website performance by loading plugin assets on WooCommerce pages only when the plugin is activated.
* Improved compatibility with other plugins where a trailing slash is appended to the url and may have caused issues with transactions.
* Improved display of the 'Payment Info on Category Pages'.
* Improved display of the 'Outside Payment Limit Info'.

= 2.2.1 =
*Release Date: Friday, 11 Sep 2020*

* Tested and verified support for WordPress 5.5 and WooCommerce 4.4.
* Fixed a defect that may have caused PHP errors when running cron jobs.

= 2.2.0 =
*Release Date: Wednesday, 26 Aug 2020*

* Tested and verified support for WordPress 5.5 and WooCommerce 4.4.
* Added support for Canadian merchants/consumers and CAD.
* Standardized modal content by using Afterpay Global JS Library.
* Improved flexibility of the hook used for Payment Info on Individual Product Pages.
* Improved usage of 'afterpay_is_product_supported' hook in the Checkout page.
* Updated FAQ documentation.

= 2.1.6 =
*Release Date: Wednesday, 22 Jul 2020*

* Tested up to WordPress 5.4 with WooCommerce 4.3.
* Improved handling of price breakdown using the displayed price, inclusion of tax now inherited from WooCommerce settings.
* Improved the experience for new customers who ticked the box to create an account, then their payment is declined. These customers are redirected to the cart page instead of the checkout to ensure the decline message can be read.
* Improved user experience by providing higher resolution modal artwork for users with high pixel density ratio screens.
* Improved handling of instalment message for variable products that are out of stock.

= 2.1.5 =
*Release Date: Wednesday, 01 Apr 2020*

* Tested up to WordPress 5.4 with WooCommerce 4.0.
* Added a shortcode to render PDP assets from page builders or on custom pages without requiring an action hook.
* Added region-specific customer service numbers to decline messages at the checkout.
* Improved the experience for new customers who ticked the box to create an account, then cancel their payment. These customers are redirected to the cart page instead of the checkout to ensure the payment cancellation message can be read.
* Improved handling of products with null price values.
* Improved compatibility with WooCommerce Product Bundles.

= 2.1.4 =
*Release Date: Wednesday, 11 Mar 2020*

* Tested up to WordPress 5.3 with WooCommerce 4.0.
* Added a new admin notification to encourage submitting a plugin review after 14 days.
* Updated JS to improve compatibility with Google Closure compression.
* Improved support for orders without shipping addresses.
* Improved method of accessing Order properties in Compatibility Mode.
* Improved handling of invalid products sent to WC_Gateway_Afterpay::is_product_supported.
* Removed references to WordPress internal constants.

= 2.1.3 =
*Release Date: Tuesday, 12 Nov 2019*

* Tested up to WordPress 5.3 with WooCommerce 3.8.
* Fixed a checkout challenge affecting some US customers on version 2.1.2.
* Removed a legacy admin notice containing a reference to a WooThemes plugin.

= 2.1.2 =
*Release Date: Thursday, 31 Oct 2019*

* Tested up to WordPress 5.3 with WooCommerce 3.8.
* Added a notification in the admin when the plugin has been updated and the configuration needs to be reviewed.
* Added a "Restore Defaults" button for customisations to the plugin configuration.
* Simplified the redirection process between the WooCommerce checkout page and the Afterpay payment screenflow.
* Revised the conditions for triggering the Afterpay messaging that applies to products outside the merchant's Afterpay payment limits.
* Revised the conditions controlling the inclusion of Afterpay as an available payment method, so that Afterpay does not appear if the currency has been changed by a third party plugin.
* Removed the dependency on serialisation of the WC_Checkout object.
* Removed the dependency on the PHP parse_ini_file function.

= 2.1.1 =
*Release Date: Friday, 30 Aug 2019*

* Tested up to WordPress 5.3 with WooCommerce 3.7.
* Improved support for orders without shipping addresses.

= 2.1.0 =
*Release Date: Tuesday, 13 Aug 2019*

* Tested up to WordPress 5.3 with WooCommerce 3.7.
* Revised checkout flow for WooCommerce 3.6+.
* Added a Compatibility Mode to minimise conflicts with third party plugins.
* Added an interface to customise hooks and priorities.
* Replaced idempotent retry processes with extended timeouts.
* Extended logging in Debug Mode.
* Improved handling of Afterpay assets on product variants and related products.
* Improved jQuery version checking.
* Improved handling of non-JSON API responses.

= 2.0.5 =
*Release Date: Wednesday, 01 May 2019*

* Improved support for quotes and special characters used in product attributes and checkout fields.

= 2.0.4 =
*Release Date: Wednesday, 19 December 2018*

* Reduced logging of unnecessary notices.
* Improved support for custom meta fields on WooCommerce order line items.

= 2.0.3 =
*Release Date: Tuesday, 11 September 2018*

* Improved support for custom meta fields for WooCommerce orders.
* Improved compatibility with third-party currency switcher plugins.
* Improved handling of WooCommerce order line items.

= 2.0.2 =
*Release Date: Wednesday, 29 August 2018*

* Improved support for Variable Products.
* Improved handling of network challenges in scheduled background tasks.

= 2.0.1 =
*Release Date: Thursday, 19 July 2018*

* Added support for Afterpay assets to display on product and cart pages where prices are outside merchant payment limits.
* Added support for multi-market use in Australia, New Zealand and United States.
* Improved logging of network challenges.
* Improved Afterpay JavaScript initialisation to cater for transactions from Australia, New Zealand and the United States.
* Improved handling of Afterpay pop-up assets - deprecated the use of fancyBox.
* Improved reliability of payment capture and refunds - implemented a retry mechanism in the unlikely event of network challenges.
* Updated plugin configuration defaults for each regional market (AU/NZ/US).
* Updated assets for Afterpay United States.

= 2.0.0 =
*Release Date: Friday, 13 July 2018*

* Added support for merchants in New Zealand and the United States.
* Added support for the calculation of instalment amounts at the product level for variably priced products.
* Added support for orders that do not require shipping addresses.
* Added support for optionally including Afterpay elements on the cart page.
* Added a shortcode for rendering the standard Afterpay logo, with support for high pixel-density screens and a choice of 3 colour variants.
* Improved ease of installation and configuration.
* Improved presentation of checkout elements for various screen sizes.
* Improved customisability for developers.
* Changed order button to read "Proceed to Afterpay" when configured to use the "v1 (recommended)" API Version.
* Changed the payment declined messages to include the phone number for the Afterpay Customer Service Team.
* Changed the default HTML for category pages and individual product pages to take advantage of the latest features.
* Changed the plugin name from "WooCommerce Afterpay Gateway" to "Afterpay Gateway for WooCommerce".
* Removed deprecated CSS.

= 1.3.1 =
*Release Date: Monday, 10 April 2017*

* Improved compatibility with WooCommerce 3 - resolution of the "invalid product" checkout challenge.
