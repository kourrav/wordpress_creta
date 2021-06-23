<?php
/**
 * Plugin Name: Magik Infinite Scroller
 * Plugin URI: https://www.magikcommerce.com/
 * Description: Magik Infinite Scroller provides infinite loading of products on WooCommerce product category page.
 * Version: 1.0
 * Author: MagikCommerce
 * Requires at least: WP 5.0
 * Tested up to: WP 5.1
 * WC requires at least: 3.5.5
 * WC tested up to: 3.5.5
 * Author URI: https://www.magikcommerce.com/
 * Text Domain: magik-infinite-scroller
 * Domain Path: /languages/
 */



if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly


! defined( 'MGKISR_PLUGIN' )                  && define( 'MGKISR_PLUGIN', true);
! defined( 'MGKISR_PLUGIN_VERSION' )          && define( 'MGKISR_PLUGIN_VERSION', '1.0.0');
! defined( 'MGKISR_PLUGIN_PATH' )             && define( 'MGKISR_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'MGKISR_PLUGIN_URL' )              && define( 'MGKISR_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );



include(MGKISR_PLUGIN_PATH."/includes/magik-menu-panel.php");

register_activation_hook( __FILE__, array( 'MGK_InfiniteScroll', 'mgkisr_install' ) );


if ( ! class_exists( 'MGK_InfiniteScroll' ) ) {
class MGK_InfiniteScroll
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {

    	add_action( 'admin_menu', array( $this, 'mgkisr_add_plugin_page' ) );
    	add_action( 'admin_init', array( $this, 'mgkisr_page_init' ) );

    	add_action( 'admin_enqueue_scripts', array( $this, 'mgkisr_admin_enqueue_scripts' ) );
    	
    }



    static function mgkisr_install() {


      add_option( 'mgkisr_version', MGKISR_PLUGIN_VERSION );

      register_uninstall_hook( __FILE__, array( 'MGK_InfiniteScroll','mgkisr_uninstall' ));
    }



    static function mgkisr_uninstall() {


     delete_option( 'mgkisr_version');


   }

     /**
     * Enqueue script and styles in admin side
     *
     * Add style and scripts to administrator
     *
     * @return void
     * @since    1.0

     */
     public function mgkisr_admin_enqueue_scripts() {
     	global $pagenow;


     	wp_enqueue_media();




      wp_enqueue_style('mgkisr_admin_setting', MGKISR_PLUGIN_URL . '/assets/css/mgkisr_admin_setting.css', array(), '');



      wp_enqueue_script( 'mgkisr_uploader', MGKISR_PLUGIN_URL . '/assets/js/mgkisr_uploader.js', array( 'jquery' ), '1.0.0', true );



    }




    /**
     * Add options page
     */
    public function mgkisr_add_plugin_page()
    {
      if ( ! empty( $this->_panel ) ) {
        return;
      }

      $mgk_infinitescroll_menu_panel= new MGK_InfiniteScroll_Menu_Panel();
      $parent_slug= $mgk_infinitescroll_menu_panel->add_menu_page();

      add_submenu_page($parent_slug,
        esc_attr__('Magik Infinite Scroller',"magik-infinite-scroller"), 
        esc_attr__('Magik Infinite Scroller',"magik-infinite-scroller"),
        'manage_options',
        'mgkisr_admin_settings',
        array( $this, 'mgkisr_create_admin_page' )
      );


    }

    /**
     * Options page callback
     */
    public function mgkisr_create_admin_page()
    {
       if ( ! current_user_can( 'manage_options' ) ) {
          wp_die( esc_html__( 'You do not have sufficient permissions to access this page.','magik-infinite-scroller' ) );
         }
        // Set class property
    	$this->options = get_option( 'mgkisr_option' );
    	?>
    	<div class="mgkisr_wrap">

    		<form id="mgkisr_scolling" method="post" action="options.php" enctype=”multipart/form-data”>
    			<?php
                // This prints out all hidden setting fields
    			settings_fields( 'mgkisr_option_group' );
    			do_settings_sections( 'mgkisr_admin_settings' );
    			submit_button();
    			?>
    		</form>
    	</div>
    	<?php
    }

    /**
     * Register and add settings
     */
    public function mgkisr_page_init()
    {        
    	register_setting(
            'mgkisr_option_group', // Option group
            'mgkisr_option', // Option name
            array( $this, 'mgkisr_sanitize' ) // Sanitize
          );

    	add_settings_section(
            'mgkisr_setting_section', // ID
            esc_attr__('Magik Infinite Scroller','magik-infinite-scroller'), // Title
            array( $this, 'mgkisr_print_section_info' ), // Callback
            'mgkisr_admin_settings' // Page
          ); 

    	add_settings_field(
            'mgkisr_enable', // ID
              esc_attr__('Enable Infinite Scoll','magik-infinite-scroller'), // Title 
            array( $this, 'mgkisr_enable_callback' ), // Callback
            'mgkisr_admin_settings', // Page
            'mgkisr_setting_section' // Section           
          );   

    	add_settings_field(
            'mgkisr_nav_selector', // ID
              esc_attr__('Navigation Selector','magik-infinite-scroller'), // Title 
            array( $this, 'mgkisr_nav_selector_callback' ), // Callback
            'mgkisr_admin_settings', // Page
            'mgkisr_setting_section' // Section           
          );      

    	add_settings_field(
    		'mgkisr_next_selector', 
        esc_attr__('Next Selector','magik-infinite-scroller'), 
        array( $this, 'mgkisr_next_selector_callback' ), 
        'mgkisr_admin_settings', 
        'mgkisr_setting_section'
      );

    	add_settings_field(
    		'mgkisr_item_selector', 
        esc_attr__('Item Selector','magik-infinite-scroller'), 
        array( $this, 'mgkisr_item_selector_callback' ), 
        'mgkisr_admin_settings', 
        'mgkisr_setting_section'
      ); 

    	add_settings_field(
    		'mgkisr_content_selector', 
        esc_attr__('Content Selector','magik-infinite-scroller'), 
        array( $this, 'mgkisr_content_selector_callback' ), 
        'mgkisr_admin_settings', 
        'mgkisr_setting_section'
      ); 

    	add_settings_field(
    		'mgkisr_item_class', 
        esc_attr__('Item Class','magik-infinite-scroller'), 
        array( $this, 'mgkisr_item_class_callback' ), 
        'mgkisr_admin_settings', 
        'mgkisr_setting_section'
      ); 

    	add_settings_field(
    		'mgkisr_loader_image', 
        esc_attr__('Loader Image','magik-infinite-scroller'), 
        array( $this, 'mgkisr_loader_image_callback' ), 
        'mgkisr_admin_settings', 
        'mgkisr_setting_section'
      );     

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function mgkisr_sanitize( $input )
    {
    	$new_input = array();
    	
    	if( isset( $input['mgkisr_enable'] ) ) {
    		$new_input['mgkisr_enable'] = sanitize_text_field( $input['mgkisr_enable'] );
      }

    	if( isset( $input['mgkisr_nav_selector'] ) ) {
    		$new_input['mgkisr_nav_selector'] = sanitize_text_field( $input['mgkisr_nav_selector'] );
      }

    	if( isset( $input['mgkisr_next_selector'] ) ) {
    		$new_input['mgkisr_next_selector'] = sanitize_text_field( $input['mgkisr_next_selector'] );
      }

    	if( isset( $input['mgkisr_item_selector'] ) ) {
    		$new_input['mgkisr_item_selector'] = sanitize_text_field( $input['mgkisr_item_selector'] );
      }

    	if( isset( $input['mgkisr_content_selector'] ) ) {
    		$new_input['mgkisr_content_selector'] = sanitize_text_field( $input['mgkisr_content_selector'] );
      }

    	if( isset( $input['mgkisr_item_class'] ) ) {
    		$new_input['mgkisr_item_class'] = sanitize_text_field( $input['mgkisr_item_class'] );
      }

    	if( isset( $input['mgkisr_loader_image'] ) ) {
    		$new_input['mgkisr_loader_image'] = sanitize_text_field( $input['mgkisr_loader_image'] );
      }




    	return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function mgkisr_print_section_info()
    {

    }


    
    public function mgkisr_enable_callback()
    {
    	$checked ="";
    	if(isset($this->options['mgkisr_enable'])) { $checked = ' checked="checked" '; }
    	echo'
    	<input type="checkbox" id="mgkisr_enable" name="mgkisr_option[mgkisr_enable]" value="1" '.$checked .'/>' ;

    }
    
    public function mgkisr_nav_selector_callback()
    {
    	printf(
    		'<input type="text" id="mgkisr_nav_selector" name="mgkisr_option[mgkisr_nav_selector]" value="%s" />',
    		isset( $this->options['mgkisr_nav_selector'] ) ? esc_attr( $this->options['mgkisr_nav_selector']) : '.woocommerce-pagination li'
    	);
    }


    public function mgkisr_next_selector_callback()
    {
     printf(
      '<input type="text" id="mgkisr_next_selector" name="mgkisr_option[mgkisr_next_selector]" value="%s" />',
      isset( $this->options['mgkisr_next_selector'] ) ? esc_attr( $this->options['mgkisr_next_selector']) : '.woocommerce-pagination li a.next'
    );
   }


   public function mgkisr_item_selector_callback()
   {
     printf(
      '<input type="text" id="mgkisr_item_selector" name="mgkisr_option[mgkisr_item_selector]" value="%s" />',
      isset( $this->options['mgkisr_item_selector'] ) ? esc_attr( $this->options['mgkisr_item_selector']) : 'li.item'
    );
   }


   public function mgkisr_content_selector_callback()
   {
     printf(
      '<input type="text" id="mgkisr_content_selector" name="mgkisr_option[mgkisr_content_selector]" value="%s" />',
      isset( $this->options['mgkisr_content_selector'] ) ? esc_attr( $this->options['mgkisr_content_selector']) : '.category-products ul.products'
    );
   }


   public function mgkisr_item_class_callback()
   {
     printf(
      '<input type="text" id="mgkisr_item_class" name="mgkisr_option[mgkisr_item_class]" value="%s" />',
      isset( $this->options['mgkisr_item_class'] ) ? esc_attr( $this->options['mgkisr_item_class']) : 'item col-lg-4 col-md-4 col-sm-4 col-xs-6'
    );
   }


   public function mgkisr_loader_image_callback()
   {


     echo'<input id="upload_image" type="text" size="36" name="mgkisr_option[mgkisr_loader_image]" value="'. $this->options['mgkisr_loader_image'].'"/><input id="upload_image_button" class="button" type="button" value="Upload Menu" />';
   }


 }
}


 if( is_admin() ) {
   $mgkisr_InfiniteScroll = new MGK_InfiniteScroll();
 }







 //Infintie scolloer front class
if ( ! class_exists( 'MGK_Front_InfiniteScroll' ) ) {
 class MGK_Front_InfiniteScroll
 {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {



     add_action('wp_enqueue_scripts', array($this,'mgkisr_front_enqueue_scripts'));   


     
   }




   public function mgkisr_front_enqueue_scripts() {

    
     wp_enqueue_style('mgkisr-style', MGKISR_PLUGIN_URL . '/assets/css/mgkisr_style.css', array(), ''); 

    $mgkisr_option=get_option( 'mgkisr_option' );
    
     if ( isset($mgkisr_option['mgkisr_enable'] ) && $mgkisr_option['mgkisr_enable'] == '1' ) { 
     if (function_exists( 'WC' ) && (is_shop() || is_product_category()))
    {
     $is_shop=true;
   }
   else{
     $is_shop=false;
   }


   if(isset($mgkisr_option['mgkisr_loader_image']) && !empty($mgkisr_option['mgkisr_loader_image']))
   {
    $loader_img=$mgkisr_option['mgkisr_loader_image'];
  }
  else
  {
    $loader_img=MGKISR_PLUGIN_URL."/assets/images/mgkloading.gif";
  }

  wp_enqueue_script( 'mgkisr-scoll', MGKISR_PLUGIN_URL . '/assets/js/mgkisr_scoll.js', array( 'jquery' ), '1.0.0', true );

  wp_localize_script(
    'mgkisr-scoll',
    'mgkisr_scoll_vars',             array(
      'infinite_scroll'        => isset($mgkisr_option['mgkisr_enable'])? $mgkisr_option['mgkisr_enable']:0,
      'nav_selector'    => $mgkisr_option['mgkisr_nav_selector'], 
      'next_selector'    => $mgkisr_option['mgkisr_next_selector'],
      'item_selector'   => $mgkisr_option['mgkisr_item_selector'],
      'item_class'   => $mgkisr_option['mgkisr_item_class'],
      'content_selector'  => $mgkisr_option['mgkisr_content_selector'],
      'is_shop'=> $is_shop,

      'loader_image'  => $loader_img
    )
  );
}
}

}
}

$mgkssh_front_InfiniteScroll = new MGK_Front_InfiniteScroll();


?>
