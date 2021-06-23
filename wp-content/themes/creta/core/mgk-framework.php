<?php 
// call theme skins function

require_once(MAGIKCRETA_THEME_PATH . '/includes/layout.php');
require_once(MAGIKCRETA_THEME_PATH . '/includes/mgk-menu.php');



add_action('init','magikCreta_theme_layouts');
 /* Include theme variation functions */  
if(!function_exists('magikCreta_theme_layouts'))
{
function magikCreta_theme_layouts()
{
 global $creta_Options;   
 if (isset($creta_Options['theme_layout']) && !empty($creta_Options['theme_layout'])) {
require_once ( MAGIKCRETA_THEME_PATH . '/skins/' . $creta_Options['theme_layout'] . '/functions.php');   
} else {
require_once ( MAGIKCRETA_THEME_PATH . '/skins/default/functions.php');   
}
 }


}


 /* Include theme variation header */   
if(!function_exists('magikCreta_theme_header'))
{
function magikCreta_theme_header()
{
 global $creta_Options;  
  if (isset($creta_Options['theme_layout']) && !empty($creta_Options['theme_layout'])) {
load_template(MAGIKCRETA_THEME_PATH . '/skins/' . $creta_Options['theme_layout'] . '/header.php', true);
} else {
load_template(MAGIKCRETA_THEME_PATH . '/skins/default/header.php', true);
}

 }

}

/* Include theme variation homepage */ 

if(!function_exists('magikCreta_theme_homepage'))
{
 function magikCreta_theme_homepage()
 {  
 global $creta_Options;  
 
 if (isset($creta_Options['theme_layout']) && !empty($creta_Options['theme_layout'])) {
load_template(MAGIKCRETA_THEME_PATH . '/skins/' . $creta_Options['theme_layout'] . '/homepage.php', true);
} else {
load_template(MAGIKCRETA_THEME_PATH . '/skins/default/homepage.php', true);
}
 }
}

 /* Include theme variation footer */ 

if(!function_exists('magikCreta_theme_footer'))
{
function magikCreta_theme_footer()
{
     
 global $creta_Options;   
  if (isset($creta_Options['theme_layout']) && !empty($creta_Options['theme_layout'])) {
load_template(MAGIKCRETA_THEME_PATH . '/skins/' . $creta_Options['theme_layout'] . '/footer.php', true);
} else {
load_template(MAGIKCRETA_THEME_PATH . '/skins/default/footer.php', true);
} 
}
}

 

if(!function_exists('magikCreta_layout_breadcrumb'))
{
function magikCreta_layout_breadcrumb() {
$MagikCreta = new MagikCreta();
 global $creta_Options; 

 if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
  {
  ?>
<div class="page-heading">   
  <div class="container">
     <div class="breadcrumbs">
      <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
      </div>
        
      <div class="page-title">
       <h1 class="entry-title">
       <?php $MagikCreta->magikCreta_page_title(); ?>
       </h1>
       </div>
    </div>     
</div>
     
<?php 
}
else
{ 
?>


 <div class="breadcrumbs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
          <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
          </div>
          <!--col-xs-12--> 
        </div>
        <!--row--> 
      </div>
      <!--container--> 
    </div>
<?php

}

}
}

 if ( ! function_exists ( 'magikCreta_social_media_links' ) ) { 
  //social links
  function magikCreta_social_media_links()
  {
    global $creta_Options;
    if(isset($creta_Options
  ['enable_social_link_footer']) && !empty($creta_Options['enable_social_link_footer']))
    {?>
 <div class="social">
<?php  if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='default')
     {?>
  <h4><?php esc_attr_e('Follow Us', 'creta'); ?></h4>
  <?php } ?>
  <ul>
  <?php
    if (isset($creta_Options
  ['social_facebook']) && !empty($creta_Options['social_facebook'])) {
      echo "<li class=\"fb pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_facebook']) ."'></a></li>";
    }

    if (isset($creta_Options['social_twitter']) && !empty($creta_Options['social_twitter'])) {
      echo "<li class=\"tw pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_twitter']) ."'></a></li>";
    }

    if (isset($creta_Options['social_googlep']) && !empty($creta_Options['social_googlep'])) {
      echo "<li class=\"googleplus pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_googlep'])."'></a></li>";
    }

    if (isset($creta_Options['social_rss']) && !empty($creta_Options['social_rss'])) {
      echo "<li class=\"rss pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_rss'])."'></a></li>";
    }

    if (isset($creta_Options['social_pinterest']) && !empty($creta_Options['social_pinterest'])) {
      echo "<li class=\"pintrest pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_pinterest'])."'></a></li>";
    }

    if (isset($creta_Options['social_linkedin']) && !empty($creta_Options['social_linkedin'])) {
      echo "<li class=\"linkedin pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_linkedin'])."'></a></li>";
    }

    if (isset($creta_Options['social_youtube']) && !empty($creta_Options['social_youtube'])) {
      echo "<li class=\"youtube pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_youtube'])."'></a></li>";
    }

    if (isset($creta_Options
    ['social_instagram']) && !empty($creta_Options['social_instagram'])) {
      echo "<li class=\"instagram pull-left\"><a target=\"_blank\" href='".  esc_url($creta_Options['social_instagram']) ."'></a></li>";
    }

    ?>
 </ul>
 </div>

 <?php }
  }

}

