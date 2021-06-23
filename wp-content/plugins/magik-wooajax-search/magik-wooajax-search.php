<?php
/**
 * Plugin Name: Magik WooAjax Search
 * Plugin URI: https://www.magikcommerce.com/
 * Description: Magik WooAjax Search provides Autocomplete search bar for WooCommerce Store.
 * Version: 1.0
 * Author: MagikCommerce
 * Requires at least: WP 5.0
 * Tested up to: WP 5.1
 * WC requires at least: 3.5.5
 * WC tested up to: 3.5.5
 * Author URI: https://www.magikcommerce.com/
 * Text Domain: magik-wooajax-search
 * Domain Path: /languages/
 */


if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly


! defined( 'MGKWOOAS_PLUGIN' )                  && define( 'MGKWOOAS_PLUGIN', true);
! defined( 'MGKWOOAS_PLUGIN_VERSION' )          && define( 'MGKWOOAS_PLUGIN_VERSION', '1.0.0');
! defined( 'MGKWOOAS_PLUGIN_PATH' )             && define( 'MGKWOOAS_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'MGKWOOAS_PLUGIN_URL' )              && define( 'MGKWOOAS_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );



include("includes/magik-menu-panel.php");
include("includes/funtions.php");




register_activation_hook( __FILE__, array( 'MGK_WooAjaxSearch', 'mgkwooas_install' ) );

