<?php
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10, 0);
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
  
    remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout',20);
    remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals');

    add_action('woocommerce_after_single_product_summary', 'magikCreta_related_upsell_products', 15);
    add_action('woocommerce_after_shop_loop_item_title', 'magikCreta_woocommerce_rating', 5);
    add_action('woocommerce_before_shop_loop', 'magikCreta_grid_list_trigger', 11);
  

    
    add_action('woocommerce_proceed_to_checkout', 'magikCreta_woocommerce_button_proceed_to_checkout');
    add_action('init','magikCreta_woocommerce_clear_cart_url');
     add_action('magikCreta_single_product_pagination', 'magikCreta_single_product_prev_next');
    add_filter('woocommerce_breadcrumb_defaults','magikCreta_woocommerce_breadcrumbs');

    add_filter('woocommerce_add_to_cart_fragments', 'magikCreta_woocommerce_header_add_to_cart_fragment');
    add_filter('loop_shop_per_page', 'magikCreta_loop_product_per_page');

    add_filter( 'woocommerce_cross_sells_total', 'magikCreta_woocommerce_cross_sells_total_limit' );


  // new features
     add_action( 'woocommerce_product_options_advanced', 'magikCreta_woocommerce_general_product_data_custom_field' );

    add_action( 'woocommerce_process_product_meta', 'magikCreta_woocommerce_process_product_meta_fields_save' );

  remove_action('woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open');

