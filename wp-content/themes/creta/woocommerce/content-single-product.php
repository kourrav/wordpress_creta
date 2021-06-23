<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

defined( 'ABSPATH' ) || exit;



if ( post_password_required() ) {
  echo get_the_password_form(); // WPCS: XSS ok.
  return;
}
?>

<?php
global $creta_Options;
?>

<div class="main-container col1-layout">
    <div class="main">

     <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
      {?>

      <div class="container">
      <div class="row"> 
      <?php } ?>    

    <div class="col-main">
       
        <div class="product-view <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
                       {?> container  version2 <?php  } ?>">

                <?php /**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

?>             
          
      <div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>

          <div class="product-essential">
             <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
                       {?>
            <div class="row">

             <?php  do_action('magikCreta_single_product_pagination'); ?>

            
                <?php } ?>

                   <?php
                 /**
                 * woocommerce_before_single_product hook
                 *
                 * @hooked wc_print_notices - 10
                 */

                 do_action( 'woocommerce_before_single_product' );
                 ?>

            <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>

             <div class="product-img-box col-lg-4 col-sm-5 col-xs-12">
                <?php } else{?>
                  <div class="product-img-box col-sm-5 col-xs-12">

                <?php }?>
        
            <?php
                        /**
                         * woocommerce_before_single_product_summary hook
                         *
                         * @hooked woocommerce_show_product_sale_flash - 10
                         * @hooked woocommerce_show_product_images - 20
                         */
                        do_action('woocommerce_before_single_product_summary');
                        ?>
         </div>

          <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
              <div class="product-shop col-lg-8 col-sm-7 col-xs-12">
                 <?php  do_action('magikCreta_single_product_pagination'); ?>
                <?php } else{ ?>
                  <div class="product-shop col-sm-7 col-xs-12">

                <?php } ?>
                       <?php
                                    /**
                                     * woocommerce_single_product_summary hook
                                     *
                                     * @hooked woocommerce_template_single_title - 5
                                     * @hooked woocommerce_template_single_rating - 10
                                     * @hooked woocommerce_template_single_price - 10
                                     * @hooked woocommerce_template_single_excerpt - 20
                                     * @hooked woocommerce_template_single_add_to_cart - 30
                                     * @hooked woocommerce_template_single_meta - 40
                                     * @hooked woocommerce_template_single_sharing - 50
                                     */
                                    do_action('woocommerce_single_product_summary');

                                    ?>

      
            </div>
            
             
               
             <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
                   </div> <!-- #productess;-->
                   </div> <!-- ##product-id;; -->
                   </div> <!--  -->
                   </div>
                   <?php } ?>
            <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
                       {?>
                   </div><!--  row -->
                   </div> <!-- #productess;-->
                   </div> <!-- ##product-id;; -->
                   <?php } ?>
 
              <div class="product-collateral <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?> col-lg-12 col-sm-12 col-xs-12<?php  } ?>">
          
              <?php
                            /**
                             * woocommerce_after_single_product_summary hook
                             *
                             * @hooked woocommerce_output_product_data_tabs - 10
                             * @hooked woocommerce_upsell_display - 15
                             * @hooked woocommerce_output_related_products - 20
                             */

                            do_action('woocommerce_after_single_product_summary');
                            ?>
                           <meta itemprop="url" content="<?php the_permalink(); ?>"/>
                           
           
  

  

    <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
                     </div>  
                   </div>
                   </div>
                   <?php } ?>

            <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
                       {?>
                     
                  
                   </div>
                   </div>
                   <?php } ?>
           


</div>
</div>


<?php do_action('woocommerce_after_single_product'); ?>
