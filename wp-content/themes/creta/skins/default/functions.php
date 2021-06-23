<?php

if(!function_exists('magikCreta_currency_language'))
{
function magikCreta_currency_language()
{ 
     global $creta_Options;


        if(isset($creta_Options['enable_header_language']) && ($creta_Options['enable_header_language']!=0))
        { ?>
          <div class="dropdown block-language-wrapper"> 
            <a role="button" data-toggle="dropdown" data-target="#" class="block-language dropdown-toggle" href="#"> 
              <img src="<?php echo esc_url(MAGIKCRETA_THEME_URI) ;?>/images/english.png" alt="<?php esc_attr_e('English', 'creta');?>">  
              <?php esc_attr_e('English', 'creta');?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><img src="<?php echo esc_url(MAGIKCRETA_THEME_URI) ;?>/images/english.png" alt="<?php esc_attr_e('English', 'creta');?>">    <?php esc_attr_e('English', 'creta');?></a></li>
              <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><img src="<?php echo esc_url(MAGIKCRETA_THEME_URI) ;?>/images/francais.png" alt="<?php esc_attr_e('French', 'creta');?>"> <?php esc_attr_e('French', 'creta');?> </a></li>
              <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><img src="<?php echo esc_url(MAGIKCRETA_THEME_URI) ;?>/images/german.png" alt="<?php esc_attr_e('German', 'creta');?>">   <?php esc_attr_e('German', 'creta');?></a></li>
            </ul>
          </div>
        <?php  
        } ?>
        
    <?php if(isset($creta_Options['enable_header_currency']) && ($creta_Options['enable_header_currency']!=0))
    { 

     global $wp,$wp_query;
     $cururl=magikCreta_curPageURL();
     if(class_exists('WOOCS'))
     {
       $usd_url =  add_query_arg( array("currency"=>"USD"), $cururl);
       $gbp_url =  add_query_arg( array("currency"=>"GBP"),  $cururl);

       $WOOCS=new WOOCS();

     $currency_selected=$WOOCS->current_currency;
     }
     else{
      
      $usd_url = "#";
      $gbp_url ="#";
      $currency_selected="USD";
    }

    if((isset($_GET['currency']) && $_GET['currency']=='GBP') || $currency_selected=='GBP')
    {
     
     $elected_currency='<a role="button" data-toggle="dropdown" data-target="#" class="block-currency dropdown-toggle" href="'. esc_url($gbp_url).'">'  
     . esc_attr("POUND", "creta").' <span class="caret"></span></a>';
     
   }
   else{
     $elected_currency='<a role="button" data-toggle="dropdown" data-target="#" class="block-currency dropdown-toggle" href="'.esc_url($usd_url).'">'  
     . esc_attr("USD", "creta").' <span class="caret"></span></a>';
   }


   ?>

   <div class="dropdown block-currency-wrapper"> 
     <?php echo wp_specialchars_decode($elected_currency);?>
     <ul class="dropdown-menu" role="menu">
      <li role="presentation">
        <a role="menuitem" tabindex="-1" href="<?php echo esc_url($usd_url);?>">
          <?php esc_attr_e('$ - Dollar', 'creta');?>
        </a>
      </li>
      
      <li role="presentation">
        <a role="menuitem" tabindex="-1" href="<?php echo esc_url($gbp_url) ;?>">
          <?php esc_attr_e('&#163; - Pound', 'creta');?>
        </a>
      </li>
    </ul>
  </div>
  <?php  
} 
}
}


if(!function_exists('magikCreta_msg'))
{
function magikCreta_msg()
{ 
     global $creta_Options;


           if (is_user_logged_in()) {
            global $current_user;
          
          if(isset($creta_Options['welcome_msg'])) {
            echo esc_attr_e('You are logged in as', 'creta'). '   <b>'. esc_attr($current_user->display_name) .'</b>';
          }
          }
          else{
            if(isset($creta_Options['welcome_msg']) && ($creta_Options['welcome_msg']!='')){
            echo wp_specialchars_decode($creta_Options['welcome_msg']);
            }
          } 
}
}

if(!function_exists('magikCreta_logo_image'))
{
function magikCreta_logo_image()
{ 
     global $creta_Options;
    
        $logoUrl = MAGIKCRETA_THEME_URI . '/images/logo.png';
        
        if (isset($creta_Options['header_use_imagelogo']) && $creta_Options['header_use_imagelogo'] == 0) {           ?>
        <a class="logo logotext logo-title" title="<?php bloginfo('name'); ?>" href="<?php echo esc_url(get_home_url()); ?> ">
        <?php bloginfo('name'); ?>
        </a>
        <?php
        } else if (isset($creta_Options['header_logo']['url']) && !empty($creta_Options['header_logo']['url'])) { 
                  $logoUrl = $creta_Options['header_logo']['url'];
                  ?>
        <a class="logo" title="<?php bloginfo('name'); ?>" href="<?php echo esc_url(get_home_url()); ?> "> <img
                      alt="<?php bloginfo('name'); ?>" src="<?php echo esc_url($logoUrl); ?>"
                      height="<?php echo !empty($creta_Options['header_logo_height']) ? esc_html($creta_Options['header_logo_height']) : ''; ?>"
                      width="<?php echo !empty($creta_Options['header_logo_width']) ? esc_html($creta_Options['header_logo_width']) : ''; ?>"> </a>
        <?php
        } else { ?>
        <a class="logo" title="<?php bloginfo('name'); ?>" href="<?php echo esc_url(get_home_url()); ?> "> 
          <img src="<?php echo esc_url($logoUrl) ;?>" alt="<?php bloginfo('name'); ?>"> </a>
        <?php }  

}
}

