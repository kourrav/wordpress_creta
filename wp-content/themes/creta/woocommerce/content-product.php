<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$MagikCreta = new MagikCreta();
global $product, $woocommerce_loop, $yith_wcwl,$creta_Options;



// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
  return;
}


 $shop_design = isset($_GET['layout']) ? $_GET['layout'] : '';
if (!in_array($shop_design, array('full','left','right'))) {
  
$layout_array=array(1=>"left",2=>"right",3=>"full");  
$page_id = wc_get_page_id('shop');
$design = get_post_meta($page_id ,'magikCreta_page_layout', true);

if(isset($design) && !empty($design))
{
$shop_design = $layout_array[$design];
}
else
{
$shop_design="left"; 
}
}


// Increase loop count


// Extra post classes
$classes = array();
if (is_cart()) {

    $classes[] = 'item col-md-3 col-sm-6 col-xs-12';

} else {
  
   if($shop_design=="full")
   {
    $classes[] = 'item col-lg-3 col-md-3 col-sm-3 col-xs-3';
   }
   else{
$classes[] = 'item col-lg-4 col-md-4 col-sm-4 col-xs-6';
   }
}




?>

  <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
{      
?> 
<li <?php wc_product_class($classes); ?>>
  <div class="item-inner">
      <div class="images-container">
        <div class="product-hover">
        <div class="pimg">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>        
            <a href="<?php the_permalink(); ?>" class="product-image">
              
                  <?php 
                     /**
                      * woocommerce_before_shop_loop_item_title hook
                      *
                      * @hooked woocommerce_show_product_loop_sale_flash - 10
                      * @hooked woocommerce_template_loop_product_thumbnail - 10
                      */
                     do_action('woocommerce_before_shop_loop_item_title');
                     ?>
             
            </a>
             <?php if ($product->is_on_sale()) : ?>

    <?php echo apply_filters('woocommerce_sale_flash', ' <div class="new-label new-top-left">' . esc_html__('Sale', 'woocommerce') . '</div>', $post, $product); ?>

<?php endif; ?>
           </div>
           </div>
               <div class="actions-no hover-box">
              <a class="detail_links" href="<?php the_permalink(); ?>"></a>
               <div class="actions">
               <ul class="add-to-links pull-left-none">                 
           
                 <?php
                  if (isset($yith_wcwl) && is_object($yith_wcwl)) {
        $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
        ?>
<li class="pull-left-none"><a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"
           data-product-id="<?php echo esc_html($product->get_id()); ?>"
           data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
           title="<?php esc_attr_e('Add to WishList','creta'); ?>"><i class="fa fa-heart-o icons"></i></a>
         </li>
<?php
    }
    ?>
  <?php
      if (class_exists('YITH_Woocompare_Frontend')) {

        $mgk_yith_cmp = new YITH_Woocompare_Frontend;
          $mgk_yith_cmp->add_product_url($product->get_id());
         ?>
 <li class="pull-left-none"><a class="compare add_to_compare_small link-compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
           href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" title="<?php esc_attr_e('Add to Compare','creta'); ?>"><i class="fa fa-signal icons"></i></a></li>
<?php
    }
     ?>

        <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                 <li class="link-view pull-left-none">
                  <div class="product-detail-bnt">
                  
                  <a title="<?php esc_attr_e('Quick View', 'creta'); ?>" class="yith-wcqv-button quickview link-quickview button detail-bnt" data-product_id="<?php echo esc_html($product->get_id()); ?>"><span><?php esc_attr_e('Quick View', 'creta'); ?></span></a>
                </div> </li>


                  <?php } ?>
                          
             </ul>
                  </div>
                  <div class="actions-cart">
                   <?php
                  /**
                   * woocommerce_after_shop_loop_item hook
                   *
                   * @hooked woocommerce_template_loop_add_to_cart - 10
                   */
                  do_action('woocommerce_after_shop_loop_item');
                  
                  ?>    
                  </div>
                      </div>
                      </div>
                    <div class="item-info">
                      <div class="info-inner">
                      <div class="item-title">
                 <a href="<?php the_permalink(); ?>">
               <?php the_title(); ?></a> 
                 </div>
                        <div class="item-content">
                          <div class="rating">
                            <div class="ratings">
                              <div class="rating-box">

                          <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating">
                        </div>

                     </div>
                       </div>
                          </div>
                          <div class="item-price">
                            <div class="price-box">

                                <?php echo wp_specialchars_decode($product->get_price_html()); ?>

                         </div>
                          </div>
                          <div class="desc std">
                         <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?>
                          </div>
                          </div>
                          </div>
                        </div>
                   
                       </div>
                    
                </li>


