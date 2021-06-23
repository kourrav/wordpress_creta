<?php
/*
 * Cut string
 * 
 * @param string $string 
 * @param int $length
 * 
 * @return string
 */

function mgkwooas_str_cut( $string, $length = 40 ) {

	$string = strip_tags( $string );

	if ( strlen( $string ) > $length ) {
		$title = mb_substr( $string, 0, $length, 'utf-8' ) . '...';
	} else {
		$title = $string;
	}
	return $title;
}



/*
 * Get product desc
 * 
 * @param int $product product object or The post ID
 * @param int $length description length
 * 
 * @return string
 */

function mgkwooas_get_product_desc( $product, $length = 130 ) {

  if ( is_numeric( $product ) ) {
    $product = wc_get_product( $product );
  }

  $output = '';

  if ( !empty( $product ) ) {


   if ( version_compare( WC()->version, '3.0', '>=' ) ) {
    $short_desc = $product->get_short_description();
  } else {
    $short_desc = $product->post->post_excerpt;
  }

  if ( !empty( $short_desc ) ) {
    $output = mgkwooas_str_cut( wp_strip_all_tags( $short_desc ), $length );
  } else {
    if ( version_compare( WC()->version, '3.0', '>=' ) ) {
      $desc = $product->get_description();
    } else {
      $short_desc = $product->post->post_content;
    }
    if ( !empty( $desc ) ) {
      $output = mgkwooas_str_cut( wp_strip_all_tags( $desc ), $length );
    }
  }
}

$output = html_entity_decode($output);

return $output;
}


?>