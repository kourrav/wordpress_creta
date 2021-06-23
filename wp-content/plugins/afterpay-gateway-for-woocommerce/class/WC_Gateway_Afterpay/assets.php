<?php
$get_afterpay_assets = function()
{
	// These are assets values in the Afterpay - WooCommerce plugin
	$global_assets = array(
		"checkout_page_footer" 							=>	'You will be redirected to the Afterpay website when you click',
		"product_variant_fallback_asset"		=>	'Afterpay available between <strong>[MIN_LIMIT]</strong> - <strong>[MAX_LIMIT]</strong>',
		"product_variant_fallback_asset_2"	=>	'Afterpay available for orders up to <strong>[MAX_LIMIT]</strong>',
		"cart_page_express_button"					=>	'<tr><td colspan="100%" style="text-align: center;"><button id="afterpay_express_button" class="btn-afterpay_express btn-afterpay_express_cart" type="button" disabled><img src="https://static.afterpay.com/button/checkout-with-afterpay/[THEME].svg" alt="Checkout with Afterpay" /></button></td></tr>',
	);

	$assets = array(
		"USD" => array(
			"name"                     =>  'US',
			"product_page"             =>  'or 4 installments [OF_OR_FROM] <strong>[AMOUNT]</strong> by <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a>',
			"category_page"            =>  'or 4 installments [OF_OR_FROM] <strong>[AMOUNT]</strong> by Afterpay',
			"product_variant"          =>  'or 4 installments of <strong>[AMOUNT]</strong> by Afterpay',
			"cart_page"                =>  '<tr><td colspan="100%">or 4 installments of <strong>[AMOUNT]</strong> by <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a></td></tr>',
			"checkout_page_cta"        =>  'Four interest-free installments totaling',
			"checkout_page_first_step" =>  'First Installment',
			"fallback_asset"           =>  '[afterpay_product_logo theme="colour"] available between <strong>[MIN_LIMIT]</strong> - <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"fallback_asset_2"         =>  '[afterpay_product_logo theme="colour"] available for orders up to <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"cs_number"								 =>	 '855 289 6014',
			"retailer_url"					   =>  'https://www.afterpay.com/for-retailers',
		),
	    "CAD" => array(
			"name"                     =>  'CA',
			"product_page"             =>  'or 4 interest-free payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"]</a> <a href="#afterpay-what-is-modal" target="_blank"><span style="font-size:12px"><u>More info</u></span></a>',
			"category_page"            =>  'or 4 payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with Afterpay',
			"product_variant"          =>  'or 4 payments of <strong>[AMOUNT]</strong> with Afterpay',
			"cart_page"                =>  '<tr><td colspan="100%">or 4 interest-free payments of <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"]</a> <a href="#afterpay-what-is-modal" target="_blank"><span style="font-size:12px"><u>More info</u></span></a></td></tr>',
			"checkout_page_cta"        =>  'Four interest-free payments totalling',
			"checkout_page_first_step" =>  'First Instalment',
			"fallback_asset"           =>  '[afterpay_product_logo theme="colour"] available between <strong>[MIN_LIMIT]</strong> - <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"><span style="font-size:12px"><u>Learn More</u></span></a>',
			"fallback_asset_2"         =>  '[afterpay_product_logo theme="colour"] available for orders up to <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"><span style="font-size:12px"><u>Learn More</u></span></a>',
			"cs_number"							   =>  '833 386 0210',
			"retailer_url"					   =>  'https://www.afterpay.com/en-CA/for-retailers',
		),
		"AUD" => array(
			"name"                     =>  'AU',
			"product_page"             =>  'or 4 fortnightly payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a>',
			"category_page"            =>  'or 4 payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with Afterpay',
			"product_variant"          =>  'or 4 payments of <strong>[AMOUNT]</strong> with Afterpay',
			"cart_page"                =>  '<tr><td colspan="100%">or 4 fortnightly payments of <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a></td></tr>',
			"checkout_page_cta"        =>  'Four interest-free payments totalling',
			"checkout_page_first_step" =>  'First Instalment',
			"fallback_asset"           =>  '[afterpay_product_logo theme="colour"] available between <strong>[MIN_LIMIT]</strong> - <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"fallback_asset_2"         =>  '[afterpay_product_logo theme="colour"] available for orders up to <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"cs_number"							   =>  '1300 100 729',
			"retailer_url"					   =>  'https://www.afterpay.com/en-AU/business',
		),
		"NZD" => array(
			"name"                     =>  'NZ',
			"product_page"             =>  'or 4 fortnightly payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a>',
			"category_page"            =>  'or 4 payments [OF_OR_FROM] <strong>[AMOUNT]</strong> with Afterpay',
			"product_variant"          =>  'or 4 payments of <strong>[AMOUNT]</strong> with Afterpay',
			"cart_page"                =>  '<tr><td colspan="100%">or 4 fortnightly payments of <strong>[AMOUNT]</strong> with <a href="#afterpay-what-is-modal" target="_blank">[afterpay_product_logo theme="colour"] <span style="font-size:12px"><u>More info</u></span></a></td></tr>',
			"checkout_page_cta"        =>  'Four interest-free payments totalling',
			"checkout_page_first_step" =>  'First Instalment',
			"fallback_asset"           =>  '[afterpay_product_logo theme="colour"] available between <strong>[MIN_LIMIT]</strong> - <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"fallback_asset_2"         =>  '[afterpay_product_logo theme="colour"] available for orders up to <strong>[MAX_LIMIT]</strong> <a href="#afterpay-what-is-modal" target="_blank"> <span style="font-size:12px"><u>Learn More</u></span></a>',
			"cs_number"				   			 =>  '0800 461 268',
			"retailer_url"			   		 =>  'https://www.afterpay.com/en-NZ/business',
		),
	);

	$currency =	get_option('woocommerce_currency');

	$region_assets = array_key_exists($currency, $assets) ? $assets[$currency] : $assets['AUD'];

	return array_merge($global_assets, $region_assets);
};

return $get_afterpay_assets();
?>