if(!function_exists('magikCreta_mobile_search'))
{
function magikCreta_mobile_search()
{ global $creta_Options;
  $MagikCreta = new MagikCreta();
    if (isset($creta_Options['header_remove_header_search']) && !$creta_Options['header_remove_header_search']) : 
        echo'<div class="mobile-search">';
         echo magikCreta_search_form();
         echo'<div class="search-autocomplete" id="search_autocomplete1" style="display: none;"></div></div>';
         endif;
}
}

if(!function_exists('magikCreta_search_form'))
{
 function magikCreta_search_form()
  {  
    global $creta_Options;
  $MagikCreta = new MagikCreta();
  ?>
  <?php if (isset($creta_Options['header_remove_header_search']) && !$creta_Options['header_remove_header_search']) : ?>
     <?php if(class_exists('MGK_Front_WooAjaxSearch') && isset($creta_Options['header_show_ajax_search']) && $creta_Options['header_show_ajax_search']==1)
      {

            
         echo do_shortcode('[mgk_woocommerce_ajax_search]');

               
      }
      else{?> 
            
  <div class="search-box">
<form name="myform"  method="GET" action="<?php echo esc_url(home_url('/')); ?>">
  <input type="text" placeholder="<?php esc_attr_e('Search entire store here...','creta');?>" maxlength="70" name="s" class="mgksearch" value="<?php echo get_search_query(); ?>">
         <?php if (class_exists('WooCommerce')) : ?>    
              <input type="hidden" value="product" name="post_type">
               <?php endif; ?>       
   <button class="search-btn-bg" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                   
  </form>
  </div>
   <?php } endif; ?>
  <?php
  }
}

if(!function_exists('magikCreta_home_page_banner'))
{
function magikCreta_home_page_banner()
{
    global $creta_Options;
     
        ?>
  <div id="magik-slideshow" class="magik-slideshow">
    <div class="container">
      <div class="row">
        <div class="col-md-9">
        <?php  if(isset($creta_Options['enable_home_gallery']) && $creta_Options['enable_home_gallery']  && isset($creta_Options['home-page-slider']) && !empty($creta_Options['home-page-slider'])) { ?>
        
        <div id='rev_slider_4_wrapper' class='rev_slider_wrapper fullwidthbanner-container'>
            <div id='rev_slider_4' class='rev_slider fullwidthabanner'>
            <ul>
                            <?php foreach ($creta_Options['home-page-slider'] as $slide) : ?>
                               <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='<?php echo esc_url($slide['thumb']); ?>'>
                                       <img
                                        src="<?php echo esc_url($slide['image']); ?>" data-bgposition='left top' data-bgfit='cover' data-bgrepeat='no-repeat'
                                        alt="<?php echo esc_attr($slide['title']); ?>"/> <?php echo wp_specialchars_decode($slide['description']); ?>
                                        <a class="s-link" href="<?php echo !empty($slide['url']) ? esc_url($slide['url']) : '#' ?>"></a>
                                </li>
                           
  <?php endforeach; ?>


   </ul>
            <div class="tp-bannertimer"></div>
          </div>
        </div>
    
      <?php } ?>
           </div>
         <div class="col-md-3 hot-deal">
        
              <?php magikCreta_hotdeal_product();?> 
            
        </div>
 
    
</div>
</div>

<!-- end Slider --> 

</div>

<?php 
   
}
}

if(!function_exists('magikCreta_header_service'))
{
function magikCreta_header_service()
{
    global $creta_Options;

if (isset($creta_Options['header_show_info_banner']) && !empty($creta_Options['header_show_info_banner'])) :
                  ?>
    <div class="our-features-box">
    <div class="container">

          <div class="row">
          <?php if (!empty($creta_Options['header_shipping_banner'])) : ?>
        <div class="col-lg-3 col-xs-12 col-sm-6">
          <div class="feature-box first"> <span class="fa fa-truck"></span>
            <div class="content">
              <h3><?php echo wp_specialchars_decode($creta_Options['header_shipping_banner']); ?></h3>
            </div>
          </div>
        </div>
         <?php endif; ?>
                 
         <?php if (!empty($creta_Options['header_customer_support_banner'])) : ?>
        <div class="col-lg-3 col-xs-12 col-sm-6">
          <div class="feature-box"> <span class="fa fa-headphones"></span>
            <div class="content">
              <h3><?php echo wp_specialchars_decode($creta_Options['header_customer_support_banner']); ?></h3>
            </div>
          </div>
        </div>
        <?php endif; ?>
                    
      <?php if (!empty($creta_Options['header_moneyback_banner'])) : ?>
        <div class="col-lg-3 col-xs-12 col-sm-6">
          <div class="feature-box"> <span class="fa fa-share"></span>
            <div class="content">
              <h3><?php echo wp_specialchars_decode($creta_Options['header_moneyback_banner']); ?></h3>
            </div>
          </div>
        </div>
         <?php endif; ?>
       <?php if (!empty($creta_Options['header_returnservice_banner'])) : ?>
        <div class="col-lg-3 col-xs-12 col-sm-6">
          <div class="feature-box last"> <span class="fa fa-phone"></span>
            <div class="content">
              <h3><?php echo wp_specialchars_decode($creta_Options['header_returnservice_banner']); ?></h3>
            </div>
          </div>
        </div>
         <?php endif; ?>
      </div>

       
        </div>
        </div>

    <?php
   
     endif;
}
}


