<?php

if ( !class_exists( 'MGK_WooCategory_Menu_Panel' ) ) {
    /**
     * MGK Menu Panel Plugin Panel for WooCommerce
     *
     * Setting Page to Manage Plugins

     * @package    magikcommerce
     * @since      1.0

     */
    class MGK_WooCategory_Menu_Panel {

        /**
         * @var string version of class
         */
        public $version = '1.0.0';

        /**
         * @var array a setting list of parameters
         */
        public $settings = array();

        /**
         * @var array a setting list of parameters
         */
        public static $wc_type = array( 'checkbox', 'textarea', 'multiselect', 'multi_select_countries', 'image_width' );

        /**
         * @var array
         */
        protected $_tabs_path_files;

        /**
         * @var bool
         */
        protected static $_actions_initialized = false;

        /**
         * Constructor
         *
         * @since    1.0
  
         */
        public function __construct( $args = array() ) {

            $args = apply_filters( 'mgk_menu_panel_option_args', $args );
            if ( !empty( $args ) ) {
                $this->settings         = $args;
                //$this->_tabs_path_files = $this->get_tabs_path_files();

                if ( isset( $this->settings[ 'create_menu_page' ] ) && $this->settings[ 'create_menu_page' ] ) {
                    $this->add_menu_page();
                }


            }

        }
        public function add_menu_page() {
            global $admin_page_hooks;

            if ( !isset( $admin_page_hooks[ 'mgkcmo_admin_settings' ] ) &&  !isset( $admin_page_hooks[ 'mgkisr_admin_settings' ] )&&  !isset( $admin_page_hooks[ 'mgkssh_admin_settings' ] ) &&  !isset( $admin_page_hooks[ 'mgkwooas_admin_settings' ] ) &&  !isset( $admin_page_hooks[ 'mgkwci_admin_settings' ] ) &&  !isset( $admin_page_hooks[ 'mgkwst_admin_settings' ] )) {

                $capability = 'manage_options';
                
                if(class_exists('MGK_CatalogMode'))
                {
                 $menu_slug="mgkcmo_admin_settings";          

                 add_menu_page("Magik Plugins","Magik Plugins",  $capability, $menu_slug, array($this,'mgk_plugins_panel_callback'));
                 return $menu_slug;
             }

             if(class_exists('MGK_InfiniteScroll'))
             {
                 $menu_slug="mgkisr_admin_settings";
                 add_menu_page("Magik Plugins","Magik Plugins",  $capability, $menu_slug, array($this,'mgk_plugins_panel_callback'));
                 return $menu_slug;



             }

             if(class_exists('MGK_SocialShare'))
             {
                 $menu_slug="mgkssh_admin_settings";
                 add_menu_page("Magik Plugins","Magik Plugins",  $capability, $menu_slug, array($this,'mgk_plugins_panel_callback'));
                 return $menu_slug;

             }

             if(class_exists('MGK_WooAjaxSearch'))
             {
                 $menu_slug="mgkwooas_admin_settings";
                 add_menu_page("Magik Plugins","Magik Plugins",  $capability, $menu_slug, array($this,'mgk_plugins_panel_callback'));
                 return $menu_slug;

             }
             if(class_exists('MGK_WooCategory'))
             {
                 $menu_slug="mgkwci_admin_settings";
                 add_menu_page("Magik Plugins","Magik Plugins",  $capability, $menu_slug, array($this,'mgk_plugins_panel_callback'));
                 return $menu_slug;

             }
             
        


         }
         else{

            if (isset( $admin_page_hooks[ 'mgkcmo_admin_settings' ] ))
            {
                $menu_slug="mgkcmo_admin_settings";          

                return $menu_slug;
            }


            if(isset( $admin_page_hooks[ 'mgkisr_admin_settings' ] ))
            {
               $menu_slug="mgkisr_admin_settings";
               return $menu_slug;
            }


            if(isset( $admin_page_hooks[ 'mgkssh_admin_settings' ] ))
            {
              $menu_slug="mgkssh_admin_settings";
              return $menu_slug;
            }

            if(isset( $admin_page_hooks[ 'mgkwooas_admin_settings' ] ))
            {

             $menu_slug="mgkwooas_admin_settings";
            return $menu_slug;

            }
  
            if(isset( $admin_page_hooks[ 'mgkwci_admin_settings' ] ) )
            {
             $menu_slug="mgkwci_admin_settings";
             return $menu_slug;    
            }

            
    }  
}

public function mgk_plugins_panel_callback()
{


}
}
}

?>