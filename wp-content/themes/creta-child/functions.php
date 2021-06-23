<?php
/* Add your custom functions here */
function magikCreta_enqueue_styles() {
       $parent_style = 'magikCreta-style'; // This is 'Responsive-style' for the Responsive theme.
       wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
       wp_enqueue_style( 'child-style',
                  get_stylesheet_directory_uri() . '/style.css',
                  array( $parent_style ),
                  wp_get_theme()->get('Version')
       );
}
add_action( 'wp_enqueue_scripts', 'magikCreta_enqueue_styles' );