<?php } else{ ?>  

<li <?php wc_product_class($classes); ?>>
  <div class="item-inner">
                    <div class="item-img">
                      <div class="item-img-info">
                        <div class="pimg">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>        
            <a href="<?php the_permalink(); ?>" class="product-image">
              
                  <?php
                     /**
                      * woocommerce_before_shop_loop_item_title hook
                      *
                      * @hooked woocommerce_show_product_loop_sale_flash - 10
                      * @hooked woocommerce_template_loop_product_thumbnail - 10
                      */
                     do_action('woocommerce_before_shop_loop_item_title');
                     ?>
             
            </a>
            <?php if ($product->is_on_sale()) : ?>

    <?php echo apply_filters('woocommerce_sale_flash', ' <div class="new-label new-top-left">' . esc_html__('Sale', 'woocommerce') . '</div>', $post, $product); ?>

<?php endif; ?>

          </div>
                    <div class="box-hover">
                          <ul class="add-to-links">

                    <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                  <li>
                  
                  <a title="<?php esc_attr_e('Quick View', 'creta'); ?>" class="yith-wcqv-button quickview link-quickview" data-product_id="<?php echo esc_html($product->get_id()); ?>"><?php esc_attr_e('Quick View', 'creta'); ?></a></li>
                  <?php } ?>
                          

                  <?php
                  if (isset($yith_wcwl) && is_object($yith_wcwl)) {
        $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
        ?>
 <li><a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"
           data-product-id="<?php echo esc_html($product->get_id()); ?>"
           data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
           title="<?php esc_attr_e('Add to WishList','creta'); ?>"><?php esc_attr_e('Add to WishList','creta'); ?></a></li>
<?php
    }
    ?>
    <?php
      if (class_exists('YITH_Woocompare_Frontend')) {

        $mgk_yith_cmp = new YITH_Woocompare_Frontend;
          $mgk_yith_cmp->add_product_url($product->get_id());
         ?>
<li><a class="compare add_to_compare_small link-compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
           href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" title="<?php esc_attr_e('Add to Compare','creta'); ?>"><?php esc_attr_e('Add to Compare','creta'); ?></a></li>
<?php
    }
     ?>              
           
              </ul>
                        </div>
                      </div>
                    </div>
                    <div class="item-info">
                      <div class="info-inner">
                        <div class="item-title">
                               <a href="<?php the_permalink(); ?>">
               <?php the_title(); ?></a></div>

                <div class="item-content">
                          <div class="rating">
                            <div class="ratings">
                              <div class="rating-box">
                               <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating"></div>

                        </div>
                        </div>
                          </div>
                          <div class="item-price">
                            <div class="price-box">
                                <?php echo wp_specialchars_decode($product->get_price_html()); ?>

                         </div>
                          </div>
                          <div class="desc std">
                         <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?>
                              </div>
                          <div class="action">
                         <?php
                  /**
                   * woocommerce_after_shop_loop_item hook
                   *
                   * @hooked woocommerce_template_loop_add_to_cart - 10
                   */
                  do_action('woocommerce_after_shop_loop_item');
                  
                  ?>    
           </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>


<?php } ?>


                