<?php
/**
 * Plugin Name: Magik Catalog Mode
 * Plugin URI: https://www.magikcommerce.com/
 * Description: Magik Catalog Mode provides catalog mode and quotation form option for WooCommerce Store.
 * Version: 1.0
 * Author: MagikCommerce
 * Requires at least: WP 5.0
 * Tested up to: WP 5.1
 * WC requires at least: 3.5.5
 * WC tested up to: 3.5.5
 * Author URI: https://www.magikcommerce.com/
 * Text Domain: magik-catalog-mode
 * Domain Path: /languages/
 */



if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly


! defined( 'MGKCMO_PLUGIN' )                  && define( 'MGKCMO_PLUGIN', true);
! defined( 'MGKCMO_PLUGIN_VERSION' )          && define( 'MGKCMO_PLUGIN_VERSION', '1.0.0');
! defined( 'MGKCMO_PLUGIN_PATH' )             && define( 'MGKCMO_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'MGKCMO_PLUGIN_URL' )              && define( 'MGKCMO_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );



include("includes/magik-menu-panel.php");


register_activation_hook( __FILE__, array( 'MGK_CatalogMode', 'mgkcmo_install' ) );

if ( ! class_exists( 'MGK_CatalogMode' ) ) {
class MGK_CatalogMode
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

      add_action( 'admin_menu', array( $this, 'mgkcmo_add_plugin_page' ) );
      add_action( 'admin_init', array( $this, 'mgkcmo_page_init' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'mgkcmo_admin_enqueue_scripts' ) );

     
      
      add_action( 'woocommerce_product_options_advanced', array( $this,'mgkcmo_woocommerce_product_catalog_mode' ));

      add_action( 'woocommerce_process_product_meta', array( $this,'mgkcmo_woocommerce_product_catalog_mode_save' ));
     



    }


    static function mgkcmo_install() {


      add_option( 'mgkcmo_version', MGKCMO_PLUGIN_VERSION );

      register_uninstall_hook( __FILE__, array( 'MGK_CatalogMode','mgkcmo_uninstall' ));
    }



    static function mgkcmo_uninstall() {


     delete_option( 'mgkcmo_version');


   }


     /**
     * Enqueue script and styles in admin side
     *
     * Add style and scripts to administrator
     *
     * @return void
     * @since    1.0

     */


     public function mgkcmo_admin_enqueue_scripts() {
      global $pagenow;


      wp_enqueue_media();
      wp_enqueue_style('mgkcmo_admin_setting', MGKCMO_PLUGIN_URL . '/assets/css/mgkcmo_admin_setting.css', array(), '');


    }




    /**
     * Add options page
     */
    public function mgkcmo_add_plugin_page()
    {
      if ( ! empty( $this->_panel ) ) {
        return;
      }

      $mgk_catalogmode_menu_panel= new MGK_CatalogMode_Menu_Panel();
      $parent_slug= $mgk_catalogmode_menu_panel->add_menu_page();

      add_submenu_page($parent_slug,
        esc_attr__('Magik Catalog Mode',"magik-catalog-mode"),
        esc_attr__('Magik Catalog Mode',"magik-catalog-mode"),
        'manage_options',
        'mgkcmo_admin_settings',
        array( $this, 'mgkcmo_create_admin_page' )
      );


    }

    /**
     * Options page callback
     */
    public function mgkcmo_create_admin_page()
    {
      if ( ! current_user_can( 'manage_options' ) ) {
          wp_die( esc_html__( 'You do not have sufficient permissions to access this page.','magik-catalog-mode' ) );
        }
        // Set class property
      $this->options = get_option( 'mgkcmo_option' );
      ?>
      <div class="mgkcmo_wrap">


        <form id="mgkcmo_scolling" method="post" action="options.php" enctype="multipart/form-data">
          <?php
                // This prints out all hidden setting fields
          settings_fields( 'mgkcmo_option_group' );
          do_settings_sections( 'mgkcmo_admin_settings' );
          submit_button();
          ?>
        </form>
      </div>
      <?php
    }

    /**
     * Register and add settings
     */
    public function mgkcmo_page_init()
    {        
      register_setting(
            'mgkcmo_option_group', // Option group
            'mgkcmo_option', // Option name
            array( $this, 'mgkcmo_sanitize' ) // Sanitize
          );

      add_settings_section(
            'mgkcmo_setting_section', // ID
              esc_attr__('General Setting','magik-catalog-mode'),  // Title
            array( $this, 'mgkcmo_print_section_info' ), // Callback
            'mgkcmo_admin_settings' // Page
          ); 

      add_settings_field(
            'mgkcmo_enable', // ID
            esc_attr__('Enable Magik WooCommerce Catalog Mode','magik-catalog-mode'), // Title 
            array( $this, 'mgkcmo_enable_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section' // Section           
          ); 


      add_settings_section(
            'mgkcmo_setting_section2', // ID
              esc_attr__('Full Catalog Mode Setting','magik-catalog-mode'),  // Title
            array( $this, 'mgkcmo_print_section_info2' ), // Callback
            'mgkcmo_admin_settings' // Page
          ); 


      add_settings_field(
            'mgkcmo_shop_option', // ID
            esc_attr__('Disable Shopping Option','magik-catalog-mode'), // Title 
            array( $this, 'mgkcmo_shop_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section2' // Section           
          ); 

      add_settings_section(
            'mgkcmo_setting_section3', // ID
              esc_attr__('Catalog Mode settings','magik-catalog-mode'),  // Title
            array( $this, 'mgkcmo_print_section_info3' ), // Callback
            'mgkcmo_admin_settings' // Page
          ); 

      add_settings_field(
            'mgkcmo_category_page', // ID
             esc_attr__('Hide "Add to Cart" in shop/category page','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_category_page_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section3' // Section           
          );  
      add_settings_field(
            'mgkcmo_product_detail', // ID
             esc_attr__('Hide "Add to Cart" in Product Detail page','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_product_detail_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section3' // Section           
          );  

      add_settings_section(
            'mgkcmo_setting_section4', // ID
              esc_attr__('Enquiry Form Settings','magik-catalog-mode'),  // Title
            array( $this, 'mgkcmo_print_section_info4' ), // Callback
            'mgkcmo_admin_settings' // Page
          ); 

      add_settings_field(
            'mgkcmo_enable_enquiryform', // ID
             esc_attr__('Enable Enquiry Form','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_enable_enquiryform_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section4' // Section           
          ); 

      add_settings_field(
            'mgkcmo_enable_shortcode_enquiryform', // ID
             esc_attr__('Enable Shortcode Enquiry Form','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_enable_shortcode_enquiryform_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section4' // Section           
          ); 


      add_settings_field(
            'mgkcmo_enable_allproduct_enquiryform', // ID
             esc_attr__('Show Enquiry Form on All Product Page','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_enable_allproduct_enquiryform_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section4' // Section           
          ); 

      add_settings_field(
            'mgkcmo_enquiryform_shortcode', // ID
             esc_attr__('Add Contact Form 7 Shortcode Here','magik-catalog-mode'),  // Title 
            array( $this, 'mgkcmo_enquiryform_shortcode_callback' ), // Callback
            'mgkcmo_admin_settings', // Page
            'mgkcmo_setting_section4' // Section           
          );  



    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function mgkcmo_sanitize( $input )
    {
      $new_input = array();
      
      if( isset( $input['mgkcmo_enable'] ) ) {
        $new_input['mgkcmo_enable'] = sanitize_text_field( $input['mgkcmo_enable'] );
      }

      if( isset( $input['mgkcmo_shop_option'] ) ) {
        $new_input['mgkcmo_shop_option'] = sanitize_text_field( $input['mgkcmo_shop_option'] );
      }

      if( isset( $input['mgkcmo_category_page'] ) ) {
        $new_input['mgkcmo_category_page'] = sanitize_text_field( $input['mgkcmo_category_page'] );
      }

      if( isset( $input['mgkcmo_product_detail'] ) )  {
        $new_input['mgkcmo_product_detail'] = sanitize_text_field( $input['mgkcmo_product_detail'] );
      }

      if( isset( $input['mgkcmo_enable_enquiryform'] ) ) {
        $new_input['mgkcmo_enable_enquiryform'] = sanitize_text_field( $input['mgkcmo_enable_enquiryform'] );
      }

      if( isset( $input['mgkcmo_enable_shortcode_enquiryform'] ) ) {
        $new_input['mgkcmo_enable_shortcode_enquiryform'] = sanitize_text_field( $input['mgkcmo_enable_shortcode_enquiryform'] );
      }

      
      if( isset( $input['mgkcmo_enable_allproduct_enquiryform'] ) ) {
        $new_input['mgkcmo_enable_allproduct_enquiryform'] = sanitize_text_field( $input['mgkcmo_enable_allproduct_enquiryform'] );
      }


      if( isset( $input['mgkcmo_enquiryform_shortcode'] ) ) {
        $new_input['mgkcmo_enquiryform_shortcode'] =  sanitize_text_field($input['mgkcmo_enquiryform_shortcode']) ;
      }




      return $new_input;
    }


    public function mgkcmo_print_section_info()
    {

    }

    public function mgkcmo_print_section_info2()
    {

    }

    public function mgkcmo_print_section_info3()
    {

    }


    public function mgkcmo_print_section_info4()
    {

    }








    public function mgkcmo_enable_callback()
    {
      $checked ="";
      if(isset($this->options['mgkcmo_enable'])) { $checked = ' checked="checked" '; }
      echo'
      <input type="checkbox" id="mgkcmo_enable" name="mgkcmo_option[mgkcmo_enable]" value="1" '.$checked .'/>' ;

    }


    public function mgkcmo_shop_callback()
    {
      $checked ="";
      if(isset($this->options['mgkcmo_shop_option'])) { $checked = ' checked="checked" '; }
      echo'
      <input type="checkbox" id="mgkcmo_shop_option" name="mgkcmo_option[mgkcmo_shop_option]" value="1" '.$checked .'/>  ';
      esc_html_e('Hide and disable "Cart" page, "Checkout" page and all "Add to Cart" buttons','magik-catalog-mode') ;

    }


    public function mgkcmo_category_page_callback()
    {

     $checked ="";
     if(isset($this->options['mgkcmo_category_page'])) { $checked = ' checked="checked" '; }
     echo'
     <input type="checkbox" id="mgkcmo_category_page" name="mgkcmo_option[mgkcmo_category_page]" value="1" '.$checked .'/> ' ;
   }


   public function mgkcmo_product_detail_callback()
   {

     $checked ="";
     if(isset($this->options['mgkcmo_product_detail'])) { $checked = ' checked="checked" '; }
     echo'
     <input type="checkbox" id="mgkcmo_product_detail" name="mgkcmo_option[mgkcmo_product_detail]" value="1" '.$checked .'/> ' ;
   }


   public function mgkcmo_enable_enquiryform_callback()
   {

     $checked ="";
     if(isset($this->options['mgkcmo_enable_enquiryform'])) { $checked = ' checked="checked" '; }
     echo'
     <input type="checkbox" id="mgkcmo_enable_enquiryform" name="mgkcmo_option[mgkcmo_enable_enquiryform]" value="1" '.$checked .'/> ' ;
   }

   public function mgkcmo_enable_shortcode_enquiryform_callback()
   {

     $checked ="";
     if(isset($this->options['mgkcmo_enable_shortcode_enquiryform'])) { $checked = ' checked="checked" '; }
     echo'
     <input type="checkbox" id="mgkcmo_enable_shortcode_enquiryform" name="mgkcmo_option[mgkcmo_enable_shortcode_enquiryform]" value="1" '.$checked .'/> ' ;
   }

   public function mgkcmo_enable_allproduct_enquiryform_callback()
   {

     $checked ="";
     if(isset($this->options['mgkcmo_enable_allproduct_enquiryform'])) { $checked = ' checked="checked" '; }
     echo'
     <input type="checkbox" id="mgkcmo_enable_allproduct_enquiryform" name="mgkcmo_option[mgkcmo_enable_allproduct_enquiryform]" value="1" '.$checked .'/> ' ;
   }





   public function mgkcmo_enquiryform_shortcode_callback()
   {

     $shcodeval ="";
     if(isset($this->options['mgkcmo_enquiryform_shortcode'])) {  $shcodeval = $this->options['mgkcmo_enquiryform_shortcode']; }
     ?>
     <input type="text" id="mgkcmo_enquiryform_shortcode" name="mgkcmo_option[mgkcmo_enquiryform_shortcode]" value='<?php echo esc_html($shcodeval);?>' class="regular-text"/> 
   <?php }




  // aAdd catalog mode in product page in admin panel

   public function mgkcmo_woocommerce_product_catalog_mode() {
     global $woocommerce, $post;

     woocommerce_wp_checkbox(
      array(
        'id' => 'mgkcmo_product_catalog_mode_enable',
        'wrapper_class' => 'checkbox_class',
        'label' => esc_html__('Enable Catalog Mode', 'magik-catalog-mode' ),
        'description' => esc_html__( 'Tick checkbox to Enable Catalog Mode for this product', 'magik-catalog-mode' )
      )
    );
     
     woocommerce_wp_checkbox(
      array(
        'id' => 'mgkcmo_product_catalog_enquiry_form',
        'wrapper_class' => 'checkbox_class',
        'label' => esc_html__('Enable Enquiry Form', 'magik-catalog-mode' ),
        'description' => esc_html__( 'Tick checkbox to show enquiry form on product detail page', 'magik-catalog-mode' )
      )
    );
     

     


   }



// Save Fields using WooCommerce Action Hook
   function mgkcmo_woocommerce_product_catalog_mode_save( $post_id ){
    global  $woo_checkbox;
    $woo_checkbox = isset( $_POST['mgkcmo_product_catalog_mode_enable'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'mgkcmo_product_catalog_mode_enable', $woo_checkbox );

    $woo_checkbox = isset( $_POST['mgkcmo_product_catalog_enquiry_form'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'mgkcmo_product_catalog_enquiry_form', $woo_checkbox );

  }



}
}

if( is_admin() )
{
 $mgkcmo_CatalogMode = new MGK_CatalogMode();
}










 // catalog mode front end class

if ( ! class_exists( 'MGK_Front_CatalogMode' ) ) {
class MGK_Front_CatalogMode
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
      add_action('wp_enqueue_scripts', array($this,'mgkcmo_scripts_styles')); 

     if ($this->mgkcmo_is_enable())
      {
       add_action( 'woocommerce_after_shop_loop_item', array( $this, 'mgkcmo_hide_add_to_cart_loop' ), 5 );  
       add_action( 'woocommerce_single_product_summary', array( $this, 'mgkcmo_hide_add_to_cart_loop' ), 5 );  

       if ( $this->mgkcmo_disable_shop_option() ) {

         remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
         add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

         remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
         add_filter( 'woocommerce_is_purchasable', '__return_false', 30 );  
         add_action( 'wp', array( $this, 'mgkcmo_check_pages_redirect' ) );   

       }

       else if($this->mgkcmo_disable_product_detail())
       {  
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        add_filter( 'woocommerce_is_purchasable', '__return_false', 30 );  

      }

      else if($this->mgkcmo_disable_category_option())
      { 

        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

      }
      else{

      }

      add_action( 'woocommerce_single_product_summary', array( $this,'mgkcmo_woocommerce_template_single_add_to_cart'), 30,4 );
      add_action( 'send_raq_mail',  array($this, 'mgkcmo_send_transactional_email' ), 10, 1);



    }
  }


  public function mgkcmo_scripts_styles()
  {

    
    wp_enqueue_style('mgkssh_style', MGKCMO_PLUGIN_URL . '/assets/css/mgkcmo_style.css', array(), ''); 
     wp_enqueue_script( 'mgkcmo_common', MGKCMO_PLUGIN_URL . '/assets/js/mgkcmo_common.js', array( 'jquery' ), '1.0.0', true );

  }




  public function mgkcmo_send_message() {

    $errors = "";

    if ( ! isset( $_POST['mgkcmo_name'] ) ) {
     return;
   }else {



    if ( empty( $_POST['mgkcmo_name'] ) ) {
      $errors .= '<p class="error">' . esc_html__( 'Please enter a name', 'magik-catalog-mode' ) . '</p>';
    }

    if ( ! isset( $_POST['mgkcmo_email'] ) || empty( $_POST['mgkcmo_email'] ) || ! is_email( $_POST['mgkcmo_email'] ) ) {
      $errors.= '<p class="error">' . esc_html__( 'Please enter a valid email', 'magik-catalog-mode' ) . '</p>';
    }


    if ( empty( $errors ) ) {

      $args = array(
        'user_name'    => $_POST['mgkcmo_name'],
        'user_email'   => $_POST['mgkcmo_email'],
        'user_message' => nl2br( $_POST['mgkcmo_message'] ),
        'mgkcmo_product_url' =>  $_POST['mgkcmo_product_url'] ,
        'mgkcmo_product_id' =>  $_POST['mgkcmo_product_id']

      );
      
      do_action( 'send_raq_mail', $args );
      
    }
    else{
      echo wp_specialchars_decode($errors);
    }
  }



}



// catalog mode functions
public function mgkcmo_send_transactional_email( $args )
{
 $admin_email = get_option('admin_email');
 $headers = array("From: $admin_email");


 $to = $admin_email;
 $subject = 'Product Enquiry Request';
 $message = "Product Enquiry Form submition detail.";
 $message .= "<br>User Email :".$args ['user_email'];
 $message .= "<br>User Name :".$args ['user_name'];
 $message .= "<br>Product Url :".$args ['mgkcmo_product_url'];
 $message .= "<br>Message :".$args ['user_message'];

 wp_mail( $to, $subject, $message,$headers  );
 ?>
 <p class="quote-success"><?php echo esc_html_e('Request Sent Successfully.', 'magik-catalog-mode');?></p>
 <?php
}



public function mgkcmo_woocommerce_template_single_add_to_cart() {

 global $product;  
 $mgkcmo_options=get_option( 'mgkcmo_option' );
 
 if(isset($mgkcmo_options['mgkcmo_enable_enquiryform']) && $mgkcmo_options['mgkcmo_enable_enquiryform']==1)
 {

  $this->mgkcmo_send_message();
  $current_user = array();
  if ( is_user_logged_in() ) {
    $current_user = get_user_by( 'id', get_current_user_id() );
  }





  if(isset($mgkcmo_options['mgkcmo_enable_allproduct_enquiryform']) && $mgkcmo_options['mgkcmo_enable_allproduct_enquiryform']==1)
  {
    echo wp_specialchars_decode($this->mgkcmo_enquiryform_code());
  }
  else{

   $product_catalog_enable=get_post_meta($product->get_id(), 'mgkcmo_product_catalog_enquiry_form', true );
   if(isset($product_catalog_enable) &&   $product_catalog_enable=="yes")
   {
     echo wp_specialchars_decode($this->mgkcmo_enquiryform_code());
   }
   
 }
}
}

public function  mgkcmo_enquiryform_code()
{
 
  global $product;  
  $mgkcmo_options=get_option( 'mgkcmo_option' );
  $user_name = ( ! empty( $current_user ) ) ?  $current_user->display_name : '';
  $user_email = ( ! empty( $current_user ) ) ?  $current_user->user_email : '';
  $product_url=get_permalink();
  ?>
  <button type="button" class="btn btn-enquiry" id="mgkcmo_modalbtn" data-toggle="modal" data-target="#mgkcmo_modal"><?php  esc_html_e( 'Send Quote', 'magik-catalog-mode' ) ;?></button>

  <?php
  if(isset($mgkcmo_options['mgkcmo_enable_shortcode_enquiryform']) && $mgkcmo_options['mgkcmo_enable_shortcode_enquiryform']==1)
  {
    ?>
    <div id="mgkcmo_modal" class="modal" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php  esc_html_e( 'Quotation Form', 'magik-catalog-mode' ) ;?></h4>
          </div>
          
          <div class="modal-body">
            

            <p class="form-row form-row-wide validate-required" id="mgkcmo_name_row">
             <?php echo do_shortcode($mgkcmo_options['mgkcmo_enquiryform_shortcode']);?>
           </p>

         </div>

       </div>

     </div>
   </div>
   <?php
 }
 else{
  ?>

  <!-- Modal -->
  <div id="mgkcmo_modal" class="modal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php  esc_html_e( 'Quotation Form', 'magik-catalog-mode' ) ;?></h4>
        </div>
        <div class="modal-body">
         <form id="mgkcmo-quote-form" name="mgkcmo-quote-form" action="<?php echo esc_html($product_url); ?>" method="post">
          <input type="hidden" name="mgkcmo_product_url" value="<?php echo esc_html($product_url); ?> ">
          <input type="hidden" name="mgkcmo_product_id" value="<?php echo esc_html($product->get_id()); ?>">

          <p class="form-row form-row-wide validate-required" id="mgkcmo_name_row">
            <label for="rqa-name" class=""><?php esc_html_e( 'Name', 'magik-catalog-mode' ) ?>
            <abbr class="required" title="<?php esc_html_e( 'required', 'magik-catalog-mode' ) ?>">*</abbr></label>
            <input type="text" class="input-text " name="mgkcmo_name" id="mgkcmo_name" placeholder="" value="<?php echo esc_html($user_name); ?>" required>
          </p>

          <p class="form-row form-row-wide validate-required" id="mgkcmo_email_row">
            <label for="rqa-email" class=""><?php esc_html_e( 'Email', 'magik-catalog-mode' ) ?>
            <abbr class="required" title="<?php esc_html_e( 'required', 'magik-catalog-mode' ) ?>">*</abbr></label>
            <input type="email" class="input-text" name="mgkcmo_email" id="mgkcmo_email" placeholder="" value="<?php echo esc_html($user_email); ?>" required>
          </p>

          <p class="form-row" id="mgkcmo_message_row">
            <label for="rqa-message" class=""><?php esc_html_e( 'Message', 'magik-catalog-mode' ) ?></label>
            <textarea name="mgkcmo_message" class="input-text " id="mgkcmo_message" rows="5" cols="5"></textarea>
          </p>


          <p class="form-row">
            <input type="hidden" id="mgkcmo-mail-wpnonce" name="raq_mail_wpnonce" value="<?php echo wp_create_nonce( 'send-request-quote' ) ?>">
            <input class="button mgkcmo-send-request" type="submit" value="<?php esc_html_e( 'Send Request', 'magik-catalog-mode' ) ?>">
          </p>

        </form>
      </div>

    </div>

  </div>
</div>


<?php
}

}


    /**
     * Hides "Add to cart" button, if not excluded, from loop page
     *
     * @since   1.0.0
     * @return  void

     */
    public function mgkcmo_hide_add_to_cart_loop() {


      add_filter('woocommerce_is_purchasable',  array( $this,'mgkcmo_hide_add_to_cart_loop_purchasable'));



    }

    public function mgkcmo_hide_add_to_cart_loop_purchasable() {
      global $product;

      $product_catalog_enable=get_post_meta($product->get_id(), 'mgkcmo_product_catalog_mode_enable', true );
      $product_title=$product->get_title();
     
      if($product_catalog_enable=="yes")
      {
       if(is_product())
       {
         
        
         if( $product->is_type( 'simple' ) ) {
          remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
          
        }
          // For variable product types (keeping attribute select fields)
        elseif( $product->is_type( 'variable' ) ) {
          remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
        }

      }
     

      return false;
    }
    return true;

  }




       /**
     * Disable Shop
     *
     * @since   1.0.0
     * @return  boolean
     */
       public function mgkcmo_is_enable() {

        $enabled = false;
        $mgkcmo_options=get_option( 'mgkcmo_option' );


        if ( isset($mgkcmo_options['mgkcmo_enable'] ) && $mgkcmo_options['mgkcmo_enable'] == '1' ) {

          global $post;
          $enabled=true;

        }

        return $enabled;

      }

        /**
     * Disable Shop
     *
     * @since   1.0.0
     * @return  boolean
     */
        public function mgkcmo_disable_shop_option() {

          $enabled = false;
          $mgkcmo_options=get_option( 'mgkcmo_option' );


          if ( isset($mgkcmo_options['mgkcmo_shop_option'] ) && $mgkcmo_options['mgkcmo_shop_option'] == '1' ) {

            global $post;

            $enabled=true;

          }

          return $enabled;

        }

     /**
     * Disable Category / Shop
     *
     * @since   1.0.0
     * @return  boolean

     */
     public function mgkcmo_disable_category_option() {

      $enabled = false;
      $mgkcmo_options=get_option( 'mgkcmo_option' );


      if ( isset($mgkcmo_options['mgkcmo_category_page'] ) && $mgkcmo_options['mgkcmo_category_page'] == '1' ) {

        global $post;

        $enabled=true;

      }

      return $enabled;


    }


     /**
     * Disable Product Detail
     *
     * @since   1.0.0
     * @return  boolean
     */
     public function mgkcmo_disable_product_detail() {

      $enabled = false;
      $mgkcmo_options=get_option( 'mgkcmo_option' );




      if ( isset($mgkcmo_options['mgkcmo_product_detail'] ) && $mgkcmo_options['mgkcmo_product_detail'] == '1' ) {


        $enabled=true;

      }

      return $enabled;

    }


   /**
     * Avoid Cart and Checkout Pages to be visited
     *
     * @since   1.0.4
     * @return  void
     */
   public function mgkcmo_check_pages_redirect() {

    if (function_exists( 'WC' ) &&  ($this->mgkcmo_disable_shop_option() )) {

      $cart     = is_page( wc_get_page_id( 'cart' ) );
      $checkout = is_page( wc_get_page_id( 'checkout' ) );

      wp_reset_query();

      if ( $cart || $checkout ) {

        wp_redirect( home_url() );
        exit;

      }

    }

  }

}
}



$mgkcmo_front_CatalogMode = new MGK_Front_CatalogMode();

?>