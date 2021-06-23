<?php
/**
 * Plugin Name: Magik WooCategory Image
 * Plugin URI: https://www.magikcommerce.com/
 * Description: It provide option to add extra product category image.
 * Version: 1.0
 * Author: MagikCommerce
 * Requires at least: WP 5.0
 * Tested up to: WP 5.1
 * WC requires at least: 3.5.5
 * WC tested up to: 3.5.5
 * Author URI: https://www.magikcommerce.com/
 * Text Domain: magik-woocategory-image
 * Domain Path: /languages/
 */



if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly



! defined( 'MGKWCI_PLUGIN' )                  && define( 'MGKWCI_PLUGIN', true);
! defined( 'MGKWCI_PLUGIN_VERSION' )          && define( 'MGKWCI_PLUGIN_VERSION', '1.0.0');
! defined( 'MGKWCI_PLUGIN_PATH' )             && define( 'MGKWCI_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'MGKWCI_PLUGIN_URL' )              && define( 'MGKWCI_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );


include("includes/magik-menu-panel.php");


register_activation_hook( __FILE__, array( 'MGK_WooCategory', 'mgkwci_install' ) );

if ( ! class_exists( 'MGK_WooCategory' ) ) {
class MGK_WooCategory
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

      add_action( 'admin_menu', array( $this, 'mgkwci_add_plugin_page' ) );
      add_action( 'admin_init', array( $this, 'mgkwci_page_init' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'mgkwci_admin_enqueue_scripts' ) );

   if ( $this->mgkwci_is_enable())
      {
      
     add_action( 'product_cat_add_form_fields',array( $this,'mgkwci_add_category_fields'), 11, 1);
     add_action( 'product_cat_edit_form_fields', array( $this,'mgkwci_edit_category_fields'), 11 );
     add_action( 'created_term', array( $this,'mgkwci_save_category_fields' ), 11, 3 );
     add_action( 'edit_term', array( $this,'mgkwci_save_category_fields') , 11, 3 );
   }


   }


    static function mgkwci_install() {


      add_option( 'mgkwci_version', MGKWCI_PLUGIN_VERSION );

      register_uninstall_hook( __FILE__, array( 'MGK_WooCategory','mgkwci_uninstall' ));
    }



    static function mgkwci_uninstall() {


     delete_option( 'mgkwci_version');


   }



     
       public function mgkwci_is_enable() {

        $enabled = false;
        $mgkwci_options=get_option( 'mgkwci_option' );


        if ( isset($mgkwci_options['mgkwci_enable'] ) && $mgkwci_options['mgkwci_enable'] == '1' ) {

      
          $enabled=true;

        }

        return $enabled;

      }



     public function mgkwci_admin_enqueue_scripts() {
      global $pagenow;


      wp_enqueue_media();
      wp_enqueue_style('mgkwci_admin_setting', MGKWCI_PLUGIN_URL . '/assets/css/mgkwci_admin_setting.css', array(), '');
      wp_enqueue_script('mgkwci-admin-category', MGKWCI_PLUGIN_URL . '/assets/js/mgkwci-admin-category.js',array('jquery'), '', true);
       if(class_exists( 'WooCommerce' ))
       {
       $placeholder_img= wc_placeholder_img_src();
       }
       else
       {
       $placeholder_img=MGKWCI_PLUGIN_URL . '/assets/images/placeholder.png';
       }
       wp_localize_script( 'mgkwci-admin-category', 'mgkwci_params', array(
        
        
          'MGKWCI_CHOOSE_IMAGE_TEXT'=> esc_html__('Choose an image','magik-woocategory-image'),
          'MGKWCI_USE_IMAGE_TEXT'=> esc_html__('Use Image','magik-woocategory-image'),
          'MGKWCI_PLACE_HOLDER_IMAGE'=> esc_url($placeholder_img)
        

        ));


    }


    /**
     * Add options page
     */
    public function mgkwci_add_plugin_page()
    {
      if ( ! empty( $this->_panel ) ) {
        return;
      }

      $mgk_woocategory_menu_panel= new MGK_WooCategory_Menu_Panel();
      $parent_slug= $mgk_woocategory_menu_panel->add_menu_page();

      add_submenu_page($parent_slug,
        esc_attr__('Magik WooCategory',"magik-woocategory-image"),
        esc_attr__('Magik WooCategory',"magik-woocategory-image"),
        'manage_options',
        'mgkwci_admin_settings',
        array( $this, 'mgkwci_create_admin_page' )
      );


    }

    /**
     * Options page callback
     */
    public function mgkwci_create_admin_page()
    {

       if ( ! current_user_can( 'manage_options' ) ) {
          wp_die( esc_html__( 'You do not have sufficient permissions to access this page.','magik-woocategory-image' ) );
         }
        // Set class property
      $this->options = get_option( 'mgkwci_option' );
      ?>
      <div class="mgkwci_wrap">

   
        <form id="mgkwci_scolling" method="post" action="options.php" enctype=”multipart/form-data”>
          <?php
                // This prints out all hidden setting fields
          settings_fields( 'mgkwci_option_group' );
          do_settings_sections( 'mgkwci_admin_settings' );
          submit_button();
          ?>
        </form>
      </div>
      <?php
    }

    /**
     * Register and add settings
     */
    public function mgkwci_page_init()
    {        
      register_setting(
            'mgkwci_option_group', // Option group
            'mgkwci_option', // Option name
            array( $this, 'mgkwci_sanitize' ) // Sanitize
          );

      add_settings_section(
            'mgkwci_setting_section', // ID
              esc_attr__('Magik Product Category Image','magik-woocategory-image'),  // Title
            array( $this, 'mgkwci_print_section_info' ), // Callback
            'mgkwci_admin_settings' // Page
          ); 

      add_settings_field(
            'mgkwci_enable', // ID
            esc_attr__('Enable Woo Category Image','magik-woocategory-image'), // Title 
            array( $this, 'mgkwci_enable_callback' ), // Callback
            'mgkwci_admin_settings', // Page
            'mgkwci_setting_section' // Section           
          ); 



    }


    /** 
     * Print the Section text
     */
    public function mgkwci_print_section_info()
    {
      
    }

     /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function mgkwci_sanitize( $input )
    {
      $new_input = array();
      
      if( isset( $input['mgkwci_enable'] ) )
      {
        $new_input['mgkwci_enable'] = sanitize_text_field( $input['mgkwci_enable'] );
      }

   


      return $new_input;
    }
   
    /** 
     * Get the settings option array and print one of its values
     */
    public function mgkwci_enable_callback()
    {
      $checked ="";
      if(isset($this->options['mgkwci_enable'])) { $checked = ' checked="checked" '; }
      echo'
      <input type="checkbox" id="mgkwci_enable" name="mgkwci_option[mgkwci_enable]" value="1" '.$checked .'/>' ;

    }


   public function mgkwci_add_category_fields()
   { 
    ?>

    <div class="form-field term-thumbnail-wrap">
      <label><?php esc_html_e( 'Magik Category Banner', 'magik-woocategory-image' ); ?></label>
      <div id="product_cat_thumbnail_magik" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
      <div style="line-height: 60px;">
        <input type="hidden" id="product_cat_magik_thumbnail_id" name="product_cat_magik_thumbnail_id" />
        <button type="button" class="button magik_img_add_button"><?php esc_html_e( 'Upload/Add image', 'magik-woocategory-image' ); ?></button>
        <button type="button" class="button magik_img_remove_button"><?php esc_html_e( 'Remove image', 'magik-woocategory-image' ); ?></button>
      </div>
     
      <div class="clear"></div>
    </div>

    <?php
  }

  public function mgkwci_edit_category_fields( $term ) {


    $thumbnail_id = absint( get_term_meta( $term->term_id, 'magik_thumbnail_id', true ) );

    if ( $thumbnail_id ) {
      $image = wp_get_attachment_thumb_url( $thumbnail_id );
    } else {
      $image = wc_placeholder_img_src();
    }
    ?>

    <tr class="form-field">
      <th scope="row" valign="top"><label><?php esc_html_e( 'Magik Category Banner', 'magik-woocategory-image' ); ?></label></th>
      <td>
        <div id="product_cat_thumbnail_magik" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
        <div style="line-height: 60px;">
          <input type="hidden" id="product_cat_magik_thumbnail_id" name="product_cat_magik_thumbnail_id" value="<?php echo esc_html($thumbnail_id); ?>" />
          <button type="button" class=" button magik_img_add_button"><?php esc_html_e( 'Upload/Add image', 'magik-woocategory-image' ); ?></button>
          <button type="button" class="button magik_img_remove_button"><?php esc_html_e( 'Remove image', 'magik-woocategory-image' ); ?></button>
        </div>
    
        <div class="clear"></div>
      </td>
    </tr>
    <?php
  }


  public function mgkwci_save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

    if ( isset( $_POST['product_cat_magik_thumbnail_id'] ) && 'product_cat' === $taxonomy ) {
      update_woocommerce_term_meta( $term_id, 'magik_thumbnail_id', absint( $_POST['product_cat_magik_thumbnail_id'] ) );
    }
  }



}
}

if( is_admin() )
 $mgkwci_WooCategory = new MGK_WooCategory();










   // catalog mode front end class
if ( ! class_exists( 'MGK_Front_WooCategory' ) ) {
class MGK_Front_WooCategory
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
      $mgkwci_options=get_option( 'mgkwci_option' );

    if ( isset($mgkwci_options['mgkwci_enable'] ) && $mgkwci_options['mgkwci_enable'] == '1' ) {
     remove_action('woocommerce_archive_description', 'woocommerce_category_image');

     add_action('woocommerce_archive_description', array( $this, 'mgkwci_woocommerce_category_image'), 20);

   }


     
   }

   public function mgkwci_woocommerce_category_image()
   {
    global $product;
    if (is_product_category()) {
      global $wp_query;
      $cat = $wp_query->get_queried_object();
      $thumbnail_id = get_term_meta($cat->term_id, 'magik_thumbnail_id', true);
      $image = wp_get_attachment_url($thumbnail_id);
      if ($image) {
        echo '<div class="category-description std"><img src="' . esc_url($image) . '" alt="'.esc_html__( 'Remove image', 'magik-woocategory-image' ).'" /></div>';
      }
    }
  }
}

}



$mgkwci_front_WooCategory = new MGK_Front_WooCategory();

?>