if(!function_exists('magikCreta_home_sub_banners'))
{
function magikCreta_home_sub_banners()
{
     global $creta_Options;
   if (isset($creta_Options['enable_home_sub_banners']) && $creta_Options['enable_home_sub_banners']){
        ?>
<div class="bottom-banner-section">
  <div class="container">
      <div class="row">

                  <div class="col-md-4 col-sm-6">
                    <?php if (isset($creta_Options['home-sub-banner1']) &&  !empty($creta_Options['home-sub-banner1']['url']))
                  {?>
                   <div class="bottom-banner-img1">
                  <a href="<?php echo !empty($creta_Options['home-sub-banner1-url']) ? esc_url($creta_Options['home-sub-banner1-url']) : '#' ?>">
                    <img src="<?php echo esc_url($creta_Options['home-sub-banner1']['url']); ?>" 
                    alt="<?php esc_attr_e('offer banner', 'creta'); ?>"> 
                   
              <div class="bottom-img-info">
                  <h3><?php echo wp_specialchars_decode($creta_Options['home-sub-banner1-text']);?></h3>
                  <span class="line"></span>
               </div> </a> 
                    </div> 
                    <?php } ?>
                 </div>
                    

                    
                    <div class="col-md-4 col-sm-6"> 
                       <?php if (isset($creta_Options['home-sub-banner2']) &&  !empty($creta_Options['home-sub-banner2']['url']))
                  {?>                 
                     <div class="bottom-banner-img1">
                            <a href="<?php echo !empty($creta_Options['home-sub-banner2-url']) ? esc_url($creta_Options['home-sub-banner2-url']) : '#' ?>">
                                   <img src="<?php echo esc_url($creta_Options['home-sub-banner2']['url']); ?>"
                                         alt="<?php esc_attr_e('offer banner', 'creta'); ?>"> 
                          <div class="bottom-img-info">
                  <h3><?php echo wp_specialchars_decode($creta_Options['home-sub-banner2-text']);?></h3>
              <span class="line"></span>
               </div>
                                          </a> 
                        </div> 
                        <?php } ?>
                         </div>
                   
                      
                      <div class="col-md-4 col-sm-6"> 
                           <?php if (isset($creta_Options['home-sub-banner3']) &&  !empty($creta_Options['home-sub-banner3']['url']))
                  {?>
                          <div class="bottom-banner-img1">
                        <a href="<?php echo !empty($creta_Options['home-sub-banner3-url']) ? esc_url($creta_Options['home-sub-banner3-url']) : '#' ?>">
                                    <img src="<?php echo esc_url($creta_Options['home-sub-banner3']['url']); ?>"
                                         alt="<?php esc_attr_e('offer banner', 'creta'); ?>"> 
                                          <div class="bottom-img-info">
                  <h3><?php echo wp_specialchars_decode($creta_Options['home-sub-banner3-text']);?></h3>
              <span class="line"></span>
               </div> </a> 
                       </div>
                       <?php } ?>
                        </div>
                    
  
                <div class="col-md-4 col-sm-6">
                   <?php if (isset($creta_Options['home-sub-banner4']) &&  !empty($creta_Options['home-sub-banner4']['url']))
                  {?>
                 <div class="bottom-banner-img1">
                        <a href="<?php echo !empty($creta_Options['home-sub-banner4-url'])
                         ? esc_url($creta_Options['home-sub-banner4-url']) : '#' ?>">
                                    <img src="<?php echo esc_url($creta_Options['home-sub-banner4']['url']); ?>"
                                         alt="<?php esc_attr_e('offer banner', 'creta'); ?>"> 
                                          <div class="bottom-img-info"> <span class="banner-overly"></span>
                  <h3><?php echo wp_specialchars_decode($creta_Options['home-sub-banner4-text']);?></h3>
               <span class="line"></span>
               </div> </a> 
                       </div>
                       <?php } ?>
                        </div>
                 

                <div class="col-md-8 col-sm-12"> 
                    <?php if (isset($creta_Options['home-sub-banner5']) &&  !empty($creta_Options['home-sub-banner5']['url']))
                  {?>
             <div class="bottom-banner-img1 last">

                 <a href="<?php echo !empty($creta_Options['home-sub-banner5-url'])
                  ? esc_url($creta_Options['home-sub-banner5-url']) : '#' ?>">
                  <img src="<?php echo esc_url($creta_Options['home-sub-banner5']['url']); ?>" 
                  alt="<?php esc_attr_e('offer banner', 'creta'); ?>"> <span class="banner-overly"></span>
                   <div class="bottom-img-info">
                  <?php echo wp_specialchars_decode($creta_Options['home-sub-banner5-text']);?>
               </div>
                 </a>
                  </div>
                  <?php } ?>
                 </div> 
        
</div>
</div>
 </div>  
<?php }  ?>

<!-- end banner -->

<?php 
} 
}



if(!function_exists('magikCreta_footer_signupform'))
{
function magikCreta_footer_signupform()
{
  global $creta_Options;
if (isset($creta_Options['enable_mailchimp_form']) && !empty($creta_Options['enable_mailchimp_form'])) {
 if( function_exists('mc4wp_show_form'))
  {
  ?> 
        
  <div class="newsletter-wrap">
  <?php
    mc4wp_show_form();
   ?>
           
   </div>
  <?php
    } 
    }  

}
}


