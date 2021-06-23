<?php
/**
 * Plugin Name: Magik Social Share
 * Plugin URI: https://www.magikcommerce.com/
 * Description: It prodvide social sharing option for WooCommerce products.
 * Version: 1.0
 * Author: MagikCommerce
 * Requires at least: WP 5.0
 * Tested up to: WP 5.1
 * WC requires at least: 3.5.5
 * WC tested up to: 3.5.5
 * Author URI: https://www.magikcommerce.com/
 * Text Domain: magik-social-share
 * Domain Path: /languages/
 */



if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly



! defined( 'MGKSSH_PLUGIN' )                  && define( 'MGKSSH_PLUGIN', true);
! defined( 'MGKSSH_PLUGIN_VERSION' )          && define( 'MGKSSH_PLUGIN_VERSION', '1.0.0');
! defined( 'MGKSSH_PLUGIN_PATH' )             && define( 'MGKSSH_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'MGKSSH_PLUGIN_URL' )              && define( 'MGKSSH_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );



include("includes/magik-menu-panel.php");



register_activation_hook( __FILE__, array( 'MGK_SocialShare', 'mgkssh_install' ) );

if ( ! class_exists( 'MGK_SocialShare' ) ) {
class MGK_SocialShare
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

    	add_action( 'admin_menu', array( $this, 'mgkssh_add_plugin_page' ) );
    	add_action( 'admin_init', array( $this, 'mgkssh_page_init' ) );

    	add_action( 'admin_enqueue_scripts', array( $this, 'mgkssh_admin_enqueue_scripts' ) );





    }

     static function mgkssh_install() {


      add_option( 'mgkssh_version', MGKSSH_PLUGIN_VERSION );

      register_uninstall_hook( __FILE__, array( 'MGK_SocialShare','mgkssh_uninstall' ));
    }



    static function mgkssh_uninstall() {


     delete_option( 'mgkssh_version');


   }


     public function mgkssh_admin_enqueue_scripts() {
     	global $pagenow;


     	wp_enqueue_media();
      wp_enqueue_style('mgkssh_admin_setting', MGKSSH_PLUGIN_URL . '/assets/css/mgkssh_admin_setting.css', array(), '');


    }




    /**
     * Add options page
     */
    public function mgkssh_add_plugin_page()
    {
      if ( ! empty( $this->_panel ) ) {
        return;
      }

      $mgk_social_share_menu_panel= new MGK_SocialShare_Menu_Panel();
      $parent_slug= $mgk_social_share_menu_panel->add_menu_page();

      add_submenu_page($parent_slug,
        esc_attr__('Magik Social Share',"magik-social-share"),
        esc_attr__('Magik Social Share',"magik-social-share"),
        'manage_options',
        'mgkssh_admin_settings',
        array( $this, 'mgkssh_create_admin_page' )
      );


    }

    /**
     * Options page callback
     */
    public function mgkssh_create_admin_page()
    {
        // Set class property
         if ( ! current_user_can( 'manage_options' ) ) {
          wp_die( esc_html__( 'You do not have sufficient permissions to access this page.','magik-social-share' ) );
         }

    	$this->options = get_option( 'mgkssh_option' );
    	?>
    	<div class="mgkssh_wrap">    		
    		<form id="mgkssh_scolling" method="post" action="options.php" enctype=”multipart/form-data”>
    			<?php
                // This prints out all hidden setting fields
    			settings_fields( 'mgkssh_option_group' );
    			do_settings_sections( 'mgkssh_admin_settings' );
    			submit_button();
    			?>
    		</form>
    	</div>
    	<?php
    }

    /**
     * Register and add settings
     */
    public function mgkssh_page_init()
    {        
    	register_setting(
            'mgkssh_option_group', // Option group
            'mgkssh_option', // Option name
            array( $this, 'mgkssh_sanitize' ) // Sanitize
          );

    	add_settings_section(
            'mgkssh_setting_section', // ID
              esc_attr__('Magik Social Share','magik-social-share'),  // Title
            array( $this, 'mgkssh_print_section_info' ), // Callback
            'mgkssh_admin_settings' // Page
          ); 

    	add_settings_field(
            'mgkssh_enable', // ID
            esc_attr__('Enable Magik Social Share','magik-social-share'), // Title 
            array( $this, 'mgkssh_enable_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 


      add_settings_field(
            'mgkssh_facebook_share', // ID
            esc_attr__('Facebook','magik-social-share'), // Title 
            array( $this, 'mgkssh_facebook_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 

      add_settings_field(
            'mgkssh_twitter_share', // ID
            esc_attr__('Twitter','magik-social-share'), // Title 
            array( $this, 'mgkssh_twitter_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 

      add_settings_field(
            'mgkssh_linkedin_share', // ID
            esc_attr__('Linkedin','magik-social-share'), // Title 
            array( $this, 'mgkssh_linkedin_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 

      add_settings_field(
            'mgkssh_google_share', // ID
            esc_attr__('Google','magik-social-share'), // Title 
            array( $this, 'mgkssh_google_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 
      add_settings_field(
            'mgkssh_pinterest_share', // ID
            esc_attr__('Pinterest','magik-social-share'), // Title 
            array( $this, 'mgkssh_pinterest_callback' ), // Callback
            'mgkssh_admin_settings', // Page
            'mgkssh_setting_section' // Section           
          ); 




    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function mgkssh_sanitize( $input )
    {
    	$new_input = array();
    	
    	if( isset( $input['mgkssh_enable'] ) )
    		$new_input['mgkssh_enable'] = sanitize_text_field( $input['mgkssh_enable'] );

      if( isset( $input['mgkssh_facebook_share'] ) )
        $new_input['mgkssh_facebook_share'] = sanitize_text_field( $input['mgkssh_facebook_share'] );

      if( isset( $input['mgkssh_twitter_share'] ) )
        $new_input['mgkssh_twitter_share'] = sanitize_text_field( $input['mgkssh_twitter_share'] );

      if( isset( $input['mgkssh_linkedin_share'] ) )
        $new_input['mgkssh_linkedin_share'] = sanitize_text_field( $input['mgkssh_linkedin_share'] );

      if( isset( $input['mgkssh_google_share'] ) )
        $new_input['mgkssh_google_share'] = sanitize_text_field( $input['mgkssh_google_share'] );

      if( isset( $input['mgkssh_pinterest_share'] ) )
        $new_input['mgkssh_pinterest_share'] = sanitize_text_field( $input['mgkssh_pinterest_share'] );


      return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function mgkssh_print_section_info()
    {
    	
    }







    /** 
     * Get the settings option array and print one of its values
     */
    public function mgkssh_enable_callback()
    {
    	$checked ="";
    	if(isset($this->options['mgkssh_enable'])) { $checked = ' checked="checked" '; }
    	echo'
    	<input type="checkbox" id="mgkssh_enable" name="mgkssh_option[mgkssh_enable]" value="1" '.$checked .'/>' ;

    }

     /** 
     * Get the settings option array and print one of its values
     */
     public function mgkssh_facebook_callback()
     {
      $checked ="";
      if(isset($this->options['mgkssh_facebook_share'])) { $checked = ' checked="checked" '; }
      echo'
      <input type="checkbox" id="mgkssh_facebook_share" name="mgkssh_option[mgkssh_facebook_share]" value="1" '.$checked .'/>' ;

    }
      /** 
     * Get the settings option array and print one of its values
     */
      public function mgkssh_twitter_callback()
      {
        $checked ="";
        if(isset($this->options['mgkssh_twitter_share'])) { $checked = ' checked="checked" '; }
        echo'
        <input type="checkbox" id="mgkssh_twitter_share" name="mgkssh_option[mgkssh_twitter_share]" value="1" '.$checked .'/>' ;

      }
        /** 
     * Get the settings option array and print one of its values
     */
        public function mgkssh_linkedin_callback()
        {
          $checked ="";
          if(isset($this->options['mgkssh_linkedin_share'])) { $checked = ' checked="checked" '; }
          echo'
          <input type="checkbox" id="mgkssh_linkedin_share" name="mgkssh_option[mgkssh_linkedin_share]" value="1" '.$checked .'/>' ;

        }

    /** 
     * Get the settings option array and print one of its values
     */
    public function mgkssh_google_callback()
    {
      $checked ="";
      if(isset($this->options['mgkssh_google_share'])) { $checked = ' checked="checked" '; }
      echo'
      <input type="checkbox" id="mgkssh_google_share" name="mgkssh_option[mgkssh_google_share]" value="1" '.$checked .'/>' ;

    }

        /** 
     * Get the settings option array and print one of its values
     */
        public function mgkssh_pinterest_callback()
        {
          $checked ="";
          if(isset($this->options['mgkssh_pinterest_share'])) { $checked = ' checked="checked" '; }
          echo'
          <input type="checkbox" id="mgkssh_pinterest_share" name="mgkssh_option[mgkssh_pinterest_share]" value="1" '.$checked .'/>' ;

        }






      }
    }

      if( is_admin() )
       $mgkssh_SocialShare = new MGK_SocialShare();










   // catalog mode front end class
if ( ! class_exists( 'MGK_Front_SocialShare' ) ) {
     class MGK_Front_SocialShare
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




     add_action('wp_enqueue_scripts', array($this,'mgkssh_scripts_styles')); 

     $mgkssh_option=get_option('mgkssh_option');
    

   if(isset($mgkssh_option['mgkssh_enable']) && !empty($mgkssh_option['mgkssh_enable']))
   {
     add_action('woocommerce_share',  array( $this,'mgkssh_product_social_share'),5);
   }
   }

     
   

   public function mgkssh_scripts_styles()
   {
   
    wp_enqueue_style('mgkssh-style', MGKSSH_PLUGIN_URL . '/assets/css/mgkssh_style.css', array(), ''); 
  }


  public function mgkssh_product_social_share()
  {

  
$mgkssh_option=get_option('mgkssh_option');
    $sharing_facebook = isset($mgkssh_option['mgkssh_facebook_share']) ? $mgkssh_option['mgkssh_facebook_share'] : 0;
    $sharing_twitter = isset($mgkssh_option['mgkssh_twitter_share']) ? $mgkssh_option['mgkssh_twitter_share'] : 0;
    $sharing_google = isset($mgkssh_option['mgkssh_google_share']) ? $mgkssh_option['mgkssh_google_share'] : 0;
    $sharing_linkedin = isset($mgkssh_option['mgkssh_linkedin_share']) ? $mgkssh_option['mgkssh_linkedin_share'] : 0;
    $sharing_pinterest = isset($mgkssh_option['mgkssh_pinterest_share']) ? $mgkssh_option['mgkssh_pinterest_share'] : 0;


    if (!empty($sharing_facebook) ||
      !empty($sharing_twitter) ||
      !empty($sharing_linkedin) ||
      !empty($sharing_google) ||
      !empty($sharing_pinterest)
    ) :
    ?>
    <div class="social">
      <ul>
        <?php if (!empty($sharing_facebook)) : ?>
          <li class="fb pull-left">
            <a onclick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[url]=<?php echo esc_html(urlencode(get_permalink()));?>','sharer', 'toolbar=0,status=0,width=620,height=280');"  href="javascript:;">

            </a>
          </li>
        <?php endif; ?>

        <?php if (!empty($sharing_twitter)) :  ?>
          <li class="tw pull-left">
            <a onclick="popUp=window.open('http://twitter.com/home?status=<?php echo esc_html(urlencode(get_the_title())); ?> <?php echo esc_html(urlencode(get_permalink())); ?>','sharer','scrollbars=yes,width=800,height=400');popUp.focus();return false;"  href="javascript:;">

            </a>
          </li>
        <?php endif; ?>

        <?php if (!empty($sharing_google)) :  ?>
          <li class="googleplus pull-left">
           <a href="javascript:;" onclick="popUp=window.open('https://plus.google.com/share?url=<?php echo esc_html(urlencode(get_permalink())); ?>','sharer','scrollbars=yes,width=800,height=400');popUp.focus();return false;">

           </a>
         </li>
       <?php endif; ?>

       <?php if (!empty($sharing_linkedin )):?>
        <li  class="linkedin pull-left">
          <a  onclick="popUp=window.open('http://linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_html(urlencode(get_permalink())); ?>&amp;title=<?php echo esc_html(urlencode(get_the_title())); ?>','sharer','scrollbars=yes,width=800,height=400');popUp.focus();return false;" href="javascript:;">

          </a>
        </li>
      <?php endif; ?>



      <?php if (!empty($sharing_pinterest)) :  ?>
        <li class="pintrest pull-left">
          <a onclick="popUp=window.open('http://pinterest.com/pin/create/button/?url=<?php echo esc_html(urlencode(get_permalink())); ?>&amp;description=<?php echo esc_html(urlencode(get_the_title())); ?>&amp;media=<?php $arrImages = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo has_post_thumbnail() ? esc_html($arrImages[0])  : "" ; ?>','sharer','scrollbars=yes,width=800,height=400');popUp.focus();return false;" href="javascript:;">

          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
<?php endif;

}

}

}



$mgkssh_front_SocialShare = new MGK_Front_SocialShare();

?>