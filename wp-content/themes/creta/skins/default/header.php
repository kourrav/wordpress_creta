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
<body <?php body_class(); ?> >
  <div id="page" class="page catalog-category-view">

      <!-- Header -->
      <header id="header" >
         <div class="header-container">

      <div class="header-top">
      <div class="container">
        <div class="row">

         <div class="col-xs-12 col-sm-6">
          
          <?php echo magikCreta_currency_language(); ?>

        <div class="welcome-msg">
            <?php echo magikCreta_msg(); ?>
        </div>  

           </div>
             <div class="col-xs-6 hidden-xs"> 
              <div class="toplinks">
                <div class="links">
              <!-- Header Top Links -->
            <?php echo magikCreta_top_navigation(); ?>
              <!-- End Header Top Links --> 
              </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    
    <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 hidden-xs">
          
              <?php echo magikCreta_search_form(); ?>  
          
          </div>  

           <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 logo-block"> 
             <!-- Header Logo -->
            <div class="logo">
             <?php magikCreta_logo_image();?>
            </div>

            <!-- End Header Logo -->

        </div>
       <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <div class="top-cart-contain pull-right"> 
                 <?php
                        if (class_exists('WooCommerce')) :
                             $MagikCreta->magikCreta_mini_cart();
                             endif;
                             ?>
            </div>
      </div>
          
       </div>
      </div>
    </div>
  </header>
  <nav>
    <div class="container">
                 <div class="mm-toggle-wrap">
                  <div class="mm-toggle">  <a class="mobile-toggle"><i class="fa fa-reorder"></i></a></div>
                 
                 </div>
                 <div id="main-menu-new">
                  <div class="nav-inner">
                     
                  <?php echo magikCreta_main_menu(); ?>                       
                   </div> 
                    
                  </div>
            </div>
               </nav>
      <!-- end header -->
      <?php if (class_exists('WooCommerce') && is_woocommerce()) : ?>
    
    <div class="breadcrumbs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
                  <?php woocommerce_breadcrumb(); ?>
              </div>
          <!--col-xs-12--> 
        </div>
        <!--row--> 
      </div>
      <!--container--> 
    </div>
           <?php endif; ?>