if(!function_exists('magikCreta_footer_middle'))
{
function magikCreta_footer_middle()
{
  global $creta_Options;
 
 if (isset($creta_Options['enable_footer_middle']) && !empty($creta_Options['footer_middle']))
  {?>
     
  <?php echo wp_specialchars_decode($creta_Options['footer_middle']);?>
            
   <?php  
    }  
}
}

if(!function_exists('magikCreta_recommended_products'))
{
function magikCreta_recommended_products()
{
     global $creta_Options;

if (isset($creta_Options['enable_home_recommended_products']) && !empty($creta_Options['enable_home_recommended_products']) ) { 
  ?>
 <div class="new-arrivals-pro">
    <div class="container">
      <div class="slider-items-products">
        <div class="new-arrivals-block">
          <div id="new-arrivals-slider" class="product-flexslider hidden-buttons">
             <div class="home-block-inner">
                   <div class="block-title">
                    <h2><?php esc_attr_e('Special', 'creta'); ?><br>
                         <em> <?php esc_attr_e('Products', 'creta'); ?></em>
                     </h2>
                        
                   </div>
                      <div class="pretext">
                         
                 <?php echo wp_specialchars_decode($creta_Options['enable_home_recommended_products-text']);?>
                       </div>
                      <a class="view_more_bnt" href="<?php echo !empty($creta_Options['enable_home_recommended_products']) ? esc_url($creta_Options['recommended_product_url']) : '#' ?>"><?php esc_attr_e('VIEW ALL ','creta'); ?></a>
                  </div>
                 
            
                            <div class="slider-items slider-width-col4 products-grid block-content">
<?php               
                            $args = array(
                              'post_type'       => 'product',
                              'post_status'       => 'publish',
                              'ignore_sticky_posts'   => 1,
                              'posts_per_page' => $creta_Options['recommended_per_page'],      
                              'meta_key' => 'recommended',
                              'order' => 'DESC'                               
                              );
                                $loop = new WP_Query( $args );
                             
                                if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();                  
                                magikCreta_productitem_template();
                                endwhile;
                                } else {
                                esc_attr_e( 'No products found', 'creta' );
                                }

                               wp_reset_postdata();
                               
                             
?>

        



</div>
</div>
</div>
</div>
</div>
</div>
 <?php  } ?>
<?php 
 }
}



if(!function_exists('magikCreta_featured_products'))
{
function magikCreta_featured_products()
{
    global $creta_Options;
    if (isset($creta_Options['enable_home_featured_products']) && !empty($creta_Options['enable_home_featured_products'])) {
        ?>
<div class="featured-pro">
    <div class="container">
      <div class="slider-items-products">
        <div class="featured-block">
          <div id="featured-slider" class="product-flexslider hidden-buttons">
            <div class="home-block-inner">
              <div class="block-title">
                <h2><?php esc_attr_e('Featured', 'creta'); ?><br>
                  <em> <?php esc_attr_e('Products', 'creta'); ?></em>
                </h2>
               
               </div>
               <div class="pretext">
                    
                 <?php
                   if(isset($creta_Options['enable_home_featured_products-text']))
                 {


                  echo wp_specialchars_decode($creta_Options['enable_home_featured_products-text']);
}
                  ?>
               </div>
               <a class="view_more_bnt" href="<?php echo !empty($creta_Options['enable_home_featured_products']) ? esc_url($creta_Options['featured_product_url']) : '#' ?>"><?php esc_attr_e('VIEW ALL ','creta'); ?></a>   
            </div>

<div class="slider-items slider-width-col4 products-grid block-content owl-carousel owl-theme">

    
                <?php
                $args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',                  
                    'posts_per_page' => $creta_Options['featured_per_page'], 
                    'tax_query' => array(
                      array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    ),
                ),                 
                );

                $loop = new WP_Query($args);
                if ($loop->have_posts()) {
                    while ($loop->have_posts()) : $loop->the_post();
                        magikCreta_productitem_template();
                    endwhile;
                } else {
                    esc_attr_e('No products found','creta');
                }

                wp_reset_postdata();
                ?>

    </div>
    </div>
  </div>
</div>

      </div>
    
  </div>
    <?php
    }
}
}

if(!function_exists('magikCreta_bestseller_products'))
{
function magikCreta_bestseller_products()
{
   global $creta_Options;

if (isset($creta_Options['enable_home_bestseller_products']) && !empty($creta_Options['enable_home_bestseller_products'])) { 
  ?>
 <div class="bestsell-pro">
    <div class="container">
      <div class="slider-items-products">
        <div class="bestsell-block">
          <div id="bestsell-slider" class="product-flexslider hidden-buttons">

            <div class="home-block-inner">
             <div class="block-title">
              <h2><?php esc_attr_e('Best', 'creta'); ?><br>
                  <em> <?php esc_attr_e('Seller', 'creta'); ?></em></h2>
                 
              </div>
              <div class="pretext">
                 <?php 
                 if(isset($creta_Options['home_bestseller_products-text']))
                 {

                 echo wp_specialchars_decode($creta_Options['home_bestseller_products-text']);
               }


                 ?>
               </div>
              <a class="view_more_bnt" href="<?php echo !empty($creta_Options['enable_home_bestseller_products']) ? esc_url($creta_Options['bestseller_product_url']) : '#' ?>"><?php esc_attr_e('VIEW ALL','creta'); ?></a>
            </div>
                 
                 
    <div class="slider-items slider-width-col4 products-grid block-content">

        <!-- best seller category fashion -->
     
<?php
                
                             $args = array(
                              'post_type'       => 'product',
                              'post_status'       => 'publish',
                              'ignore_sticky_posts'   => 1,
                              'posts_per_page' => $creta_Options['bestseller_per_page'],      
                              'meta_key'        => 'total_sales',
                              'orderby'         => 'meta_value_num',
                              'order' => 'DESC'                               
                              );

                                $loop = new WP_Query( $args );
                             
                                if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();                  
                                magikCreta_productitem_template();
                                endwhile;
                                } else {
                                esc_attr_e( 'No products found', 'creta' );
                                }

                               wp_reset_postdata();
                             
                             
?>

</div>
</div>
</div>
</div>
</div>
</div>
 <?php  } ?>
<?php 

}
}