function magikCreta_related_upsell_products()
{
    global $product,$creta_Options;

    if (isset($product) && is_product())
     {
     ?>
  <?php if (isset($creta_Options['enable_home_related_products']) && !empty($creta_Options['enable_home_related_products']) ) 
     { ?>
 
  <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
{?>
  </div>
  <div class="box-additional">  
<?php } ?>
              <!-- Related Products Slider -->
   <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
{?>
   <!-- Related Slider -->
  <div class="related-pro">

      <div class="slider-items-products">
        <div class="related-block">
          <div id="related-products-slider" class="product-flexslider hidden-buttons">
            <div class="home-block-inner">
              <div class="block-title">
                <h2><?php esc_attr_e('Related', 'creta'); ?><br>
                         <em> <?php esc_attr_e('Products', 'creta'); ?></em>
                  </h2>
               
              </div>
              <div class="pretext">
                 <?php echo wp_specialchars_decode($creta_Options['related_products-text']);?>
               
              </div>  
              <a class="view_more_bnt" href="<?php echo !empty($creta_Options['enable_home_related_products']) ? esc_url($creta_Options['related_product_url']) : '#' ?>"><?php esc_attr_e('VIEW ALL ','creta'); ?></a>  
             </div>

              <div class="slider-items slider-width-col4 products-grid block-content">
              <?php } else{ ?>
              
             
            <!-- BEGIN CATEGORY PRODUCTS -->
            <div>
              <div class="slider-items-products">
                <div class="new_title">
                  <h2><?php esc_attr_e('Related Products', 'creta'); ?></h2>
                </div>
                <div id="related-products-slider" class="product-flexslider hidden-buttons">
                  <div class="slider-items slider-width-col4 products-grid">
                    <?php } ?>
          <?php
                            $related = wc_get_related_products(6);
                            $args = apply_filters('woocommerce_related_products_args', array(
                                'post_type' => 'product',
                                'ignore_sticky_posts' => 1,
                                'no_found_rows' => 1,
                                'posts_per_page' => $creta_Options['related_per_page'],
                                'orderby' => 'rand',
                                'post__in' => $related,
                                'post__not_in' => array($product->get_id())
                            ));

                            $loop = new WP_Query($args);
                            if ($loop->have_posts()) {
                                while ($loop->have_posts()) : $loop->the_post();
                                   magikCreta_related_upsell_template();
                                endwhile;
                            } else {
                                esc_attr_e('No products found', 'creta');
                            }

                            wp_reset_postdata();
                            ?>
          <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
              </div>
          </div>
        </div>
      </div>
  </div>

          <?php } else{ ?>
        </div>
      </div>

  </div>
</div> <?php }
}
 ?>
  <!-- End related products Slider -->
       
            
<?php
      if (isset($creta_Options['enable_home_upsell_products']) && !empty($creta_Options['enable_home_upsell_products']) ) 
     { 


        $upsells = $upsells = $product->get_upsell_ids();

        if (sizeof($upsells) == 0) {
        } else {
            ?>

  <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
{?>          
  <!-- Upsell Product Slider -->
  
 
     <!-- upsell Slider -->
  <div class="upsell-pro">
      <div class="slider-items-products">
        <div class="upsell-block">
          <div id="upsell-products-slider" class="product-flexslider hidden-buttons">
            <div class="home-block-inner">
              <div class="block-title">
                <h2><?php esc_attr_e('Upsell', 'creta'); ?><br>
                         <em> <?php esc_attr_e('Products', 'creta'); ?></em>
                  </h2>
                 
              </div>
              <div class="pretext">
                <?php echo wp_specialchars_decode($creta_Options['upsell_products-text']);?>
               
              </div>
              <a class="view_more_bnt" href="<?php echo !empty($creta_Options['enable_home_upsell_products']) ? esc_url($creta_Options['upsell_product_url']) : '#' ?>"><?php esc_attr_e('VIEW ALL ','creta'); ?></a>
            </div>
            <div class="slider-items slider-width-col4 products-grid block-content">
        <?php } else{ ?>
        <div class="upsell-pro wow bounceInUp animated">
              <div>
                <div class="slider-items-products"> </div>
                <!--new_title center-->
                <div id="upsell-products" class="product-flexslider hidden-buttons">
                  <div class="container">
                    <div class="slider-items-products">
                      <div class="new_title">
                        <h2><?php esc_attr_e('Upsell Products', 'creta'); ?></h2>
                      </div>
                      <div id="upsell-products-slider" class="product-flexslider hidden-buttons">
                        <div class="slider-items slider-width-col4 products-grid">
                <?php } ?>
          <?php

                                $meta_query = WC()->query->get_meta_query();

                                $args = array(
                                    'post_type' => 'product',
                                    'ignore_sticky_posts' => 1,
                                    'no_found_rows' => 1,
                                    'posts_per_page' => $creta_Options['upsell_per_page'],
                                    'orderby' => 'rand',
                                    'post__in' => $upsells,
                                    'post__not_in' => array($product->get_id()),
                                    'meta_query' => $meta_query
                                );


                                $loop = new WP_Query($args);
                                if ($loop->have_posts()) {
                                    while ($loop->have_posts()) : $loop->the_post();
                                       magikCreta_related_upsell_template();
                                    endwhile;
                                } else {
                                    esc_attr_e('No products found', 'creta');
                                }

                                wp_reset_postdata();
                                ?>
<?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
        </div>
          </div>
        </div>
      </div>
  
  </div>
 
  <?php } else{ ?>
                         </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
                   
          <?php }
        }

           ?>
<?php
        }

         //end of else
        ?>

   <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
{?>
</div>
<?php } ?>
<?php

    }
}

function magikCreta_woocommerce_rating()
{
    global $wpdb, $post;

    $count = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(meta_value) FROM $wpdb->commentmeta
        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
        WHERE meta_key = '%s'
        AND comment_post_ID = %d
        AND comment_approved = %d
        AND meta_value > %d
    ", 'rating', $post->ID, 1, 0));

    $rating = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(meta_value) FROM $wpdb->commentmeta
        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
        WHERE meta_key = '%s'
        AND comment_post_ID = %d
        AND comment_approved = %d
    ", 'rating', $post->ID, 1));

    if ($count > 0) {

        $average = number_format($rating / $count, 2);

        echo '<div class="ratings" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';

        echo '<div class="rating-box">';

        echo '<div class="rating" title="' . sprintf(esc_html__('Rated %s out of 5', 'woocommerce'), esc_html($average)) . '"  style="width:' . esc_html($average * 16) . 'px"><span itemprop="ratingValue" class="rating">' . esc_html($average) . '</span></div>';

        echo '</div></div>';
    }


}


