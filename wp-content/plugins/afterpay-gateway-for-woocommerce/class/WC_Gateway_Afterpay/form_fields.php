<?php
/**
* Default values for the WooCommerce Afterpay Plugin Admin Form Fields
*/

$environments = include 'environments.php';

# Process Region-based Assets
$region_assets = include 'assets.php';

$ctg_page_asset 		= 	$region_assets['category_page'];
$product_page_asset		= 	$region_assets['product_page'];
$product_variant_asset 	= 	$region_assets['product_variant'];
$cart_page_asset 		= 	$region_assets['cart_page'];

return array(
	'core-configuration-title' => array(
		'title'				=> __( 'Core Configuration', 'woo_afterpay' ),
		'type'				=> 'title'
	),
	'enabled' => array(
		'title'				=> __( 'Enable/Disable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'label'				=> __( 'Enable Afterpay', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'title' => array(
		'title'				=> __( 'Title', 'woo_afterpay' ),
		'type'				=> 'text',
		'description'		=> __( 'This controls the payment method title which the user sees during checkout.', 'woo_afterpay' ),
		'default'			=> __( 'Afterpay', 'woo_afterpay' )
	),
	'testmode' => array(
		'title'				=> __( 'API Environment', 'woo_afterpay' ),
		'type'				=> 'select',
		'options'			=> wp_list_pluck( $environments, 'name' ),
		'default'			=> 'production',
		'description'		=> __( 'Note: Sandbox and Production API credentials are not interchangeable.', 'woo_afterpay' )
	),
	'prod-id' => array(
		'title'				=> __( 'Merchant ID (Production)', 'woo_afterpay' ),
		'type'				=> 'text',
		'default'			=> ''
	),
	'prod-secret-key' => array(
		'title'				=> __( 'Secret Key (Production)', 'woo_afterpay' ),
		'type'				=> 'password',
		'default'			=> ''
	),
	'test-id' => array(
		'title'				=> __( 'Merchant ID (Sandbox)', 'woo_afterpay' ),
		'type'				=> 'text',
		'default'			=> ''
	),
	'test-secret-key' => array(
		'title'				=> __( 'Secret Key (Sandbox)', 'woo_afterpay' ),
		'type'				=> 'password',
		'default'			=> ''
	),
	'pay-over-time-limit-min' => array(
		'title'				=> __( 'Minimum Payment Amount', 'woo_afterpay' ),
		'type'				=> 'input',
		'description'		=> __( 'This information is supplied by Afterpay and cannot be edited.', 'woo_afterpay' ),
		'custom_attributes'	=>	array(
									'readonly' => 'true'
								),
		'default'			=> ''
	),
	'pay-over-time-limit-max' => array(
		'title'				=> __( 'Maximum Payment Amount', 'woo_afterpay' ),
		'type'				=> 'input',
		'description'		=> __( 'This information is supplied by Afterpay and cannot be edited.', 'woo_afterpay' ),
		'custom_attributes'	=>	array(
									'readonly' => 'true'
								),
		'default'			=> ''
	),
	'express-checkout-title' => array(
		'title'				=> __( 'Express Checkout Configuration', 'woo_afterpay' ),
		'type'				=> 'title'
	),
	'show-express-on-cart-page' => array(
		'title'				=> __( 'Enable on Cart Page', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'	=> __( 'Display Afterpay Express Checkout element on the cart page', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'express-button-theme' => array(
		'title'				=> __( 'Cart Page: Express Button Theme', 'woo_afterpay' ),
		'type'				=> 'select',
		'default'			=> 'black-on-mint',
		'options' 		=> array(
			'black-on-mint' 	=> 'Black on Mint',
			'white-on-black'	=> 'White on Black'
 		)
	),
	'presentational-customisation-title' => array(
		'title'				=> __( 'Customisation', 'woo_afterpay' ),
		'type'				=> 'title',
		'description'		=> __( 'Please feel free to customise the presentation of the Afterpay elements below to suit the individual needs of your web store.</p><p><em>Note: Advanced customisations may require the assistance of your web development team. <a id="reset-to-default-link" style="cursor:pointer;text-decoration:underline;">Restore Defaults</a></em>', 'woo_afterpay' )
	),
	'show-info-on-category-pages' => array(
		'title'				=> __( 'Payment Info on Category Pages', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'		=> __( 'Enable to display Afterpay elements on category pages', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'category-pages-info-text' => array(
		'type'				=> 'wysiwyg',
		'default'			=> $ctg_page_asset,
		'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. Use [OF_OR_FROM] to insert "from" if the product\'s price is variable, or "of" if it is static.', 'woo_afterpay' )
	),
	'category-pages-hook' => array(
		'type'				=> 'text',
		'placeholder'		=> 'Enter hook name (e.g. woocommerce_after_shop_loop_item_title)',
		'default'			=> 'woocommerce_after_shop_loop_item_title',
		'description'		=> __( 'Set the hook to be used for Payment Info on Category Pages.', 'woo_afterpay' )
	),
	'category-pages-priority' => array(
		'type'				=> 'number',
		'placeholder'		=> 'Enter a priority number',
		'default'			=> 99,
		'description'		=> __( 'Set the hook priority to be used for Payment Info on Category Pages.', 'woo_afterpay' )
	),
	'show-info-on-product-pages' => array(
		'title'				=> __( 'Payment Info on Individual Product Pages', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'		=> __( 'Enable to display Afterpay elements on individual product pages', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'product-pages-info-text' => array(
		'type'				=> 'wysiwyg',
		'default'			=> $product_page_asset,
		'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. Use [OF_OR_FROM] to insert "from" if the product\'s price is variable, or "of" if it is static.', 'woo_afterpay' )
	),
	'product-pages-hook' => array(
		'type'				=> 'text',
		'placeholder'		=> 'Enter hook name (e.g. woocommerce_single_product_summary)',
		'default'			=> 'woocommerce_single_product_summary',
		'description'		=> __( 'Set the hook to be used for Payment Info on Individual Product Pages.', 'woo_afterpay' )
	),
	'product-pages-priority' => array(
		'type'				=> 'number',
		'placeholder'		=> 'Enter a priority number',
		'default'			=> 15,
		'description'		=> __( 'Set the hook priority to be used for Payment Info on Individual Product Pages.', 'woo_afterpay' )
	),
	'product-pages-shortcode' => array(
		'type'				=> 'hidden',
		'description'		=> __( '<h3 class="wc-settings-sub-title">Page Builders</h3> If you use a page builder plugin, the above payment info can be placed using a shortcode instead of relying on hooks. Use [afterpay_paragraph] within a product page, or include the product ID to display the info for a specific product on any custom page. E.g.: [afterpay_paragraph id="99"]', 'woo_afterpay' )
	),
	'show-info-on-product-variant' => array(
		'title'				=> __( 'Payment Info Display for Product Variant', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'		=> __( 'Enable to display Afterpay elements upon product variant selection', 'woo_afterpay' ),
		'default'			=> 'no'
	),
	'product-variant-info-text' => array(
		'type'				=> 'wysiwyg',
		'default'			=> $product_variant_asset,
		'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount.', 'woo_afterpay' )
	),
	'show-outside-limit-on-product-page' => array(
		'title'				=> __( 'Outside Payment Limit Info on Product Page', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'		=> __( 'Enable to display Outside Payment Limits Text on the product page', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'show-info-on-cart-page' => array(
		'title'				=> __( 'Payment Info on Cart Page', 'woo_afterpay' ),
		'label'				=> __( 'Enable', 'woo_afterpay' ),
		'type'				=> 'checkbox',
		'description'		=> __( 'Enable to display Afterpay elements on the cart page', 'woo_afterpay' ),
		'default'			=> 'yes'
	),
	'cart-page-info-text' => array(
		'type'				=> 'textarea',
		'default'			=> $cart_page_asset,
		'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. In this case, the instalment amount will be calculated based on the grand total of the cart, including tax and shipping.', 'woo_afterpay' )
	),
	'afterpay-checkout-experience' => array(
		'type'				=> 'hidden',
		'default'			=> 'redirect',
	),
);