if(!function_exists('magikCreta_new_products'))
{
function magikCreta_new_products()
{
   global $creta_Options;

if (isset($creta_Options['enable_home_new_products']) && !empty($creta_Options['enable_home_new_products']) && !empty($creta_Options['home_newproduct_categories'])) { 
  ?>
<div class="content-page">
    <div class="container"> 
      <!-- featured category fashion -->
      <div class="category-product">
        <div class="navbar nav-menu">
          <div class="navbar-collapse">
            <ul class="nav navbar-nav">

              <li>
                <div class="new_title">
                 <h2><strong> <?php esc_attr_e('New', 'creta'); ?> </strong><?php esc_attr_e('Products', 'creta'); ?></h2>
                  
                </div>
              </li>
            
          
<?php
$catloop=1;


 foreach($creta_Options['home_newproduct_categories'] as $category)
 {
  $term = get_term_by( 'id', $category, 'product_cat', 'ARRAY_A' );
  
  ?>
   <li class="<?php if($catloop==1){?> active <?php } ?>">
    <a href="#newcat-<?php echo esc_html($category) ?>" data-toggle="tab"><?php echo esc_html($term['name']); ?>
    </a>
  </li>
<li class="divider"></li>
  <?php 
  $catloop++;
  } ?>
 </ul>
 
        
  </div>
</div>

  <!-- Tab panes -->
 <div class="product-bestseller">
                
   <div class="product-bestseller-content">
     <div class="product-bestseller-list">
       <div class="tab-container">
    <?php 
    $contentloop=1;
  foreach($creta_Options['home_newproduct_categories'] as $catcontent)
 {
   $term = get_term_by( 'id', $catcontent, 'product_cat', 'ARRAY_A' );
?>
     <div class="tab-panel <?php if($contentloop==1){?> active <?php } ?>" id="newcat-<?php echo esc_html($catcontent); ?>">
      <div class="category-products">
       <ul class="products-grid">
<?php

 $args = array(
            'post_type'    => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts'    => 1,
            'posts_per_page' => 4,
            
             'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $catcontent
                )
            ),

                
        );

                                $loop = new WP_Query( $args );
                             
                                if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();                  
                                magikCreta_newproduct_template();
                                endwhile;
                                } else {
                                esc_html__( 'No products found', 'creta' );
                                }

                               wp_reset_postdata();
                               $contentloop++;
                             
?>

        </ul>
            </div>   
            </div>
 <?php  } ?>

    </div>
                        
  </div>
</div>
</div>
</div>
</div>
</div>


<?php 
}
}
}


if(!function_exists('magikCreta_hotdeal_product'))
{
function magikCreta_hotdeal_product()
{
   global $creta_Options;
if (isset($creta_Options['enable_home_hotdeal_products']) && !empty($creta_Options['enable_home_hotdeal_products'])) { 
  
  $product_ids_on_sale = wc_get_product_ids_on_sale();
  
  ?>
              <ul class="products-grid">


        <?php
        $today = date( 'Y-m-d' );

        $args = array(
          'post_type'      => 'product',
          'post_status'       => 'publish',
          'posts_per_page' => 1,
          'meta_key' => 'hotdeal_on_home',
          'meta_value' => 'yes',
          'post__in'    => $product_ids_on_sale   ,
          'meta_query' => WC()->query->get_meta_query(),          
        
          'order'         => 'asc'
       
             
        );
        
        $loop = new WP_Query( $args );
        
        if ( $loop->have_posts() ) {
          while ( $loop->have_posts() ) : $loop->the_post();
          magikCreta_hotdeal_template();            
          endwhile;
        } else {
         esc_attr_e('No products found','creta');
       }
       wp_reset_postdata();
       ?>
              </ul>
         
  <?php
}
}
}