function magikCreta_grid_list_trigger()
{
    ?>

<div class="sorter">
  <div class="view-mode"><a href="#" class="grid-trigger button-active button-grid"></a> <a href="#" title="<?php esc_attr_e('List', 'creta'); ?>" class="list-trigger  button-list"></a></div>
</div>
<?php

}

function magikCreta_woocommerce_add_to_compare()
{
    global $product, $woocommerce_loop, $yith_wcwl;
    if (class_exists('YITH_Woocompare_Frontend')) {

        $mgk_yith_cmp = new YITH_Woocompare_Frontend;
          $mgk_yith_cmp->add_product_url($product->get_id());
         ?>
<li class="pull-left-none"><a class="compare add_to_compare_small link-compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
           href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" title="<?php esc_attr_e('Add to Compare','creta'); ?>"><i class="fa fa-signal icons"></i><?php esc_attr_e('Add to Compare','creta'); ?>"></a>
<?php
    }
}



function magikCreta_woocommerce_add_to_whishlist()
{
    global $product, $woocommerce_loop, $yith_wcwl;

    if (isset($yith_wcwl) && is_object($yith_wcwl)) {
        $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
        ?>
 <li class="pull-left-none"><a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"
           data-product-id="<?php echo esc_html($product->get_id()); ?>"
           data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
           title="<?php esc_attr_e('Add to WishList','creta'); ?>"><i class="fa fa-heart-o icons"></i><?php esc_attr_e('Add to WishList','creta'); ?></a></li>
<?php
    }
}


function magikCreta_woocommerce_button_proceed_to_checkout()
{
    $checkout_url = wc_get_checkout_url();
    ?>
    <a href="<?php echo esc_url($checkout_url); ?>" class="button btn-proceed-checkout">
        <span><?php esc_attr_e('Proceed to Checkout', 'creta'); ?></span></a>
<?php
}

function magikCreta_woocommerce_clear_cart_url()
{
    global $woocommerce;
    if (isset($_REQUEST['clear-cart'])) {
        $woocommerce->cart->empty_cart();
    }
}

//Filter function are here
/* Breadcrumbs */

function magikCreta_woocommerce_breadcrumbs()
{
    return array(
        'delimiter' => '',
        'wrap_before' => '<ul>',
        'wrap_after' => '</ul>',
        'before' => '<li>',
        'after' => '<span> &frasl; </span></li>',
        'home' => _x('Home', 'breadcrumb', 'woocommerce'),
    );
}

function magikCreta_single_product_prev_next()
  {

    global $woocommerce, $post;

    if (!isset($woocommerce) or !is_single())
      return;
    ?>
  <div id="prev-next" class="product-next-prev">
    <?php
      $next =magikCreta_prev_next_product_object($post->ID);

      if (!empty($next)):
        ?>
    <a href="<?php echo esc_url(get_permalink($next->ID)) ?>" class="product-next"><span></span></a>
    <?php
      endif;

      $prev = magikCreta_prev_next_product_object($post->ID, 'prev');
      if (!empty($prev)):
        ?>
    <a href="<?php echo esc_url(get_permalink($prev->ID)) ?>" class="product-prev"><span></span></a>
    <?php
      endif;
      ?>
  </div>
  <!--#prev-next -->

  <?php
  }
function magikCreta_prev_next_product_object($postid, $dir = 'next')
  {

    global $wpdb;

    if ($dir == 'prev')
      $sql = $wpdb->prepare("SELECT * FROM $wpdb->posts where post_type = '%s' AND post_status = '%s' and ID < %d order by ID desc limit 0,1", 'product', 'publish', $postid);
    else
      $sql = $wpdb->prepare("SELECT * FROM $wpdb->posts where post_type = '%s' AND post_status = '%s' and ID > %d order by ID desc limit 0,1", 'product', 'publish', $postid);

    $result = $wpdb->get_row($sql);

    if (!is_wp_error($result)):
      if (!empty($result)):
        return $result;
      else:
        return false;
      endif;
    else:
      return false;
    endif;
  }


function magikCreta_woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce,$creta_Options;