if(!function_exists('magikCreta_singlepage_breadcrumb'))
{
function magikCreta_singlepage_breadcrumb() {
 $MagikCreta = new MagikCreta();
 global $creta_Options; 

 if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
{
?>
<div class="page-heading">   
  <div class="container">
      <div class="breadcrumbs">
      <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
      </div>
  </div>
</div>
     
<?php }
else{ ?>
 <div class="breadcrumbs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
          <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
          </div>
          <!--col-xs-12--> 
        </div>
        <!--row--> 
      </div>
      <!--container--> 
    </div>


<?php

}

}
}

if(!function_exists('magikCreta_simple_product_link'))
{
function magikCreta_simple_product_link()
{
  global $product,$class;
  $product_type = $product->get_type();
  $product_id=$product->get_id();
  if($product->get_price() =='')
  { ?>
<a class="button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")' >
    <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
    </a>
<?php  }
  else{
  ?>
<a class="single_add_to_cart_button add_to_cart_button  product_type_simple ajax_add_to_cart button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>' data-quantity="1" data-product_id="<?php echo esc_attr($product->get_id()); ?>"
      href='<?php echo esc_url($product->add_to_cart_url()); ?>'>
    <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
    </a>
<?php
}
}
}

if(!function_exists('magikCreta_body_classes'))
{
function magikCreta_body_classes( $classes ) 
{
  // Adds a class to body.
global $creta_Options; 
 if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
{
if ((is_front_page() && is_home()) || is_front_page())
{
  $classes[]='cms-index-index cms-creta-home cms-home-page';
}
else 
{
  $classes[]='cms-home-page';
}
}
else
{
    $classes[] = 'cms-index-index cms-home-page';
}
if (class_exists('WooCommerce') && is_woocommerce() && (!is_shop() && !is_product_category()))
{
    $classes[]='woopage cms-home-page';
}

  return $classes;
}
}

add_filter( 'body_class', 'magikCreta_body_classes');

if(!function_exists('magikCreta_post_classes'))
{
function magikCreta_post_classes( $classes ) 
{
  // add custom post classes.
if(class_exists('WooCommerce') && is_woocommerce())
{ 
$classes[]='notblog';
if(is_product_category())
{
 $classes[]='notblog'; 
} 
}
else if(is_category() || is_archive() || is_search() || is_tag() || is_home())
{
$classes[] = 'blog-post container-paper';
}
else
{
$classes[]='notblog';
} 

  return $classes;
}
}
add_filter( 'post_class', 'magikCreta_post_classes');



function  magikCreta_curPageURL() {
            global $wp;
            $pageURL=home_url($wp->request);
            return $pageURL;
        }


        
?>