if(!function_exists('magikCreta_newproduct_template'))
{
function magikCreta_newproduct_template()
{
  $MagikCreta = new MagikCreta();
  global $product, $woocommerce_loop, $yith_wcwl,$post;
   $imageUrl = wc_placeholder_img_src();
   if (has_post_thumbnail())
      $imageUrl =  wp_get_attachment_image_src(get_post_thumbnail_id(),'magikCreta-product-size-large');
   
   ?>
 <li class="item col-lg-3 col-md-3 col-sm-4 col-xs-6">
     <div class="item-inner">
      <div class="item-img">
         <div class="item-img-info">
            <a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars_decode($post->post_title); ?>" class="product-image">
               <figure class="img-responsive">
            <img alt="<?php echo wp_specialchars_decode($post->post_title); ?>" src="<?php echo esc_url($imageUrl[0]); ?>">
             </figure>

             </a>
            <?php if ($product->is_on_sale()) : ?>
            <div class="new-label new-top-left">
               <?php esc_attr_e('Sale', 'creta'); ?>
            </div>
            <?php endif; ?>

            <div class="box-hover">
                  <ul class="add-to-links">
                      <li>
                                    <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                                       <a class="yith-wcqv-button link-quickview" href="#"
                                        data-product_id="<?php echo esc_html($product->get_id()); ?>"><?php esc_attr_e('Quick View', 'creta'); ?></a>
                                     <?php } ?>
                      </li>
                      <li>
                                        <?php
                               if (isset($yith_wcwl) && is_object($yith_wcwl)) {
                            $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
                            ?>
                            <a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"  data-product-id="<?php echo esc_html($product->get_id()); ?>"
                              data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
                                ><?php esc_attr_e('Add to Wishlist', 'creta'); ?></a> 
                             <?php
                               }
                               ?>
                    </li>
                    <li>
                              <?php if (class_exists('YITH_Woocompare_Frontend')) {
                               $mgk_yith_cmp = new YITH_Woocompare_Frontend; ?>      
                              <a href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" class="compare link-compare add_to_compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
                                ><?php esc_attr_e('Add to Compare', 'creta'); 
                              ?></a>
                              <?php
                              }
                             ?> 
                    </li>
                </ul>
            </div>

            
         </div>
      </div>
      <div class="item-info">
         <div class="info-inner">
            <div class="item-title"><a href="<?php the_permalink(); ?>"
               title="<?php echo wp_specialchars_decode($post->post_title); ?>"> <?php echo wp_specialchars_decode($post->post_title); ?> </a>
            </div>
            <div class="item-content">
               <div class="rating">
                  <div class="ratings">
                     <div class="rating-box">
                        <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating"> </div>
                     </div>
                  </div>
               </div>
               <div class="item-price">
                  <div class="price-box">
                    <span class="regular-price"> 
                          <?php echo wp_specialchars_decode($product->get_price_html()); ?>
                     </span>
                    
                  </div>
               </div>
               <div class="action">
                     <?php $MagikCreta->magikCreta_woocommerce_product_add_to_cart_text() ;?>
                  </div>
            </div>
         </div>
      </div>
   </div>
</li>
<?php

}
}

if(!function_exists('magikCreta_productitem_template'))
{
function magikCreta_productitem_template()
{
  
  $MagikCreta = new MagikCreta();
  global $product, $woocommerce_loop, $yith_wcwl,$post;
   $imageUrl = wc_placeholder_img_src();
   if (has_post_thumbnail())
      $imageUrl =  wp_get_attachment_image_src(get_post_thumbnail_id(),'magikCreta-product-size-large');
   
   ?>

 <div class="item">
     <div class="item-inner">
      <div class="item-img">
         <div class="item-img-info">
            <a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars_decode($post->post_title); ?>" class="product-image">
               <figure class="img-responsive">
            <img alt="<?php echo wp_specialchars_decode($post->post_title); ?>" src="<?php echo esc_url($imageUrl[0]); ?>">
             </figure>
             </a>
            <?php if ($product->is_on_sale()) : ?>
            <div class="new-label new-top-right">
               <?php esc_attr_e('Sale', 'creta'); ?>
            </div>
            <?php endif; ?>
                <div class="box-hover">
                  <ul class="add-to-links">
                      <li>
                                    <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                                       <a class="yith-wcqv-button link-quickview" href="#"
                                        data-product_id="<?php echo esc_html($product->get_id()); ?>"><?php esc_attr_e('Quick View', 'creta'); ?></a>
                                     <?php } ?>
                      </li>
                      <li>
                                        <?php
                               if (isset($yith_wcwl) && is_object($yith_wcwl)) {
                            $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
                            ?>
                            <a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"  data-product-id="<?php echo esc_html($product->get_id()); ?>"
                              data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
                                ><?php esc_attr_e('Add to Wishlist', 'creta'); ?></a> 
                             <?php
                               }
                               ?>
                    </li>
                    <li>
                              <?php if (class_exists('YITH_Woocompare_Frontend')) {
                               $mgk_yith_cmp = new YITH_Woocompare_Frontend; ?>      
                              <a href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" class="compare link-compare add_to_compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
                                ><?php esc_attr_e('Add to Compare', 'creta'); 
                              ?></a>
                              <?php
                              }
                             ?> 
                    </li>
                </ul>
            </div>
         </div>
      </div>
      <div class="item-info">
         <div class="info-inner">
            <div class="item-title"><a href="<?php the_permalink(); ?>"
               title="<?php echo wp_specialchars_decode($post->post_title); ?>"> <?php echo wp_specialchars_decode($post->post_title); ?> </a>
            </div>
            <div class="item-content">
               <div class="rating">
                  <div class="ratings">
                     <div class="rating-box">
                        <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating"> </div>
                     </div>
                  </div>
               </div>
               <div class="item-price">
                  <div class="price-box">
                    <?php echo wp_specialchars_decode($product->get_price_html()); ?>
                  </div>
               </div>
               <div class="action">
                     <?php $MagikCreta->magikCreta_woocommerce_product_add_to_cart_text() ;?>
                  </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php

}
}