ob_start();
?><div class="mini-cart">
   
    <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
     {?>
     <div class="basket"> <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
        <span> <?php echo esc_html($woocommerce->cart->cart_contents_count); ?> </span></a> </div>
     
     <?php } else{ ?>
           <div data-hover="dropdown"  class="basket dropdown-toggle">
      <a href="<?php echo esc_url(wc_get_cart_url()); ?>"> 
        <span class="cart_count"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?> </span>
        <span class="price"><?php  esc_attr_e('My Cart','creta'); ?> /
          <?php echo wp_specialchars_decode(WC()->cart->get_cart_subtotal()); ?></span> </a>
           </div>
     <?php } ?>
      
 
            <div class="top-cart-content">
               <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
     {?>
                <div class="block-subtitle">
                <div class="top-subtotal"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?>  <?php  esc_attr_e('items','creta'); ?> , <span class="price"><?php echo wp_specialchars_decode(WC()->cart->get_cart_subtotal()); ?></span> </div>
                 </div>
                <!--top-subtotal--> 
                <?php } ?>
             
                   
         <?php if (sizeof(WC()->cart->get_cart()) > 0) : $i = 0; ?>
         <ul class="mini-products-list" id="cart-sidebar" >
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
            <?php
               $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
               $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
               
               if ($_product && $_product->exists() && $cart_item['quantity'] > 0
                   && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)
               ) :
               
                  $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                 $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                   $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
          $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                   $cnt = sizeof(WC()->cart->get_cart());
                   $rowstatus = $cnt % 2 ? 'odd' : 'even';
                   ?>
            <li class="item<?php if ($cnt - 1 == $i) { ?>last<?php } ?>">
              <div class="item-inner">
               <a class="product-image"
                  href="<?php echo esc_url($product_permalink); ?>"  title="<?php echo wp_specialchars_decode($product_name); ?>"> <?php echo str_replace(array('http:', 'https:'), '', wp_specialchars_decode($thumbnail)); ?> </a>
             

                  <div class="product-details">
                       <div class="access">
                        <a class="btn-edit" title="<?php esc_attr_e('Edit item','creta') ;?>"
                        href="<?php echo esc_url(wc_get_cart_url()); ?>"><i
                        class="icon-pencil"></i><span
                        class="hidden"><?php esc_attr_e('Edit item','creta') ;?></span></a>
                       <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
                        title="<?php esc_attr_e('Remove This Item','creta') ;?>" onClick="" 
                        class="btn-remove1"><?php esc_attr_e('Remove','creta') ;?></a> 

                         </div>
                      <strong><?php echo esc_html($cart_item['quantity']); ?>
                  </strong> x <span class="price"><?php echo wp_specialchars_decode($product_price); ?></span>
                     <p class="product-name"><a href="<?php echo esc_url($product_permalink); ?>"
                        title="<?php echo wp_specialchars_decode($product_name); ?>"><?php echo wp_specialchars_decode($product_name); ?></a> </p>
                  </div>
                   <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                     </div>
              
            </li>
            <?php endif; ?>
            <?php $i++; endforeach; ?>
         </ul> 
         <!--actions-->
                    
         <div class="actions">
                      <button class="btn-checkout" title="<?php esc_attr_e('Checkout','creta') ;?>" type="button" 
                      onClick="window.location.assign('<?php echo esc_js(wc_get_checkout_url()); ?>')">
                      <span><?php esc_attr_e('Checkout','creta') ;?></span> </button>

                       <?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
                       {?>
                      <a class="view-cart" type="button"
                     onClick="window.location.assign('<?php echo esc_js(wc_get_cart_url()); ?>')">
                     <span><?php esc_attr_e('View Cart','creta') ;?></span> </a>
                     <?php } ?>
          
         </div>   
         
         <?php else:?>
         <p class="a-center noitem">
            <?php esc_attr_e('Sorry, nothing in cart.', 'creta');?>
         </p>
         <?php endif; ?>
      
   </div>
 </div>

<?php

 $fragments['.mini-cart'] = ob_get_clean();

return $fragments;

}

function magikCreta_loop_product_per_page() {
    global $creta_Options;

    
    // replace it with theme option
    if (isset($creta_Options['category_item']) && !empty($creta_Options['category_item'])) {
        $per_page = explode(',', $creta_Options['category_item']);
    } else {
        $per_page = explode(',', '9,18,27');
    }

    $item_count = !empty($params['count']) ? $params['count'] : $per_page[0];

    return $item_count;
}

