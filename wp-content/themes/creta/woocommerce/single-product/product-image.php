<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */


defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
  return;
}




global $post, $woocommerce, $product,$creta_Options;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$attachment_id = $product->get_image_id();
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
  'woocommerce-product-gallery',
  'woocommerce-product-gallery--' . ( has_post_thumbnail() ? 'with-images' : 'without-images' ),
  'woocommerce-product-gallery--columns-' . absint( $columns ),
  'images',
  ) );
 $main_image=wc_get_gallery_image_html( $attachment_id  );

          $flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );

          $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );

          $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );

  

     $image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
          $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );

          $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
          $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
          $thumbnail_post   = get_post( $attachment_id );
      

          $attributes      = array(
            'title'                   => get_post_field( 'post_title', $attachment_id ),
            'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
            'data-src'                => $full_src[0],
            'data-large_image'        => $full_src[0],
            'data-large_image_width'  => $full_src[1],
            'data-large_image_height' => $full_src[2],
            'class'                   => $main_image ? 'wp-post-image' : '',
            );
          $image             = wp_get_attachment_image( $attachment_id, $image_size, false, $attributes );
          $image_link=$full_src[0];
          $image_title = esc_html($attributes['title']);

?>


              
<?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
  {
   ?>
            
        
        <div class="images large-image product-image">

         <?php if ($product->is_on_sale()) : ?>
          <div class="sale-label sale-top-left">
              <?php esc_attr_e('Sale', 'creta'); ?>
          </div>
         <?php endif; ?>


        <figure class="woocommerce-product-gallery__wrapper product-full">
          <?php
         
          if ( has_post_thumbnail() ) {          



            $reldata=array('useWrapper'=> false, 'adjustY' =>0,'adjustX' =>0);
                          $reldata = json_encode($reldata);
                         $html  = "<div data-thumb='" . esc_url( $thumbnail_src[0] ) . "' class='woocommerce-product-gallery__image '><a href='" . esc_url( $image_link ) . "' itemprop='image' class='woocommerce-main-image zoom cloud-zoom thumb-link'  id='zoom1' rel='".$reldata."'><img id='product-zoom' class='attachment-shop_single size-shop_single wp-post-image zoomImg' src='".esc_url( $image_link )."' data-zoom-image='".esc_url( $image_link )."' />";
                         $html .= '</a></div>';
          } else {
            $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
            $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
            $html .= '</div>';
          }

          echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

          do_action( 'woocommerce_product_thumbnails' );
          ?>
        </figure>
       </div>
   

<?php } else{ ?>

<div class="images product-image">
                     
              <?php if ($product->is_on_sale()) : ?>
                <div class="sale-label sale-top-left">
                    <?php esc_attr_e('Sale', 'creta'); ?>
                </div>
              <?php endif; ?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

    
        <figure class="woocommerce-product-gallery__wrapper product-full">
      <?php
      if ( has_post_thumbnail() ) {
  
       $html  = '<div data-thumb="' .  esc_url( $thumbnail_src[0] ). '" class="woocommerce-product-gallery__image "><a class="woocommerce-main-image zoom cloud-zoom"><img id="product-zoom" class="attachment-shop_single size-shop_single wp-post-image " src="'.esc_url( $full_src[0] ).'" data-zoom-image="'.esc_url( $full_src[0] ).'" data-large_image="'.$image_link.'" data-large_image_width="800" data-large_image_height="800"/>';
       $html .= '</a></div>';
     } else {
      $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
      $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
      $html .= '</div>';


    }

    echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
?>
<?php
    do_action( 'woocommerce_product_thumbnails' );
    ?>
  </figure>




   </div>
</div>
<?php }?>                    
           
      
                  