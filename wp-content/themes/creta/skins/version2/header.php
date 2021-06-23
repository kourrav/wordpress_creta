<!DOCTYPE html>
<html <?php language_attributes(); ?> id="parallax_scrolling">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
 <?php wp_head(); ?>
</head>
<?php
 $MagikCreta = new MagikCreta(); ?>

 <?php if ((is_front_page() && is_home()) || is_front_page()){ ?>
<body <?php body_class(); ?> >
  <div id="page" class="page">

      <!-- Header -->
      <header id="header">
        

           <?php magikCreta_header_service();?>
          
        
         <div class="header-container">
         <div class="container">
            <!-- Header Logo -->
            <div class="logo">
             <?php magikCreta_logo_image();?>
            </div>

            <!-- End Header Logo -->
          

            <div class="top-menu ">   
                   
               <nav>
                  <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>
                  <div class="nav-inner">
                     <div class="mgk-main-menu">
                     <div id="main-menu">
                        <?php echo magikCreta_main_menu(); ?>                       
                     </div>
                     </div>
                     </div>
               </nav>

            </div>
             
               <div class="header-right-col">
                <?php  if ( has_nav_menu( 'toplinks' ) ) :?>
                  <div class="click-nav">
                     <div class="no-js">
                        <a title="<?php esc_attr_e('clicker:', 'creta');?>" class="clicker"></a>
                        <div class="top-links">
                            <?php magikCreta_currency_language();?>
                            <?php echo magikCreta_top_navigation(); ?>                           
                        </div>
                     </div>
                  </div>
               <?php endif ;?>
                  <div class="top-cart-contain">
                     <?php
                        if (class_exists('WooCommerce')) :
                             $MagikCreta->magikCreta_mini_cart();
                             endif;
                             ?>
                     <!--top-cart-content-->
                  </div>

                  <!--mini-cart-->

                    <!-- top search code -->
              <div id="form-search" class="search-bar">
              
                     <?php  echo magikCreta_search_form(); ?>  
                    <div class="search-autocomplete" id="search_autocomplete" style="display: none;"></div>
                 
          </div>
                 
                  <!--links-->
               </div></div>
            
         </div>
      </header>


<?php } 

else{ ?>

<body <?php body_class(); ?> >
  <div id="page" class="page">

      <!-- Header -->
      <header id="header" >
        

           <?php magikCreta_header_service();?>
          
        
         <div class="header-container">
           <div class="container">
            <!-- Header Logo -->
            <div class="logo">
             <?php magikCreta_logo_image();?>
            </div>

            <!-- End Header Logo -->
          

            <div class="top-menu ">   
                   
               <nav>
                            <div class="mm-toggle">  <a class="mobile-toggle"><i class="fa fa-reorder"></i></a></div>
                  <div class="nav-inner">
                     <div class="mgk-main-menu">
                     <div id="main-menu">
                        <?php echo magikCreta_main_menu(); ?>                       
                     </div>
                     </div>
                     </div>
               </nav>

            </div>
             
               <div class="header-right-col">
                <?php  if ( has_nav_menu( 'toplinks' ) ) :?>
                  <div class="click-nav">
                     <div class="no-js">
                        <a title="<?php esc_attr_e('clicker:', 'creta');?>" class="clicker"></a>
                        <div class="top-links">
                            <?php magikCreta_currency_language();?>
                            <?php echo magikCreta_top_navigation(); ?>                           
                        </div>
                     </div>
                  </div>
               <?php endif ;?>
                  <div class="top-cart-contain">
                     <?php
                        if (class_exists('WooCommerce')) :
                             $MagikCreta->magikCreta_mini_cart();
                             endif;
                             ?>
                     <!--top-cart-content-->
                  </div>

                  <!--mini-cart-->

                    <!-- top search code -->
              <div id="form-search" class="search-bar">
              
                     <?php  echo magikCreta_search_form(); ?>  
                   
                     
          </div>
                 
                  <!--links-->
               </div>
            
         </div>
          </div>
      </header>
      <!-- end header -->
      <?php if (class_exists('WooCommerce') && is_woocommerce()) : ?>
     <div class="page-heading">
    
      <div class="container">
          <div class="breadcrumbs">
                  <?php woocommerce_breadcrumb(); ?>
              </div>

             <?php if(is_product_category() || is_shop()){?>
         <div class="page-title">
             <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                            <h2>
                                <?php esc_html(woocommerce_page_title()); ?>
                            </h2>
                        <?php endif; ?>
     
    </div>
    <?php } ?>
       </div>
       
       </div>
      <?php endif; ?>  
<?php } 
        