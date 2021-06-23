<?php

//These are the endpoints that will be appended to the api_url, which is selected on the gateway settings page.

$endpoints =   array
(
	"v1" => array
			(
			"configuration"   => "v1/configuration",
			"orders" 		  => "v1/orders",
			"payments"		  => "v1/payments",
			"capture-payment" => "v1/payments/capture"
			)
);
?>
