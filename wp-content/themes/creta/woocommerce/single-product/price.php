<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

?>
<div class="price-block">
 <div class="price-box price"><?php echo wp_specialchars_decode($product->get_price_html()); ?></div>
       <?php if($product->is_in_stock()){ ?><p class="availability in-stock pull-right"><span> <?php esc_attr_e('In Stock','creta');?></span></p> <?php  }else{ ?> <p class="availability out-of-stock pull-right"><span><?php esc_attr_e('Out of Stock','creta');?></span></p><?php }  ?>  
</div>


