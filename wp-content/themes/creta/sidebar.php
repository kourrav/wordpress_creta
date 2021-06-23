<?php
/**
 * @package creta
 * @subpackage creta
 */

if(class_exists( 'WooCommerce' ) && is_woocommerce()){
	dynamic_sidebar( 'sidebar-shop' );
} else {
	dynamic_sidebar( 'sidebar-blog' );
}
?> 

