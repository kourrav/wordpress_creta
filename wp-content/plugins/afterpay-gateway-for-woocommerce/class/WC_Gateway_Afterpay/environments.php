<?php
//These are the environments used by the Afterpay gateway for WooCommerce

return array(
	"sandbox" => array(
		"name"       =>  "Sandbox",
		"api_url"    =>  "https://api-sandbox.afterpay.com/",
		"web_url"    =>  "https://portal.sandbox.afterpay.com/",
		"api_us_url" =>  "https://api.us-sandbox.afterpay.com/",
		"web_us_url" =>  "https://portal.sandbox.afterpay.com/",
		"static_url" =>  "https://static.sandbox.afterpay.com/"
	),
	"production" => array(
		"name"       => "Production",
		"api_url"    => "https://api.afterpay.com/",
		"web_url"    => "https://portal.afterpay.com/",
		"api_us_url" => "https://api.us.afterpay.com/",
		"web_us_url" => "https://portal.afterpay.com/",
		"static_url" =>  "https://static.afterpay.com/"
	)
);