if(!function_exists('magikCreta_related_upsell_template'))
{
function magikCreta_related_upsell_template()
{
  $MagikCreta = new MagikCreta();
 global $product, $woocommerce_loop, $yith_wcwl,$post;


$imageUrl = wc_placeholder_img_src();
if (has_post_thumbnail())
    $imageUrl =  wp_get_attachment_image_src(get_post_thumbnail_id(),'magikCreta-product-size-large');  
?>
<!-- Item -->
<div class="item">
<div class="item-inner">
   <div class="item-img">
      <div class="item-img-info">
         <a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars_decode($post->post_title); ?>" class="product-image">
           <figure class="img-responsive">
          <img alt="<?php echo wp_specialchars_decode($post->post_title); ?>" src="<?php echo esc_url($imageUrl[0]); ?>">
          </figure>
           </a>
            <?php if ($product->is_on_sale()) : ?>
             <div class="new-label new-top-right">
               <?php esc_attr_e('Sale', 'creta'); ?>
            </div>
            <?php endif; ?>
                <div class="box-hover">
                  <ul class="add-to-links">
                      <li>
                                    <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                                       <a class="yith-wcqv-button link-quickview" href="#"
                                        data-product_id="<?php echo esc_html($product->get_id()); ?>"><?php esc_attr_e('Quick View', 'creta'); ?></a>
                                     <?php } ?>
                      </li>
                      <li>
                                        <?php
                               if (isset($yith_wcwl) && is_object($yith_wcwl)) {
                            $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
                            ?>
                            <a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"  data-product-id="<?php echo esc_html($product->get_id()); ?>"
                              data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
                                ><?php esc_attr_e('Add to Wishlist', 'creta'); ?></a> 
                             <?php
                               }
                               ?>
                    </li>
                    <li>
                              <?php if (class_exists('YITH_Woocompare_Frontend')) {
                               $mgk_yith_cmp = new YITH_Woocompare_Frontend; ?>      
                              <a href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" class="compare link-compare add_to_compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
                                ><?php esc_attr_e('Add to Compare', 'creta'); 
                              ?></a>
                              <?php
                              }
                             ?> 
                    </li>
                </ul>
            </div>
      </div>
   </div>
   <div class="item-info">
      <div class="info-inner">
         <div class="item-title"><a href="<?php the_permalink(); ?>"
                                               title="<?php echo wp_specialchars_decode($post->post_title); ?>"> <?php echo wp_specialchars_decode($post->post_title); ?> </a> </div>
         <div class="item-content">
            <div class="rating">
               <div class="ratings">
                  <div class="rating-box">
                    <?php $average = $product->get_average_rating(); ?>
                     <div class="rating"  style="width:<?php echo esc_html(($average / 5) * 100); ?>%"></div>
                  </div>
                  
               </div>
            </div>
            <div class="item-price">
               <div class="price-box"><?php echo wp_specialchars_decode($product->get_price_html()); ?></div>
            </div>
            <div class="action">
                     <?php $MagikCreta->magikCreta_woocommerce_product_add_to_cart_text() ;?>
                  </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php
}
}


if(!function_exists('magikCreta_hotdeal_template'))
{
function magikCreta_hotdeal_template()
{
$MagikCreta = new MagikCreta();
 global $product, $woocommerce_loop, $yith_wcwl,$post;
   $imageUrl = wc_placeholder_img_src();
   if (has_post_thumbnail())
        $imageUrl = wp_get_attachment_url(get_post_thumbnail_id());

 $product_type = $product->get_type();
            
              if($product_type=='variable')
              {
               $available_variations = $product->get_available_variations();
               $variation_id=$available_variations[0]['variation_id'];
                $newid=$variation_id;
              }
              else
              {
                $newid=$post->ID;
           
              }                                    
               $sales_price_to = get_post_meta($newid, '_sale_price_dates_to', true);  
               if(!empty($sales_price_to))
               {
               $sales_price_date_to = date("Y/m/d", $sales_price_to);
               } 
               else{
                $sales_price_date_to='';
              } 
               $curdate=date("m/d/y h:i:s A");                         
?> 
         
           <li class="right-space two-height item">
                  <div class="item-inner">
                   <div class="item-img">
                                     
               <div class="item-img-info">
            <a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars_decode($post->post_title); ?>" class="product-image">
              <figure class="img-responsive">
            <img alt="<?php echo wp_specialchars_decode($post->post_title); ?>" src="<?php echo esc_url($imageUrl); ?>">
              </figure>
             </a>
            <?php if ($product->is_on_sale()) : ?>
            <div class="hot-label hot-top-left">
               <?php esc_attr_e('hot', 'creta'); ?>
            </div>
            <?php endif; ?>

                    
                <div class="box-hover">
                  <ul class="add-to-links">
                      <li>
                                    <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                                       <a class="yith-wcqv-button link-quickview" href="#"
                                        data-product_id="<?php echo esc_html($product->get_id()); ?>"><?php esc_attr_e('Quick View', 'creta'); ?></a>
                                     <?php } ?>
                      </li>
                      <li>
                                        <?php
                               if (isset($yith_wcwl) && is_object($yith_wcwl)) {
                            $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="add_to_wishlist link-wishlist"' : 'class="add_to_wishlist link-wishlist"';
                            ?>
                            <a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product->get_id())) ?>"  data-product-id="<?php echo esc_html($product->get_id()); ?>"
                              data-product-type="<?php echo esc_html($product->get_type()); ?>" <?php echo wp_specialchars_decode($classes); ?>
                                ><?php esc_attr_e('Add to Wishlist', 'creta'); ?></a> 
                             <?php
                               }
                               ?>
                    </li>
                    <li>
                              <?php if (class_exists('YITH_Woocompare_Frontend')) {
                               $mgk_yith_cmp = new YITH_Woocompare_Frontend; ?>      
                              <a href="<?php echo esc_url($mgk_yith_cmp->add_product_url($product->get_id())); ?>" class="compare link-compare add_to_compare" data-product_id="<?php echo esc_html($product->get_id()); ?>"
                                ><?php esc_attr_e('Add to Compare', 'creta'); 
                              ?></a>
                              <?php
                              }
                             ?> 
                    </li>
                </ul>
            </div>

           <div class="box-timer">
         <div class="timer-grid"  data-time="<?php echo esc_html($sales_price_date_to) ;?>">
          </div>
         </div>

             </div>
                </div>

                    <div class="item-info">
         <div class="info-inner">
            <div class="item-title"><a href="<?php the_permalink(); ?>"
               title="<?php echo wp_specialchars_decode($post->post_title); ?>"> <?php echo wp_specialchars_decode($post->post_title); ?> </a>
            </div>
            <div class="item-content">
               <div class="rating">
                  <div class="ratings">
                     <div class="rating-box">
                        <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating"> </div>
                     </div>
                  </div>
               </div>
               <div class="item-price">
                  <div class="price-box">
                    <?php echo wp_specialchars_decode($product->get_price_html()); ?>
                  </div>
               </div>
               <div class="action">
               <?php $MagikCreta->magikCreta_woocommerce_product_add_to_cart_text() ;?>
              </div>
          </div>
         </div>
      </div>

                 
   </div>