//cross sells limit
function magikCreta_woocommerce_cross_sells_total_limit() {
    global $creta_Options;
    if (isset($creta_Options['enable_cross_sells_products']) && !empty($creta_Options['enable_cross_sells_products'])) {
       $cross_sells_per_page = $creta_Options['cross_per_page'];
      return $cross_sells_per_page;
     }
}





// new feature code
// Display Fields using WooCommerce Action Hook
function magikCreta_woocommerce_general_product_data_custom_field() {
   global $woocommerce, $post;
   
    woocommerce_wp_checkbox(
                array(
                    'id' => 'hotdeal_on_productpage',
                    'wrapper_class' => 'checkbox_class',
                    'label' => esc_html__('Hot deal on Product Detail Page', 'creta' ),
                    'description' => esc_html__( 'Tick checkbox to set product as hot deal on Product Detail Page', 'creta' )
                )
            );

    woocommerce_wp_checkbox(
                array(
                    'id' => 'hotdeal_on_home',
                    'wrapper_class' => 'checkbox_class',
                    'label' => esc_html__('Hot deal On Home Page', 'creta' ),
                    'description' => esc_html__( 'Tick checkbox to set product as hot deal on home page', 'creta' )
                )
            );
    
}

// Save Fields using WooCommerce Action Hook
function magikCreta_woocommerce_process_product_meta_fields_save( $post_id ){
    global  $woo_checkbox;
    $woo_checkbox = isset( $_POST['hotdeal_on_productpage'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'hotdeal_on_productpage', $woo_checkbox );

       $woo_checkbox1 = isset( $_POST['hotdeal_on_home'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'hotdeal_on_home', $woo_checkbox1 );
}


 add_action('woocommerce_single_product_summary', 'magikCreta_hot_detail_on_detailpage', 15);

 function magikCreta_hot_detail_on_detailpage()
 {

global $product , $creta_Options;

if (isset($creta_Options['enable_home_hotdeal_on_products_page']) && !empty($creta_Options['enable_home_hotdeal_on_products_page'])) { 
   $hotdeal_value = get_post_meta($product->get_id(), 'hotdeal_on_productpage', true);
if($hotdeal_value=="yes" && $product->is_on_sale())
{ 
  $product_type = $product->get_type();
            
              if($product_type=='variable')
              {
               $available_variations = $product->get_available_variations();
             
               $variation_id=$available_variations[0]['variation_id'];
                $newid=$variation_id;
              }
              else
              {
                $newid=$product->get_id();
           
              }
                $sales_price_start =  ( $date = get_post_meta( $newid, '_sale_price_dates_from', true ) ) ? date_i18n( 'Y-m-d', $date ) : ''; 
                $sales_price_start=strtotime($sales_price_start);

                $new_end_date =  ( $date = get_post_meta( $newid, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d', $date ) : ''; 
               
                $sales_price_end=strtotime($new_end_date);
            
               $curdate=strtotime(date('Y-m-d H:i:s'));

                $sales_price_to = get_post_meta($newid, '_sale_price_dates_to', true);  
               if(!empty($sales_price_to) && $sales_price_start <= $curdate  && $sales_price_end >= $curdate)
               {
              $sales_price_date_to = date("Y/m/d H:i:s", strtotime($new_end_date));
               
              
               ?>
               <div class="product-timer-box">
                <div class="box-timer">
                  <div class="countbox_2 timer-grid"  data-time="<?php echo esc_html($sales_price_date_to) ;?>">
                </div>
            </div>
            </div>
            <?php
          }
}
}
}


function magikCreta_woocommerce_category_footer_content()
{
    global $product, $creta_Options;

    if (is_product_category()) {
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        $magik_footer_content = get_woocommerce_term_meta($cat->term_id, 'magik_footer_content', true);
      
        if ($magik_footer_content !="") {
            echo '<div class="magik-category-footer-description">'.$magik_footer_content.'</div>';
        }
    }
}

 ?>