if ( ! class_exists( 'MGK_WooAjaxSearch' ) ) {
class MGK_WooAjaxSearch
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

    	add_action( 'admin_menu', array( $this, 'mgkwooas_add_plugin_page' ) );
    	add_action( 'admin_init', array( $this, 'mgkwooas_page_init' ) );

    	add_action( 'admin_enqueue_scripts', array( $this, 'mgkwooas_admin_enqueue_scripts' ) );


    }


    static function mgkwooas_install() {


      add_option( 'mgkwooas_version', MGKWOOAS_PLUGIN_VERSION );

      register_uninstall_hook( __FILE__, array( 'MGK_WooAjaxSearch','mgkwooas_uninstall' ));
    }



    static function mgkwooas_uninstall() {


     delete_option( 'mgkwooas_version');


   }



     /**
     * Enqueue script and styles in admin side
     *
     * Add style and scripts to administrator
     *
     * @return void
     * @since    1.0
  
     */


     public function mgkwooas_admin_enqueue_scripts() {
     	global $pagenow;


     	wp_enqueue_media();
      wp_enqueue_style('mgkwooas_admin_setting', MGKWOOAS_PLUGIN_URL . '/assets/css/mgkwooas_admin_setting.css', array(), '');
 


    }




    /**
     * Add options page
     */
    public function mgkwooas_add_plugin_page()
    {
      if ( ! empty( $this->_panel ) ) {
        return;
      }

      $mgk_wooajaxsearch_menu_panel= new MGK_WooAjaxSearch_Menu_Panel();
      $parent_slug= $mgk_wooajaxsearch_menu_panel->add_menu_page();

      add_submenu_page($parent_slug,
        esc_attr__('Magik WooAjax Search',"magik-wooajax-search"),
        esc_attr__('Magik WooAjax Search',"magik-wooajax-search"),
        'manage_options',
        'mgkwooas_admin_settings',
        array( $this, 'mgkwooas_create_admin_page' )
      );


    }

    /**
     * Options page callback
     */
    public function mgkwooas_create_admin_page()
    {
       if ( ! current_user_can( 'manage_options' ) ) {
          wp_die( esc_html__( 'You do not have sufficient permissions to access this page.','magik-wooajax-search' ) );
         }
        // Set class property
    	$this->options = get_option( 'mgkwooas_option' );
    	?>
    	<div class="mgkwooas_wrap">


       
        <form id="mgkwooas_scolling" method="post" action="options.php" enctype=”multipart/form-data”>
         <?php
                // This prints out all hidden setting fields
         settings_fields( 'mgkwooas_option_group' );
         do_settings_sections( 'mgkwooas_admin_settings' );
         submit_button();
         ?>
       </form>

       <p><?php esc_html_e('For Header section Shortcode is:','magik-wooajax-search');?> <b> [mgk_woocommerce_ajax_search] </b><br>
        <?php esc_html_e('For Widget section Shortcode is:','magik-wooajax-search');?> <b> [mgk_woocommerce_ajax_search section='widget']</b><br>
       <?php esc_html_e('For Pages section Shortcode is:','magik-wooajax-search');?> <b> [mgk_woocommerce_ajax_search section='page']</b>


       </p>
     </div>

     <?php
   }

    /**
     * Register and add settings
     */
    public function mgkwooas_page_init()
    {        
    	register_setting(
            'mgkwooas_option_group', // Option group
            'mgkwooas_option', // Option name
            array( $this, 'mgkwooas_sanitize' ) // Sanitize
          );

    	add_settings_section(
            'mgkwooas_basicsetting_section', // ID
              esc_attr__('Basic Settings','magik-wooajax-search'),  // Title
            array( $this, 'mgkwooas_print_section_info' ), // Callback
            'mgkwooas_admin_settings' // Page
          ); 

    	add_settings_field(
            'mgkwooas_enable', // ID
            esc_attr__('Enable Magik WooAjax Search','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_enable_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          ); 


      add_settings_field(
            'mgkwooas_suggetionlimit', // ID
            esc_attr__('Suggestions limit','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_suggetionlimit_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_minimumchar', // ID
            esc_attr__('Minimum characters','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_minimumchar_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_submitbtn', // ID
            esc_attr__('Show submit button','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_submitbtn_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_searchplaceholder', // ID
            esc_attr__('Search input placeholder','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_searchplaceholder_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          );
      add_settings_field(
            'mgkwooas_submitbtntext', // ID
            esc_attr__('Search submit button text','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_submitbtntext_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_basicsetting_section' // Section           
          );

      add_settings_section(
            'mgkwooas_searchsetting_section', // ID
              esc_attr__('Search Settings','magik-wooajax-search'),  // Title
            array( $this, 'mgkwooas_print_section_info2' ), // Callback
            'mgkwooas_admin_settings' // Page
          );

      add_settings_field(
            'mgkwooas_search_category', // ID
            esc_attr__('Search in Product Catgories','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_search_category_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_search_tags', // ID
            esc_attr__('Search in Product Tags','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_search_tags_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_search_product_content', // ID
            esc_attr__('Search in Product Content','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_search_product_content_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_search_product_excerpt', // ID
            esc_attr__('Search in Product Excerpt','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_search_product_excerpt_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_search_product_sku', // ID
            esc_attr__('Search in Product Sku','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_search_product_sku_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_exclude_out_of_stock', // ID
            esc_attr__('Exclude out of stock','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_exclude_out_of_stock_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_searchsetting_section' // Section           
          ); 
      

      add_settings_section(
            'mgkwooas_advancesetting_section', // ID
              esc_attr__('Advance Settings','magik-wooajax-search'),  // Title
            array( $this, 'mgkwooas_print_section_info2' ), // Callback
            'mgkwooas_admin_settings' // Page
          ); 

      add_settings_field(
            'mgkwooas_category_match', // ID
            esc_attr__('Show matching categories','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_category_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_tags_match', // ID
            esc_attr__('Show matching tags','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_tags_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_product_image', // ID
            esc_attr__('Show product image','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_product_image_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_product_price', // ID
            esc_attr__('Show price','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_product_price_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_product_desc', // ID
            esc_attr__('Show product description','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_product_desc_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 
      add_settings_field(
            'mgkwooas_product_sku', // ID
            esc_attr__('Show SKU','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_product_sku_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_sale_badge', // ID
            esc_attr__('Show Sale Badge','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_sale_badge_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 

      add_settings_field(
            'mgkwooas_featured_badge', // ID
            esc_attr__('Show Featured Badge','magik-wooajax-search'), // Title 
            array( $this, 'mgkwooas_featured_badge_callback' ), // Callback
            'mgkwooas_admin_settings', // Page
            'mgkwooas_advancesetting_section' // Section           
          ); 
      
    


    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function mgkwooas_sanitize( $input )
    {
    	$new_input = array();
    	
    	if( isset( $input['mgkwooas_enable'] ) ) {
    		$new_input['mgkwooas_enable'] = sanitize_text_field( $input['mgkwooas_enable'] );
      }

      if( isset( $input['mgkwooas_suggetionlimit'] ) ) {
        $new_input['mgkwooas_suggetionlimit'] = sanitize_text_field( $input['mgkwooas_suggetionlimit'] );
      }

      if( isset( $input['mgkwooas_minimumchar'] ) ) {
        $new_input['mgkwooas_minimumchar'] = sanitize_text_field( $input['mgkwooas_minimumchar'] );
      }

      if( isset( $input['mgkwooas_submitbtn'] ) ) {
        $new_input['mgkwooas_submitbtn'] = sanitize_text_field( $input['mgkwooas_submitbtn'] );
      }

      if( isset( $input['mgkwooas_category_match'] ) ) {
        $new_input['mgkwooas_category_match'] = sanitize_text_field( $input['mgkwooas_category_match'] );
      }

      if( isset( $input['mgkwooas_tags_match'] ) ) {
        $new_input['mgkwooas_tags_match'] = sanitize_text_field( $input['mgkwooas_tags_match'] );
      }
 
      if( isset( $input['mgkwooas_product_image'] ) ) {
        $new_input['mgkwooas_product_image'] = sanitize_text_field( $input['mgkwooas_product_image'] );
      }

      if( isset( $input['mgkwooas_product_price'] ) ) {
        $new_input['mgkwooas_product_price'] = sanitize_text_field( $input['mgkwooas_product_price'] );
      }

      if( isset( $input['mgkwooas_product_desc'] ) ) {
        $new_input['mgkwooas_product_desc'] = sanitize_text_field( $input['mgkwooas_product_desc'] );
      }

      if( isset( $input['mgkwooas_product_sku'] ) ) {
        $new_input['mgkwooas_product_sku'] = sanitize_text_field( $input['mgkwooas_product_sku'] );
      }

      if( isset( $input['mgkwooas_sale_badge'] ) ) {
        $new_input['mgkwooas_sale_badge'] = sanitize_text_field( $input['mgkwooas_sale_badge'] );
      }

      if( isset( $input['mgkwooas_featured_badge'] ) ) {
        $new_input['mgkwooas_featured_badge'] = sanitize_text_field( $input['mgkwooas_featured_badge'] );
      }

      if( isset( $input['mgkwooas_search_category'] ) ) {
        $new_input['mgkwooas_search_category'] = sanitize_text_field( $input['mgkwooas_search_category'] );
      }

      if( isset( $input['mgkwooas_search_tags'] ) ) {
        $new_input['mgkwooas_search_tags'] = sanitize_text_field( $input['mgkwooas_search_tags'] );
      }

      if( isset( $input['mgkwooas_search_product_content'] ) ) {
        $new_input['mgkwooas_search_product_content'] = sanitize_text_field( $input['mgkwooas_search_product_content'] );
      }

      if( isset( $input['mgkwooas_search_product_excerpt'] ) ) {
        $new_input['mgkwooas_search_product_excerpt'] = sanitize_text_field( $input['mgkwooas_search_product_excerpt'] );
      }

      if( isset( $input['mgkwooas_search_product_sku'] ) ) {
        $new_input['mgkwooas_search_product_sku'] = sanitize_text_field( $input['mgkwooas_search_product_sku'] );
      }
      if( isset( $input['mgkwooas_exclude_out_of_stock'] ) ) {
        $new_input['mgkwooas_exclude_out_of_stock'] = sanitize_text_field( $input['mgkwooas_exclude_out_of_stock'] );
      }

      if( isset( $input['mgkwooas_searchplaceholder'] ) ) {
        $new_input['mgkwooas_searchplaceholder'] = sanitize_text_field( $input['mgkwooas_searchplaceholder'] );
      }

      if( isset( $input['mgkwooas_submitbtntext'] ) ) {
        $new_input['mgkwooas_submitbtntext'] = sanitize_text_field( $input['mgkwooas_submitbtntext'] );
      }
      

 

      return $new_input;
    }

        /** 
         * Print the Section text
         */

        
        public function mgkwooas_print_section_info()
        {
        	
        }

        public function mgkwooas_print_section_info2()
        {

        }
        public function mgkwooas_print_section_info3()
        {

        }




        public function mgkwooas_enable_callback()
        {
        	$checked ="";
        	if(isset($this->options['mgkwooas_enable'])) { $checked = ' checked="checked" '; }
        	echo'
        	<input type="checkbox" id="mgkwooas_enable" name="mgkwooas_option[mgkwooas_enable]" value="1" '.$checked .'/>' ;

        }

        
        public function mgkwooas_suggetionlimit_callback()
        {
          $val ="6";
          if(isset($this->options['mgkwooas_suggetionlimit'])) { $val = $this->options['mgkwooas_suggetionlimit']; }
          echo'
          <input type="number" id="mgkwooas_suggetionlimit" name="mgkwooas_option[mgkwooas_suggetionlimit]" value="'.$val .'"/>' ;

        }

        public function mgkwooas_minimumchar_callback()
        {
          $val ="3";
          if(isset($this->options['mgkwooas_minimumchar'])) { $val = $this->options['mgkwooas_minimumchar']; }
          echo'
          <input type="number" id="mgkwooas_minimumchar" name="mgkwooas_option[mgkwooas_minimumchar]" value="'.$val .'"/>' ;

        }
        
        public function mgkwooas_submitbtn_callback()
        {
          $checked ="";
          if(isset($this->options['mgkwooas_submitbtn'])) { $checked = ' checked="checked" '; }
          echo'
          <input type="checkbox" id="mgkwooas_submitbtn" name="mgkwooas_option[mgkwooas_submitbtn]" value="1" '.$checked .'/>' ;

        }



        public function mgkwooas_submitbtntext_callback()
        {
        
          $val =esc_html__( 'Search', 'magik-wooajax-search');
         if(isset($this->options['mgkwooas_submitbtntext'])) { $val = $this->options['mgkwooas_submitbtntext']; }
         echo'
         <input type="text" id="mgkwooas_submitbtntext" name="mgkwooas_option[mgkwooas_submitbtntext]" value="'.$val .'"/>' ;

       }



       public function mgkwooas_searchplaceholder_callback()
       {
         $val =esc_html__( 'Search Here', 'magik-wooajax-search');
         if(isset($this->options['mgkwooas_searchplaceholder'])) { $val = $this->options['mgkwooas_searchplaceholder']; }
         echo'
         <input type="text" id="mgkwooas_searchplaceholder" name="mgkwooas_option[mgkwooas_searchplaceholder]" value="'.$val .'"/>' ;

       }
       




       public function mgkwooas_category_callback()
       {
        $checked ="";
        if(isset($this->options['mgkwooas_category_match'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_category_match" name="mgkwooas_option[mgkwooas_category_match]" value="1" '.$checked .'/>' ;

      }

      
      public function mgkwooas_tags_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_tags_match'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_tags_match" name="mgkwooas_option[mgkwooas_tags_match]" value="1" '.$checked .'/>' ;

      }



      public function mgkwooas_search_category_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_search_category'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_search_category" name="mgkwooas_option[mgkwooas_search_category]" value="1" '.$checked .'/>' ;

      }



      public function mgkwooas_search_tags_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_search_tags'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_search_tags" name="mgkwooas_option[mgkwooas_search_tags]" value="1" '.$checked .'/>' ;

      }



      public function mgkwooas_search_product_content_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_search_product_content'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_search_product_content" name="mgkwooas_option[mgkwooas_search_product_content]" value="1" '.$checked .'/>' ;

      }

      public function mgkwooas_search_product_excerpt_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_search_product_excerpt'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_search_product_excerpt" name="mgkwooas_option[mgkwooas_search_product_excerpt]" value="1" '.$checked .'/>' ;

      }

      public function mgkwooas_search_product_sku_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_search_product_sku'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_search_product_sku" name="mgkwooas_option[mgkwooas_search_product_sku]" value="1" '.$checked .'/>' ;

      }


      public function mgkwooas_exclude_out_of_stock_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_exclude_out_of_stock'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_exclude_out_of_stock" name="mgkwooas_option[mgkwooas_exclude_out_of_stock]" value="1" '.$checked .'/>' ;

      }




      public function mgkwooas_product_image_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_product_image'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_product_image" name="mgkwooas_option[mgkwooas_product_image]" value="1" '.$checked .'/>' ;

      }



      public function mgkwooas_product_price_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_product_price'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_product_price" name="mgkwooas_option[mgkwooas_product_price]" value="1" '.$checked .'/>' ;

      }



      public function mgkwooas_product_desc_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_product_desc'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_product_desc" name="mgkwooas_option[mgkwooas_product_desc]" value="1" '.$checked .'/>' ;

      }


      
      public function mgkwooas_product_sku_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_product_sku'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_product_sku" name="mgkwooas_option[mgkwooas_product_sku]" value="1" '.$checked .'/>' ;

      }


      public function mgkwooas_sale_badge_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_sale_badge'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_sale_badge" name="mgkwooas_option[mgkwooas_sale_badge]" value="1" '.$checked .'/>' ;

      }


      public function mgkwooas_featured_badge_callback()
      {
        $checked ="";
        if(isset($this->options['mgkwooas_featured_badge'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkwooas_featured_badge" name="mgkwooas_option[mgkwooas_featured_badge]" value="1" '.$checked .'/>' ;

      }





}

  }

  if( is_admin() ) {
   $mgkwooas_WooAjaxSearch = new MGK_WooAjaxSearch();
}









   // WooCoomerce ajax product search
if ( ! class_exists( 'MGK_Front_WooAjaxSearch' ) ) {
 class MGK_Front_WooAjaxSearch
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



      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );


      add_action( 'wp_ajax_nopriv_mgkwoo_ajax_search_products', array( $this, 'mgkwooas_products_ajax_search' ) );
      add_action( 'wp_ajax_mgkwoo_ajax_search_products', array( $this, 'mgkwooas_products_ajax_search' ) );
      add_shortcode( 'mgk_woocommerce_ajax_search', array( $this, 'mgkwooas_woo_ajax_search_shortcode' ) );


      add_filter( 'posts_search', array( $this, 'mgkwooas_search_filters' ), 501, 2 );
      add_filter( 'posts_where', array( $this, 'mgkwooas_woo_excerpt_search' ), 100, 2 );
      add_filter('posts_distinct', array($this, 'mgkwooas_search_distinct'), 501, 2);
      add_filter( 'posts_join', array( $this, 'mgkwooas_search_filters_join' ), 501, 2 );
      add_filter( 'pre_get_posts', array( $this, 'mgkwooas_set_default_search_query' ), 501 );


    }



        /**
         * Enqueue styles and scripts
         * 
         * @access public
         * @return void 
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {

          wp_enqueue_script('mgkwooas_autocomplete', MGKWOOAS_PLUGIN_URL . '/assets/js/mgkwooas-autocomplete.js', array('jquery'), '1.2.7', true);

          wp_enqueue_script('mgkwooas_frontend', MGKWOOAS_PLUGIN_URL . '/assets/js/mgkwooas-frontend.js', array('jquery'), '1.0', true);
          wp_enqueue_style('mgkwooas-style', MGKWOOAS_PLUGIN_URL . '/assets/css/mgkwooas_style.css', array(), ''); 


          $mgkwooas_option=get_option('mgkwooas_option');
          $mgkwooas_productimage=0;
          $mgkwooas_productprice=0;
          $mgkwooas_productdesc=0;
          $mgkwooas_productsku=0;
          $mgkwooas_productsalebadge=0;
          $mgkwooas_productfeaturedbadge=0;
          $mgkwooas_loader_image="";
          

          $sale_badge   = esc_html__( 'sale',  'magik-wooajax-search' );
          $featured_badge = esc_html__( 'featured',  'magik-wooajax-search' );     

          if(isset($mgkwooas_option['mgkwooas_product_image']))
          {
           $mgkwooas_productimage=$mgkwooas_option['mgkwooas_product_image'];
         }
         if(isset($mgkwooas_option['mgkwooas_product_price']))
         {
           $mgkwooas_productprice=$mgkwooas_option['mgkwooas_product_price'];
         }
         if(isset($mgkwooas_option['mgkwooas_product_desc']))
         {
           $mgkwooas_productdesc=$mgkwooas_option['mgkwooas_product_desc'];
         }
         if(isset($mgkwooas_option['mgkwooas_product_sku']))
         {
           $mgkwooas_productsku=$mgkwooas_option['mgkwooas_product_sku'];
         }
         if(isset($mgkwooas_option['mgkwooas_sale_badge']))
         {
           $mgkwooas_productsalebadge=$mgkwooas_option['mgkwooas_sale_badge'];
         }
         if(isset($mgkwooas_option['mgkwooas_featured_badge']))
         {
           $mgkwooas_productfeaturedbadge=$mgkwooas_option['mgkwooas_featured_badge'];
         }
         $min_char=$mgkwooas_option['mgkwooas_minimumchar'];

    

         

         wp_localize_script( 'mgkwooas_frontend', 'mgkwooas_params', array(
        
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'product_image'=>$mgkwooas_productimage,
          'product_price'=>$mgkwooas_productprice,
          'product_desc'=>$mgkwooas_productdesc,
          'product_sku'=>$mgkwooas_productsku,
          'product_salebadge'=>$mgkwooas_productsalebadge,
          'product_featuredbadge'=>$mgkwooas_productfeaturedbadge,
          'product_saletext'=>$sale_badge,
          'product_featuredtext'=>$featured_badge

        ));

         

       }

        
         public function mgkwooas_woo_ajax_search_shortcode( $atts ) {
          $defaults_array=array('section' => 'header');
          
          $args  = shortcode_atts( $defaults_array, $atts );
          
          $class="mgkwooas_header";
          if(!empty($args['section']))
          {
            if($args['section']=="page")
            {
              $class="mgkwooas_page";
            }
            else if($args['section']=="widget")
            {
             $class="mgkwooas_widget";
           }
           else{
            $class="mgkwooas_header";
          }
        }
        else{
          $class="mgkwooas_header";
        }
        
        ob_start();

        $submit_btn=0;
        $submit_btntext="";
        $mgkwooas_option=get_option('mgkwooas_option');
        
        if(isset($mgkwooas_option['mgkwooas_enable']) &&  $mgkwooas_option['mgkwooas_enable']==1)
        {
          $search_limit=$mgkwooas_option['mgkwooas_suggetionlimit'];
          $min_char=$mgkwooas_option['mgkwooas_minimumchar'];
          if(isset($mgkwooas_option['mgkwooas_submitbtn']))
          {
           $submit_btn=$mgkwooas_option['mgkwooas_submitbtn'];
           $submit_btntext=$mgkwooas_option['mgkwooas_submitbtntext'];
           
         }
         $searchplaceholder=$mgkwooas_option['mgkwooas_searchplaceholder'];

         ?>


         <div class="mgkwooas-ajaxsearchform-container <?php echo esc_html($class) ;?>">
          <form role="search" method="get" class="mgkwooas-ajaxsearchform" action="<?php echo esc_url( home_url( '/'  ) ) ?>">
            <div>
              <label class="screen-reader-text"><?php esc_html_e( 'Search for:', 'magik-wooajax-search' ); ?></label>

              <input type="text"
              value="<?php echo get_search_query() ?>"
              name="s"             
              class="mgkwooas-searchstr"
              placeholder="<?php echo esc_html($searchplaceholder); ?>"         
              data-min-chars="<?php echo esc_html($min_char); ?>"/>
              <?php if($submit_btn==1)
              {?>
                <button type="submit" class="mgkwooas-searchbtn"><span><?php echo esc_html($submit_btntext); ?></span>
                </button>
              <?php } ?>
              <input type="hidden" name="post_type" value="product" />
              <?php do_action( 'wpml_add_language_form_field' ); ?>

            </div>
          </form>
        </div>

        <?php
      }
      return ob_get_clean();
    }

    
        /**
         * Perform ajax search products
         */
        public function mgkwooas_products_ajax_search() {

          global $woocommerce;


          if ( !defined( 'MGKWOOAS_AJAX_SEARCH' ) ) {
            define( 'MGKWOOAS_AJAX_SEARCH', true );
          }



          $mgkwooas_option=get_option( 'mgkwooas_option' );
          $suggetionlimit=$mgkwooas_option['mgkwooas_suggetionlimit'];

          $output  = array();
          $results = array();

          $search_keyword =  $_REQUEST['query'];




          $product_results   = array();

          $search_limit= $suggetionlimit;

          /* SEARCH IN WOO CATEGORIES */

          if ( $search_limit > 0 ) {

            if (isset($mgkwooas_option['mgkwooas_category_match']) && $mgkwooas_option['mgkwooas_category_match'] == '1' ) {

              $results = array_merge( $this->mgkwooas_get_categories( $search_keyword ), $results );

          // Update slots
              $search_limit= $suggetionlimit - count( $results );
            } 


          }

          /* END SEARCH IN WOO CATEGORIES */



          /* SEARCH IN WOO TAGS */
          if ( $search_limit > 0 ) {

            if (isset($mgkwooas_option['mgkwooas_tags_match']) &&  $mgkwooas_option['mgkwooas_tags_match'] == '1' ) {

              $results = array_merge( $this->mgkwooas_get_tags( $search_keyword ), $results );
              
              // Update slots
              $search_limit= $suggetionlimit - count( $results );
            }
          }

          /* END SEARCH IN WOO TAGS */



          /* SEARCH IN PRODUCT TAGS */
          if ( $search_limit > 0 ) {

            if (isset($mgkwooas_option['mgkwooas_search_tags']) &&  $mgkwooas_option['mgkwooas_search_tags'] == '1' ) {

              $results = array_merge( $this->mgkwooas_get_product_tags( $search_keyword ), $results );
              
              

              // Update slots
              $search_limit= $suggetionlimit - count( $results );
            }
          }

          /* END SEARCH IN PRODUCT TAGS */



          /* SEARCH IN PRODUCT CATEGORY */
          if ( $search_limit > 0 ) {

            if (isset($mgkwooas_option['mgkwooas_search_category']) &&  $mgkwooas_option['mgkwooas_search_category'] == '1' ) {

              $results = array_merge( $this->mgkwooas_get_product_category( $search_keyword ), $results );
              
              // Update slots
              $search_limit= $suggetionlimit - count( $results );
            }
          }/* END SEARCH IN PRODUCT CATEGORY */


          if ( $search_limit > 0 ) {

            $ordering_args = $woocommerce->query->get_catalog_ordering_args( 'title', 'asc' );

            $args = array(
              's'                   => $search_keyword,
              'post_type'           => 'product',
              'post_status'         => 'publish',
              'ignore_sticky_posts' => 1,
              'orderby'             => $ordering_args['orderby'],
              'order'               => $ordering_args['order'],
              'suppress_filters'   =>false,
              'posts_per_page'      => apply_filters( 'mgkwooas_ajax_search_products_posts_per_page', $suggetionlimit)

            );


            // Backward compatibility WC < 3.0
            if ( version_compare( WC()->version, '3.0', '<' ) ) {
              $args['meta_query'] = $this->mgkwooas_get_meta_query();
            }else{
              $args['tax_query'] = $this->mgkwooas_get_tax_query();
            }

            $args = apply_filters('mgkwooas_products_args', $args);


            

            $products = get_posts( $args );

            if ( ! empty( $products ) ) {

              foreach ( $products as $post ) {
                $product = wc_get_product( $post );

                $product_image="";
                $price="";
                $desc ="";
                $sku="";
                $on_sale="";
                $featured="";




                if ( isset($mgkwooas_option['mgkwooas_product_image']) && $mgkwooas_option['mgkwooas_product_image'] == '1' ) {

                  $product_image=$product->get_image();
                }

                // Get price
                if ( isset($mgkwooas_option['mgkwooas_product_price']) &&  $mgkwooas_option['mgkwooas_product_price' ] == '1' ) {
                  $price = $product->get_price_html();
                }

                // Get description
                if ( isset($mgkwooas_option['mgkwooas_product_desc']) && $mgkwooas_option['mgkwooas_product_desc'] == '1' ) {

                  $desc = mgkwooas_get_product_desc( $product->get_id(),'60' );
                }

                // Get SKU
                if ( isset($mgkwooas_option['mgkwooas_product_sku']) && $mgkwooas_option['mgkwooas_product_sku'] == '1' ) {
                  $sku = $product->get_sku();
                }

                // Is on sale 
                if ( isset($mgkwooas_option['mgkwooas_sale_badge']) && $mgkwooas_option['mgkwooas_sale_badge'] == '1' ) {
                  $on_sale = $product->is_on_sale();
                }

                // Is featured

                if ( isset($mgkwooas_option['mgkwooas_featured_badge']) && $mgkwooas_option['mgkwooas_featured_badge'] == '1' ) {
                  $featured = $product->is_featured();
                  
                }

                $product_results[] = apply_filters( 'mgkwooas_suggestion', array(
                  'id'    => $product->get_id(),
                  'value' => strip_tags( $product->get_title() ),
                  'url'   => $product->get_permalink(),
                  'image' => $product_image,
                  'price' =>$price,
                  'desc' =>$desc,
                  'on_sale' =>$on_sale,
                  'featured' =>$featured,
                  'sku' =>  $sku

                ), $product );

                // Get thumb HTML

                if($search_limit==1)
                {
                  break;
                }

                $search_limit--;
                
              }

            } 
            wp_reset_postdata();

          }
          
          $results=array_unique(array_merge($product_results,$results), SORT_REGULAR);

          if ( empty( $results ) ) {
           $results[] = array(
            'id'    => - 1,
            'value' => esc_html__( 'No results', 'magik-wooajax-search' ),
            'url'   => '',
          );
         }


         $results = array(
          'suggestions' => $results
          
        );
         echo json_encode( $results );
         die();

       }



    /*
     * Get meta query
     * For WooCommerce < 3.0
     * 
     * return array
     */

    private function mgkwooas_get_meta_query() {

      $meta_query = array();

      $meta_query = array(
        'relation' => 'AND',
        1          => array(
          'key'     => '_visibility',
          'value'   => array( 'search', 'visible' ),
          'compare' => 'IN'
        ),
        2          => array(
          'relation' => 'OR',
          array(
            'key'     => '_visibility',
            'value'   => array( 'search', 'visible' ),
            'compare' => 'IN'
          )
        )
      );

      $mgkwooas_option=get_option( 'mgkwooas_option' );
    // Exclude out of stock products from suggestions
      if (isset($mgkwooas_option['mgkwooas_exclude_out_of_stock']) && $mgkwooas_option['mgkwooas_exclude_out_of_stock']== 1 ) {
        $meta_query[] = array(
          'key'     => '_stock_status',
          'value'   => 'outofstock',
          'compare' => 'NOT IN'
        );
      };

      return $meta_query;
    }

  /*
   * Get tax query
   * For WooCommerce >= 3.0
   *
   * return array
   */

  private function mgkwooas_get_tax_query() {

    $product_visibility_term_ids = wc_get_product_visibility_term_ids();

    $tax_query = array(
      'relation' => 'AND'
    );

    $tax_query[] = array(
      'taxonomy' => 'product_visibility',
      'field'    => 'term_taxonomy_id',
      'terms'    => $product_visibility_term_ids['exclude-from-search'],
      'operator' => 'NOT IN',
    );

    $mgkwooas_option=get_option( 'mgkwooas_option' );
     // Exclude out of stock products from suggestions
    if (isset($mgkwooas_option['mgkwooas_exclude_out_of_stock']) && $mgkwooas_option['mgkwooas_exclude_out_of_stock']== 1 ) {
      $tax_query[] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'term_taxonomy_id',
        'terms'    => $product_visibility_term_ids['outofstock'],
        'operator' => 'NOT IN',
      );
    };

    return $tax_query;
  }

  /*
   * Search for matching category
   * 
   * @param string $keyword
   * 
   * @return array
   */

  public function mgkwooas_get_categories( $keyword ) {

    $results = array();

    $args = array(
      'taxonomy' => 'product_cat'
    );

    $product_categories = get_terms( 'product_cat', $args );

   // Compare keyword and term name
    $i = 0;
    $mgkwooas_option=get_option( 'mgkwooas_option' );
    $charlimit=    $mgkwooas_option['mgkwooas_minimumchar'];
    
    foreach ( $product_categories as $cat ) {

      if ( $i < $charlimit ) {

        $pos = strpos( strtolower( $cat->name ), strtolower( $keyword ) );


        if ( $pos !== false ) {  
          $results[ $i ] = array(
            'term_id'  => $cat->term_id,
            'taxonomy'   => 'product_cat',
            'value'    => preg_replace( sprintf( "/(%s)/", $keyword ), "$1", $cat->name ),
            'url'    => get_term_link( $cat, 'product_cat' ),
            'parents'  => ''
          );


          // Add category parents info
          $parents = $this->mgkwooas_get_taxonomy_parent_string( $cat->term_id, 'product_cat', array(), array( $cat->term_id ) );

          if ( !empty( $parents ) ) {

            $results[ $i ][ 'parents' ] = sprintf( ' <em>%s <b>%s</b></em>', esc_html__( 'in', 'magik-wooajax-search' ), mb_substr( $parents, 0, -3 ) );
          }

          $i++;
        }
      }


    }

    return $results;
  }



  /*
   * Extend research in the Woo tags
   * 
   * @param strong $keyword
   * 
   * @return array
   */

  public function mgkwooas_get_tags( $keyword ) {

    $results = array();

    $args = array(
      'taxonomy' => 'product_tag'
    );

    $product_tags = get_terms( 'product_tag', $args );

    // Compare keyword and term name
    $i = 0;
    $mgkwooas_option=get_option( 'mgkwooas_option' );
    $charlimit=    $mgkwooas_option['mgkwooas_minimumchar'];
    foreach ( $product_tags as $tag ) {

     if ( $i < $charlimit ) {

      $pos = strpos( strtolower( $tag->name ), strtolower( $keyword ) );

      if ( $pos !== false ) {
        $results[ $i ] = array(
          'term_id'  => $tag->term_id,
          'taxonomy'   => 'product_tag',
          'value'    => preg_replace( sprintf( "/(%s)/", $keyword ), "$1", $tag->name ),
          'url'    => get_term_link( $tag, 'product_tag' ),
          'parents'  => ''
        );

          // Add taxonomy parents info
        $parents = $this->mgkwooas_get_taxonomy_parent_string( $tag->term_id, 'product_tag', array(), array( $tag->term_id ) );

        if ( !empty( $parents ) ) {

          $results[ $i ][ 'parents' ] = sprintf( ' <em>%s <b>%s</b></em>', esc_html__( 'in', 'magik-wooajax-search' ), mb_substr( $parents, 0, -3 ) );
        }

        $i++;
      }
    }
  }

  return $results;
}



  /*
   * Extend research in the Woo tags
   * 
   * @param strong $keyword
   * 
   * @return array
   */

  public function mgkwooas_get_product_tags( $keyword ) {

    $results = array();

    $product_results   = array();


// Compare keyword and term name
    $i = 0;
    $mgkwooas_option=get_option( 'mgkwooas_option' );
    

    $args = array(
      'product_tag'                   => $keyword,
      'post_type'           => 'product',
      'post_status'         => 'publish',
      'ignore_sticky_posts' => 1
      
    );
    if ( version_compare( WC()->version, '3.0', '<' ) ) {
      $args['meta_query'] = $this->mgkwooas_get_meta_query();
    }else{
      $args['tax_query'] = $this->mgkwooas_get_tax_query();
    }

    $args = apply_filters('mgkwooas_products_args', $args);



    $products = get_posts( $args );

    if ( ! empty( $products ) ) {

      foreach ( $products as $post ) {
        $product = wc_get_product( $post );

        $product_image="";
        $price="";
        $desc ="";
        $sku="";
        $on_sale="";
        $featured="";




        if ( isset($mgkwooas_option['mgkwooas_product_image']) && $mgkwooas_option['mgkwooas_product_image'] == '1' ) {

          $product_image=$product->get_image();
        }

          // Get price
        if ( isset($mgkwooas_option['mgkwooas_product_price']) &&  $mgkwooas_option['mgkwooas_product_price' ] == '1' ) {
          $price = $product->get_price_html();
        }

          // Get description
        if ( isset($mgkwooas_option['mgkwooas_product_desc']) && $mgkwooas_option['mgkwooas_product_desc'] == '1' ) {

          $desc = mgkwooas_get_product_desc( $product->get_id(),'60' );
        }

              // Get SKU
        if ( isset($mgkwooas_option['mgkwooas_product_sku']) && $mgkwooas_option['mgkwooas_product_sku'] == '1' ) {
          $sku = $product->get_sku();
        }


                 // Is on sale 
        if ( isset($mgkwooas_option['mgkwooas_sale_badge']) && $mgkwooas_option['mgkwooas_sale_badge'] == '1' ) {
          $on_sale = $product->is_on_sale();
        }

                // Is featured

        if ( isset($mgkwooas_option['mgkwooas_featured_badge']) && $mgkwooas_option['mgkwooas_featured_badge'] == '1' ) {
          $featured = $product->is_featured();
          
        }


        

        $product_results[] = apply_filters( 'mgkwooas_suggestion', array(
          'id'    => $product->get_id(),
          'value' => strip_tags( $product->get_title() ),
          'url'   => $product->get_permalink(),
          'image' => $product_image,
          'price' =>$price,
          'desc' =>$desc,
          'on_sale' =>$on_sale,
          'featured' =>$featured,
          'sku' =>  $sku

        ), $product );
                  // Get thumb HTML
      }
    }
    return $product_results ;
  }

  

   /*
   * Extend research in the Woo tags
   * 
   * @param strong $keyword
   * 
   * @return array
   */

   public function mgkwooas_get_product_category( $keyword ) {

    $results = array();
    $product_results = array();


// Compare keyword and term name
    $i = 0;
    $mgkwooas_option=get_option( 'mgkwooas_option' );
    
    
    $args = array(

      'post_type'      => 'product',
      'post_status'         => 'publish',
      'ignore_sticky_posts' => 1,
      'product_cat'    => $keyword,
      
    );
    if ( version_compare( WC()->version, '3.0', '<' ) ) {
      $args['meta_query'] = $this->mgkwooas_get_meta_query();
    }else{
      $args['tax_query'] = $this->mgkwooas_get_tax_query();
    }

    $args = apply_filters('mgkwooas_products_args', $args);


    

    $products = get_posts( $args );

    if ( ! empty( $products ) ) {

      foreach ( $products as $post ) {
        $product = wc_get_product( $post );

        $product_image="";
        $price="";
        $desc ="";
        $sku="";
        $on_sale="";
        $featured="";




        if ( isset($mgkwooas_option['mgkwooas_product_image']) && $mgkwooas_option['mgkwooas_product_image'] == '1' ) {

          $product_image=$product->get_image();
        }

          // Get price
        if ( isset($mgkwooas_option['mgkwooas_product_price']) &&  $mgkwooas_option['mgkwooas_product_price' ] == '1' ) {
          $price = $product->get_price_html();
        }

          // Get description
        if ( isset($mgkwooas_option['mgkwooas_product_desc']) && $mgkwooas_option['mgkwooas_product_desc'] == '1' ) {

          $desc = mgkwooas_get_product_desc( $product->get_id(),'60' );
        }

              // Get SKU
        if ( isset($mgkwooas_option['mgkwooas_product_sku']) && $mgkwooas_option['mgkwooas_product_sku'] == '1' ) {
          $sku = $product->get_sku();
        }

    // Is on sale 
        if ( isset($mgkwooas_option['mgkwooas_sale_badge']) && $mgkwooas_option['mgkwooas_sale_badge'] == '1' ) {
          $on_sale = $product->is_on_sale();
        }

                // Is featured

        if ( isset($mgkwooas_option['mgkwooas_featured_badge']) && $mgkwooas_option['mgkwooas_featured_badge'] == '1' ) {
          $featured = $product->is_featured();
          
        }

        $product_results[] = apply_filters( 'mgkwooas_suggestion', array(
          'id'    => $product->get_id(),
          'value' => strip_tags( $product->get_title() ),
          'url'   => $product->get_permalink(),
          'image' => $product_image,
          'price' =>$price,
          'desc' =>$desc,
          'on_sale' =>$on_sale,
          'featured' =>$featured,
          'sku' =>  $sku

        ), $product );
                  // Get thumb HTML
      }
    }
    return $product_results;
  }


  /*
   * Set search product limit
   */

  public function mgkwooas_change_wp_search_size( $query ) {

    if ( $this->mgkwooas_is_ajax_search() ) {
      if ( $query->is_search )
      {
        $query->query_vars[ 'posts_per_page' ] = 10;
      }
    }


    return $query;
  }




/*
   * Search only in products titles
   * 
   * @param string $search SQL
   * 
   * @return string prepared SQL
   */

public function mgkwooas_search_filters( $search, $wp_query ) {
  global $wpdb;

  $mgkwooas_option=get_option( 'mgkwooas_option' );

  if ( empty( $search ) ) {
      return $search; // skip processing - there is no keyword
    }


    if ( $this->mgkwooas_is_ajax_search() || $this->mgkwooas_is_search_page() ) {

      $q = $wp_query->query_vars;

      if ( $q[ 'post_type' ] != 'product' ) {
        return $search; // skip processing
      }

      $n = !empty( $q[ 'exact' ] ) ? '' : '%';

      $search    = $searchand  = '';

      if ( !empty( $q[ 'search_terms' ] ) ) {
        foreach ( (array) $q[ 'search_terms' ] as $term ) {
          $term = esc_sql( $wpdb->esc_like( $term ) );

          $search .= "{$searchand} (";

          // Search in title
          $search .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";

          // Search in content
          if (isset($mgkwooas_option['mgkwooas_search_product_content']) && $mgkwooas_option['mgkwooas_search_product_content'] == 1 ) {
            $search .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
          }

          // Search in excerpt
          if (isset($mgkwooas_option['mgkwooas_search_product_excerpt']) && $mgkwooas_option['mgkwooas_search_product_excerpt'] == 1 ) {
            $search .= " OR ($wpdb->posts.post_excerpt LIKE '{$n}{$term}{$n}')";

          }

          // Search in SKU
          if (isset($mgkwooas_option['mgkwooas_search_product_sku']) && $mgkwooas_option['mgkwooas_search_product_sku'] == 1 ) {
            $search .= " OR (mgkwooas_sku.meta_key='_sku' AND mgkwooas_sku.meta_value LIKE '{$n}{$term}{$n}')";

          }

          $search .= ")";

          $searchand = ' AND ';
        }
      }

      if ( !empty( $search ) ) {
        $search = " AND ({$search}) ";
        if ( !is_user_logged_in() )
          $search .= " AND ($wpdb->posts.post_password = '') ";
      }
    }

    return $search;
  }

    /**
   * @param $where
   *
   * @return string
       */
    public function mgkwooas_search_distinct($where) {
      if ( $this->mgkwooas_is_ajax_search() || $this->mgkwooas_is_search_page() ) {
        return 'DISTINCT';
      }

      return $where;
    }


  /*
   * Join the postmeta column in the search posts SQL
   */

  public function mgkwooas_search_filters_join( $join, $query ) {
    global $wpdb;

    if ( empty( $query->query_vars[ 'post_type' ] ) || $query->query_vars[ 'post_type' ] !== 'product' ) {
      return $join; // skip processing
    }

    if ( ($this->mgkwooas_is_ajax_search() || $this->mgkwooas_is_search_page()) ) {

      $mgkwooas_option=get_option( 'mgkwooas_option' );

      if (isset($mgkwooas_option['mgkwooas_search_product_sku']) && $mgkwooas_option['mgkwooas_search_product_sku'] == 1 ) {

        $join .= " INNER JOIN $wpdb->postmeta AS mgkwooas_sku ON ( $wpdb->posts.ID = mgkwooas_sku.post_id )";
      }
    }


    return $join;
  }

  /**
   * Corrects the search by excerpt if necessary.
   * WooCommerce adds search in excerpt by defaults and this should be corrected.
   *
   * @since 1.1.4
   *
   * @param string $where
   * @return string
   */
  public function mgkwooas_woo_excerpt_search($where){
    global $wp_the_query;
    $mgkwooas_option=get_option( 'mgkwooas_option' );
    // If this is not a WC Query, do not modify the query
    if ( empty( $wp_the_query->query_vars['wc_query'] ) || empty( $wp_the_query->query_vars['s'] ) ) {
      return $where;
    }

    if (isset($mgkwooas_option['mgkwooas_search_product_excerpt']) &&  $mgkwooas_option['mgkwooas_search_product_excerpt'] != 1 ) {

      $where = preg_replace(
        "/OR \(post_excerpt\s+LIKE\s*(\'\%[^\%]+\%\')\)/",
        "", $where );
    }


    return $where;
  }



  /**
   * Get taxonomy parent
   *
   * @param int $term_id
   * @param string $taxonomy
   *
   * @return string
   */

  private function mgkwooas_get_taxonomy_parent_string( $term_id, $taxonomy, $visited = array(), $exclude = array() ) {

    $chain     = '';
    $separator   = ' > ';

    $parent = get_term( $term_id, $taxonomy );

    if ( empty( $parent ) || !isset( $parent->name ) ) {
      return '';
    }

    $name = $parent->name;

    if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
      $visited[] = $parent->parent;
      $chain .= $this->mgkwooas_get_taxonomy_parent_string( $parent->parent, $taxonomy, $visited );
    }

    if ( !in_array( $parent->term_id, $exclude ) ) {
      $chain .= $name . $separator;
    }

    return $chain;
  }

  
  /*
   * Change default search query on the search results page
   * 
   * @since 1.0.3
   * 
   * @param object $query
   * 
   * @return object
   */

  public function mgkwooas_set_default_search_query( $query ) {
    global $woocommerce;


    if ( $this->mgkwooas_is_ajax_search() && $query->is_search ) {

      if ( $this->mgkwooas_is_search_page() ) {



        $query->query_vars[ 'post_type' ] = 'product';
        $query->query_vars[ 'ignore_sticky_posts' ] = 1;
        $query->query_vars[ 'suppress_filters' ] = true;

        // Backward compatibility WC < 3.0
        if ( function_exists( 'WC' ) ) {
        if ( version_compare( WC()->version, '3.0', '<' ) ) {
          $query->query_vars[ 'meta_query' ] = $this->mgkwooas_get_meta_query();
        }else{
          $query->query_vars[ 'tax_query' ] = $this->mgkwooas_get_tax_query();
        }
     


        $ordering_args = $woocommerce->query->get_catalog_ordering_args( 'title', 'asc' );


        $query->query_vars[ 'orderby' ] = $ordering_args[ 'orderby' ];
        $query->query_vars[ 'order' ] = $ordering_args[ 'order' ];
         }

        $query->query_vars = apply_filters('mgkwooas_products_search_page_args', $query->query_vars);

      }
    }

    return $query;
  }

  /**
   * Check if is WooCommerce search results page
   *
   * @since 1.1.3
   *
   * @return bool
   */
  public function mgkwooas_is_search_page() {
    if ( ! empty( $_GET['s'] ) ) {
      return true;
    }



    return false;
  }

  /**
   * Check if is ajax search processing
   *
   * @since 1.1.3
   *
   * @return bool
   */
  public function mgkwooas_is_ajax_search() {    
    if ( defined( 'MGKWOOAS_AJAX_SEARCH' ) && MGKWOOAS_AJAX_SEARCH ) {

      return true;
    }

    return false;
  }


}

}



$mgkwooas_front_WooAjaxSearch = new MGK_Front_WooAjaxSearch();