</li>
<?php
}
}

if(!function_exists('magikCreta_footer_brand_logo'))
{
function magikCreta_footer_brand_logo()
  {
    global $creta_Options;
    if (isset($creta_Options['enable_brand_logo']) && $creta_Options['enable_brand_logo'] && !empty($creta_Options['all-company-logos'])) : ?>
    
    <div class="brand-logo">
    <div class="container">
      <div class="slider-items-products">
        <div id="brand-logo-slider" class="product-flexslider hidden-buttons">
          <div class="slider-items slider-width-col6"> 
            
            <!-- Item -->
            
                   <?php foreach ($creta_Options['all-company-logos'] as $_logo) : ?>
                  <div class="item">
                    <a href="<?php echo esc_url($_logo['url']); ?>" target="_blank"> <img
                        src="<?php echo esc_url($_logo['image']); ?>" 
                        alt="<?php echo esc_attr($_logo['title']); ?>"> </a>
                  </div>
                  <?php endforeach; ?>
                
      </div>
    </div>
  </div>
  </div>
  </div>

    
  <?php endif;
  }
}

if(!function_exists('magikCreta_home_blog_posts'))
{
function magikCreta_home_blog_posts()
{
    $count = 0;
    global $creta_Options;
    $MagikCreta = new MagikCreta();
    if (isset($creta_Options['enable_home_blog_posts']) && !empty($creta_Options['enable_home_blog_posts'])) {
        ?>

  <div class="container latest-blog">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="blog-outer-container">
          <div class="new_title">
            <h2><strong> <?php esc_attr_e('Latest', 'creta'); ?> </strong><?php esc_attr_e('Blog', 'creta'); ?></h2>
             
          </div>

 <div class="blog-inner">
       
        <?php

        $args = array('posts_per_page' => 2, 'post__not_in' => get_option('sticky_posts'));
        $the_query = new WP_Query($args);
           $i=1;  
        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : $the_query->the_post(); ?>
            
                
                 <div class="col-lg-6 col-md-6 col-sm-6 blog-preview_item">
                    <div class="entry-thumb image-hover2">  
                      <a href="<?php the_permalink(); ?>">
                                <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'single-post-thumbnail'); ?>
                                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title_attribute(); ?>">
                      </a>
                          
                     </div>

                     <div class="blog-preview_info">
                      <ul class="post-meta">
                  <li><i class="fa fa-user"></i><?php esc_attr_e('By', 'creta'); ?>  <?php the_author(); ?> </li>
                  <li><i class="fa fa-comment"></i><a href="<?php comments_link(); ?>"><?php comments_number('0 Comment', '1 Comment', '% Comments'); ?>
                          </a></li>
                  <li><i class="fa fa-calendar"></i><time class="entry-date" datetime="2015-05-11T11:19:34+00:00"><?php esc_html(the_time('F d, Y')); ?></time>
                   </li>
                </ul>
                <h4 class="blog-preview_title">
                  <a href="<?php the_permalink(); ?>"><?php esc_html(the_title()); ?>
                     </a>
                </h4>
                <div class="blog-preview_desc"><div class="homeblog"><?php the_excerpt(); ?></div></div>
                <a class="blog-preview_btn" href="<?php the_permalink(); ?>"><?php esc_attr_e('Read more','creta'); ?></a>
                 </div>

             </div>
              
            <?php    $i++;
             endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <p>
                <?php esc_attr_e('Sorry, no posts matched your criteria.', 'creta'); ?>
            </p>
        <?php endif;
        ?>
        </div>
        </div>
</div>
</div>
</div>

<?php
    }
}
}


// new ccustom code section added

if(!function_exists('magikCreta_home_customsection'))
{
 function magikCreta_home_customsection()
 {
   global $creta_Options;
   ?>

   <div class="container">
    <div class="row">
     <div class="mgk_custom_block">
       <div class="col-md-12">
       <?php the_content(); ?>
     </div>
     </div>
   </div>
 </div>
 <?php
}

}