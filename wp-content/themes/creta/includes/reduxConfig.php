<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }


    // This is your option name where all the Redux data is stored.
    $opt_name = "mgk_option";

    // This line is only for altering the demo. Can be easily removed.
    $opt_name = apply_filters( 'redux_demo/opt_name', $opt_name );

    /*
     *
     * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
     *
     */



    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => $theme->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'      => $theme->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
         'menu_title' => esc_html__('Creta Options', 'creta'),
        'page_title' => esc_html__('Creta Options', 'creta'),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => true,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => true,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-portfolio',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => 'creta_Options',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => true,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => true,
    

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
   
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => '',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '',
        // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                       // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'red',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    // Panel Intro text -> before the form
    if ( ! isset( $args['global_variable'] ) || $args['global_variable'] !== false ) {
        if ( ! empty( $args['global_variable'] ) ) {
            $v = $args['global_variable'];
        } else {
            $v = str_replace( '-', '_', $args['opt_name'] );
        }
        $args['intro_text'] = sprintf(__( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'creta' ), $v );
    } else {
        $args['intro_text'] = '<p>'.esc_html__('This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.', 'creta' ).'</p>';
    }

    // Add content after the form.
    $args['footer_text'] ='<p>'. esc_html__( 'This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.', 'creta' ).'</p>';

    Redux::setArgs( $opt_name, $args );
    /*
     * ---> END ARGUMENTS
     */


    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => esc_html__( 'Theme Information 1', 'creta' ),
            'content' => esc_html__( 'This is the tab content, HTML is allowed.', 'creta' )
        ),
        array(
            'id'      => 'redux-help-tab-2',
            'title'   => esc_html__( 'Theme Information 2', 'creta' ),
            'content' => esc_html__( 'This is the tab content, HTML is allowed.', 'creta' )
        )
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = esc_html__( 'This is the sidebar content, HTML is allowed.', 'creta' );
    Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    /*

        As of Redux 3.5+, there is an extensive API. This API can be used in a mix/match mode allowing for


     */
  global $woocommerce;
               $cat_arg=array();
               $cat_data='';
              
                if(class_exists('WooCommerce')) {
                   
                    $cat_data='terms';
                    $cat_arg=array('taxonomies'=>'product_cat', 'args'=>array());
                   }

    // -> Home Settings
       Redux::setSection( $opt_name, array(
        'title' => esc_html__('Home Settings', 'creta'),
        'desc' => esc_html__('Home page settings ', 'creta'),
        'icon' => 'el-icon-home',
             
        'fields' => array( 

            array(
                'id' => 'theme_layout',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Theme Variation', 'creta'),
                'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'creta'),
                'options' => array(
                    'default' => array(
                        'title' => esc_html__('Default', 'creta'),
                        'alt' => esc_html__('Default', 'creta'),
                        'img' => MAGIKCRETA_THEME_URI . '/images/variations/screen1.jpg'
                    ),
                    'version2' => array(
                        'title' => esc_html__('Version2', 'creta'),
                        'alt' => esc_html__('Version 2', 'creta'),
                        'img' => MAGIKCRETA_THEME_URI . '/images/variations/screen2.jpg'
                    ),
                    
                    
                ),
                'default' => 'default'
            ),   
            array(
                'id' => 'welcome_msg',
                'type' => 'text',
                'title' => esc_html__('Enter your welcome message here', 'creta'),
                'subtitle' => esc_html__('Enter your welcome message here.', 'creta'),
                'required' => array('theme_layout', '=', 'default'),
                
                'desc' => esc_html__('', 'creta'),
                
                
            ),             
            array(
                'id' => 'enable_home_gallery',
                'type' => 'switch',
                'title' => esc_html__('Enable Home Page Gallery', 'creta'),
                'subtitle' => esc_html__('You can enable/disable Home page Gallery', 'creta')
            ),

            array(
                'id' => 'home-page-slider',
                'type' => 'slides',
                'title' => esc_html__('Home Slider Uploads', 'creta'),
                'required' => array('enable_home_gallery', '=', '1'),
                'subtitle' => esc_html__('Unlimited slide uploads with drag and drop sortings.', 'creta'),
                'placeholder' => array(
                    'title' => esc_html__('This is a title', 'creta'),
                    'description' => esc_html__('Description Here', 'creta'),
                    'url' => esc_html__('Give us a link!', 'creta'),
                ),

            ),
            
            array(
                'id' => 'home-side-banner1',
                'type' => 'media',

                'required' => array('enable_home_side_banners', '=', '1'),
                'title' => esc_html__('Home slider Side Banner 1', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on Side of home page gallery', 'creta')
            ),  
            array(
                'id' => 'home-side-banner1-url',
                'type' => 'text',
                'required' => array('enable_home_side_banners', '=', '1'),
                'title' => esc_html__('Home slider Side Banner-1 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ), 
            array(
                'id' => 'home-side-banner2',
                'type' => 'media',
                'required' => array('enable_home_side_banners', '=', '1'),
                'title' => esc_html__('Home slider Side Banner 2', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on Side of home page gallery', 'creta')
            ),  
            array(
                'id' => 'home-side-banner2-url',
                'type' => 'text',
                'required' => array('enable_home_side_banners', '=', '1'),
                'title' => esc_html__('Home slider Side Banner-2 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ), 
            
            array(
                'id' => 'enable_home_sub_banners',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Enable Home Page Slider sub Banners', 'creta'),
                'subtitle' => esc_html__('You can enable/disable Home page Slider Bottom Banners', 'creta')
            ),

            array(
                'id' => 'home-sub-banner1',
                'type' => 'media',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 1', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on bottom of slider', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner1-url',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 1 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner1-text',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home slider sub Banner-1 text', 'creta'),
                'subtitle' => esc_html__('Title for the banner-1.', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner2',
                'type' => 'media',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 2', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on  bottom of slider', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner2-url',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 2 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ),


            array(
                'id' => 'home-sub-banner2-text',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home slider sub Banner-2 text', 'creta'),
                'subtitle' => esc_html__('Title for the banner-2.', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner3',
                'type' => 'media',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 3', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on bottom of slider', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner3-url',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 3 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ),


            array(
                'id' => 'home-sub-banner3-text',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home slider sub Banner-3 text', 'creta'),
                'subtitle' => esc_html__('Title for the banner-3.', 'creta'),
            ),
            array(
                'id' => 'home-sub-banner4',
                'type' => 'media',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 4', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on bottom of slider', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner4-url',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 4 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ),


            array(
                'id' => 'home-sub-banner4-text',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home slider sub Banner-4 text', 'creta'),
                'subtitle' => esc_html__('Title for the banner-4.', 'creta'),
            ), 

            array(
                'id' => 'home-sub-banner5',
                'type' => 'media',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 5', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload banner to appear on bottom of slider', 'creta'),
            ),

            array(
                'id' => 'home-sub-banner5-url',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home Sub Banner 5 URL', 'creta'),
                'subtitle' => esc_html__('URL for the banner.', 'creta'),
            ),
            

            array(
                'id' => 'home-sub-banner5-text',
                'type' => 'text',
                'required' => array('enable_home_sub_banners', '=', '1'),
                'title' => esc_html__('Home slider sub Banner-5 text', 'creta'),
                'subtitle' => esc_html__('Title for the banner-5.', 'creta'),
            ),


            array(
                'id' => 'enable_home_hotdeal_products',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show Hot Deal Product', 'creta'),
                'subtitle' => esc_html__('You can show Hot Deal product on home page.', 'creta')
            ),

            array(
                'id' => 'enable_home_hotdeal_on_products_page',
                'type' => 'switch',
                
                'title' => esc_html__('Show Hot Deal on Product Detail Page', 'creta'),
                'subtitle' => esc_html__('You can show Hot Deal on Product Detail page.', 'creta')
            ),
            
            
            array(
                'id' => 'enable_home_new_products',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show New Products', 'creta'),
                'subtitle' => esc_html__('You can show Show New Products on home page.', 'creta')
            ),   
            array(
                'id'=>'home_newproduct_categories',
                'type' => 'select',
                'multi'=> true,                        
                'data' => $cat_data,                            
                'args' => $cat_arg,
                'title' => esc_html__('New Product Category', 'creta'), 
                'required' => array('enable_home_new_products', '=', '1'),
                'subtitle' => esc_html__('Please choose New Product Category to show  its product in home page.', 'creta'),
                'desc' => '',
            ),

            array(
                'id' => 'enable_home_bestseller_products',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show Best Seller Products', 'creta'),
                'subtitle' => esc_html__('You can show best seller products on home page.', 'creta')
            ),

            
            
            array(
                'id' => 'home_bestseller_products-text',
                'type' => 'text',
                'required' => array('enable_home_bestseller_products', '=', '1'),
                'title' => esc_html__('Home bestseller products text', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('home page bestseller_products-text ', 'creta')
                
            ),
            
            array(
                'id' => 'bestseller_product_url',
                'type' => 'text',
                'required' =>array('enable_home_bestseller_products', '=', '1'),
                'title' => esc_html__('Home Best seller   Url', 'creta'),
                'subtitle' => esc_html__('Home Best seller  Url.', 'creta'),
            ),
            array(
                'id' => 'bestseller_per_page',
                'type' => 'text',
                'required' => array('enable_home_bestseller_products', '=', '1'),
                'title' => esc_html__('Number of Bestseller Products', 'creta'),
                'subtitle' => esc_html__('Number of Bestseller products on home page.', 'creta')
            ), 
            
            
            array(
                'id' => 'enable_home_featured_products',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show Featured Products', 'creta'),
                'subtitle' => esc_html__('You can show featured products on home page.', 'creta')
            ),
            
            array(
                'id' => 'enable_home_featured_products-text',
                'type' => 'text',
                'required' =>array('enable_home_featured_products', '=', '1'),
                'title' => esc_html__('Home Featured Products Text', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('home page featured products text ', 'creta')
            ),

            
            array(
                'id' => 'featured_product_url',
                'type' => 'text',
                'required' => array('enable_home_featured_products', '=', '1'),
                'title' => esc_html__('Home Featured  Url', 'creta'),
                'subtitle' => esc_html__('Home Featured  Url.', 'creta'),
            ),
            
            array(
                'id' => 'featured_per_page',
                'type' => 'text',
                'required' => array('enable_home_featured_products', '=', '1'),
                'title' => esc_html__('Number of Featured Products', 'creta'),
                'subtitle' => esc_html__('Number of Featured products on home page.', 'creta')
            ),                             

            

            array(
                'id' => 'enable_home_recommended_products',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show Recommended Products', 'creta'),
                'subtitle' => esc_html__('You can show Recommended products on home page.', 'creta')
            ),
            array(
                'id' => 'enable_home_recommended_products-text',
                'type' => 'text',
                'required' => array('enable_home_recommended_products', '=', '1'),
                'title' => esc_html__('Home Recommended Products Text', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('home page recommended products text ', 'creta')
            ),
            array(
                'id' => 'recommended_product_url',
                'type' => 'text',
                'required' => array('enable_home_recommended_products', '=', '1'),
                'title' => esc_html__('Home Recommended   Url', 'creta'),
                'subtitle' => esc_html__('Home Recommended   Url.', 'creta'),
            ),
            array(
                'id' => 'recommended_per_page',
                'type' => 'text',
                'required' => array('enable_home_recommended_products', '=', '1'),
                'title' => esc_html__('Number of Recommended Products', 'creta'),
                'subtitle' => esc_html__('Number of Recommended products on home page.', 'creta')
            ),
            

            array(
                'id' => 'enable_home_related_products',
                'type' => 'switch',
                'title' => esc_html__('Show Related Products', 'creta'),
                'subtitle' => esc_html__('You can show Related products on home page.', 'creta')
            ),
            array(
                'id' => 'related_products-text',
                'type' => 'text',
                'required' => array(array('enable_home_related_products', '=', '1'),array('theme_layout', '=', 'default')),        
                'title' => esc_html__('Related products text', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('related products-text ', 'creta')
                
            ),
            array(
                'id' => 'related_product_url',
                'type' => 'text',
                'required' => array(array('enable_home_related_products', '=', '1'),array('theme_layout', '=', 'default')), 
                'title' => esc_html__('Home Related   Url', 'creta'),
                'subtitle' => esc_html__('Home Related  Url.', 'creta'),
            ),
            array(
                'id' => 'related_per_page',
                'type' => 'text',
                'required' => array('enable_home_related_products', '=', '1'), 
                'title' => esc_html__('Number of Related Products', 'creta'),
                'subtitle' => esc_html__('Number of Related products on home page.', 'creta')
            ),
            

            array(
                'id' => 'enable_home_upsell_products',
                'type' => 'switch',
                'title' => esc_html__('Show Upsell Products', 'creta'),
                'subtitle' => esc_html__('You can show Upsell products on home page.', 'creta')
            ),
            array(
                'id' => 'upsell_products-text',
                'type' => 'text',
                'required' => array(array('enable_home_upsell_products', '=', '1'),array('theme_layout', '=', 'default')),
                'title' => esc_html__('Upsell Products text', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('upsell products text ', 'creta')
                
            ),
            array(
                'id' => 'upsell_product_url',
                'type' => 'text',
                'required' => array(array('enable_home_upsell_products', '=', '1'),array('theme_layout', '=', 'default')),
                'title' => esc_html__('Home Upsell   Url', 'creta'),
                'subtitle' => esc_html__('Home Upsell  Url.', 'creta'),
            ),
            array(
                'id' => 'upsell_per_page',
                'type' => 'text',
                'required' => array('enable_home_upsell_products', '=', '1'), 
                'title' => esc_html__('Number of Upsell Products', 'creta'),
                'subtitle' => esc_html__('Number of Upsell products on home page.', 'creta')
            ),
            
            array(
                'id' => 'enable_cross_sells_products',
                'type' => 'switch',
                'title' => esc_html__('Show cross sells Products', 'creta'),
                'subtitle' => esc_html__('You can show cross sells products.', 'creta')
            ),

            array(
                'id' => 'cross_per_page',
                'type' => 'text',
                'required' => array('enable_cross_sells_products', '=', '1'), 
                'title' => esc_html__('Number of cross sells Products', 'creta'),
                'subtitle' => esc_html__('Number of cross sells Products', 'creta')
            ),
            
            array(
                'id' => 'header_breadcrumb',
                'type' => 'media',
                'required' => array('theme_layout', '=', 'version2'),
                'title' => esc_html__('Header breadcrumb image', 'creta'),
                'desc' => esc_html__('', 'creta'),
                'subtitle' => esc_html__('Upload Header breadcrumb image ', 'creta')
            ),
            array(
                'id' => 'enable_home_blog_posts',
                'type' => 'switch',
                'required' => array('theme_layout', '=', 'default'),
                'title' => esc_html__('Show Home Blog', 'creta'),
                'subtitle' => esc_html__('You can show latest blog post on home page.', 'creta')
            ),

                ), // fields array ends
) );
       // Edgesettings: General Settings Tab
Redux::setSection( $opt_name,array(
    'icon' => 'el-icon-cogs',
    'title' => esc_html__('General Settings', 'creta'),
    'fields' => array(

        array(
            'id'       => 'category_pagelayout',
            'type'     => 'radio',
            'title'    => esc_html__('Set Category Page Default Layout', 'creta'), 
            'subtitle' => esc_html__('Set Category Page Default Layout', 'creta'),
            'desc'     => esc_html__('You cans set Category Page Default Layout here ', 'creta'),
                    //Must provide key => value pairs for radio options
            'options'  => array(
                'grid' => 'Grid', 
                'list' => 'List', 
                
            ),
            'default' => 'grid'
        ),
        
        array(
           'id'       => 'category_item',
           'type'     => 'spinner', 
           'title'    => esc_html__('Product display in product category page', 'creta'),
           'subtitle' => esc_html__('Number of item display in product category page','creta'),
           'desc'     => esc_html__('Number of item display in product category page', 'creta'),
           'default'  => '9',
           'min'      => '0',
           'step'     => '1',
           'max'      => '100',
       ),
        
        array(
            'id'       => 'enable_brand_logo',
            'type'     => 'switch',                    
            'title'    => esc_html__( 'Enable Company Logo Uploads', 'creta' ),
            'subtitle' => esc_html__( 'You can enable/disable Company Logo Uploads', 'creta' ),
            'default' => '0'
        ),                   
        array(
            'id' => 'all-company-logos',
            'type' => 'slides',
            'required' => array('enable_brand_logo', '=', '1'),
            'title' => esc_html__('Company Logo Uploads', 'creta'),
            'subtitle' => esc_html__('Unlimited Logo uploads with drag and drop sortings.', 'creta'),
            'placeholder' => array(
                'title' => esc_html__('This is a title', 'creta'),
                'description' => esc_html__('Description Here', 'creta'),
                'url' => esc_html__('Give us a link!', 'creta'),
            ),
        ),

        array(
            'id'       => 'enable_product_socialshare',
            'type'     => 'switch',                    
            'title'    => esc_html__( 'Enable Product Page Social Share ', 'creta' ),
            'subtitle' => esc_html__( 'You can enable/disable Product Page Social Share', 'creta' ),
            'default' => '0'
        ), 

        array(
            'id' => 'back_to_top',
            'type' => 'switch',
            'title' => esc_html__('Back To Top Button', 'creta'),
            'subtitle' => esc_html__('Toggle whether or not to enable a back to top button on your pages.', 'creta'),
            'default' => true,
        ),

        
        
    )
));

  // Edgesettings: General Options -> Styling Options Settings Tab
Redux::setSection( $opt_name, array(
    'icon' => 'el-icon-website',
    'title' => esc_html__('Styling Options', 'creta'),
    
    'fields' => array(
        array(
            'id' => 'opt-animation',
            'type' => 'switch',
            'title' => esc_html__('Use animation effect', 'creta'),
            'subtitle' => esc_html__('', 'creta'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ), 

        array(
            'id' => 'set_body_background_img_color',
            'type' => 'switch',
            'title' => esc_html__('Set Body Background', 'creta'),
            'subtitle' => esc_html__('', 'creta'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-background',
            'type' => 'background',
            'required' => array('set_body_background_img_color', '=', '1'),
            'output' => array('body'),
            'title' => esc_html__('Body Background', 'creta'),
            'subtitle' => esc_html__('Body background with image, color, etc.', 'creta'),               
            'transparent' => false,
        ),                   
        array(
            'id' => 'opt-color-footer',
            'type' => 'color',
            'title' => esc_html__('Footer Background Color', 'creta'),
            'subtitle' => esc_html__('Pick a background color for the footer.', 'creta'),
            'validate' => 'color',
            'transparent' => false,
            'mode' => 'background',
            'output' => array('footer','.footer-bottom')
        ),
        array(
            'id' => 'opt-color-rgba',
            'type' => 'color',
            'title' => esc_html__('Header Nav Menu Background', 'creta'),
            'output' => array('.mgk-main-menu'),
            'mode' => 'background',
            'validate' => 'color',
            'transparent' => false,
        ),
        array(
            'id' => 'opt-color-header',
            'type' => 'color',
            'title' => esc_html__('Header Background', 'creta'),
            'transparent' => false,
            'output' => array('header'),
            'mode' => 'background',
        ),  
        
    )
));

  // header tab
Redux::setSection( $opt_name, array(
    'icon' => 'el-icon-file-alt',
    'title' => esc_html__('Header', 'creta'),
    'heading' => esc_html__('All header related options are listed here.', 'creta'),
    'desc' => esc_html__('', 'creta'),
    'fields' => array(
         array(
            'id' => 'enable_sticky_header',
            'type' => 'switch',
            'title' => esc_html__('Sticky Header Option', 'creta'),
            'subtitle' => esc_html__('You can switch on/off sticky header.', 'creta'),
            'default' => 1
         ),
        array(
            'id' => 'enable_header_currency',
            'type' => 'switch',
            'title' => esc_html__('Show Currency HTML', 'creta'),
            'subtitle' => esc_html__('You can show Currency in the header.', 'creta')
        ),
        array(
            'id' => 'enable_header_language',
            'type' => 'switch',
            'title' => esc_html__('Show Language HTML', 'creta'),
            'subtitle' => esc_html__('You can show Language in the header.', 'creta')
        ),
        array(
            'id' => 'header_use_imagelogo',
            'type' => 'checkbox',
            'title' => esc_html__('Use Image for Logo?', 'creta'),
            'subtitle' => esc_html__('If left unchecked, plain text will be used instead (generated from site name).', 'creta'),
            'desc' => esc_html__('', 'creta'),
            'default' => '1'
        ),
        array(
            'id' => 'header_logo',
            'type' => 'media',
            'required' => array('header_use_imagelogo', '=', '1'),
            'title' => esc_html__('Logo Upload', 'creta'),
            'desc' => esc_html__('', 'creta'),
            'subtitle' => esc_html__('Upload your logo here and enter the height of it below', 'creta'),
        ),
        array(
            'id' => 'header_logo_height',
            'type' => 'text',
            'required' => array('header_use_imagelogo', '=', '1'),
            'title' => esc_html__('Logo Height', 'creta'),
            'subtitle' => esc_html__('Don\'t include "px" in the string. e.g. 30', 'creta'),
            'desc' => '',
            'validate' => 'numeric'
        ),
        array(
            'id' => 'header_logo_width',
            'type' => 'text',
            'required' => array('header_use_imagelogo', '=', '1'),
            'title' => esc_html__('Logo Width', 'creta'),
            'subtitle' => esc_html__('Don\'t include "px" in the string. e.g. 30', 'creta'),
            'desc' => '',
            'validate' => 'numeric'
        ),    
        
        array(
            'id' => 'header_remove_header_search',
            'type' => 'checkbox',
            'title' => esc_html__('Remove Header Search', 'creta'),
            'subtitle' => esc_html__('Active to remove the search functionality from your header', 'creta'),
            'desc' => esc_html__('', 'creta'),
            'default' => '0'
        ),
        array(
            'id' => 'header_show_ajax_search',
            'type' => 'checkbox',
            'title' => esc_html__('Enable Ajax Search', 'creta'),
            'subtitle' => esc_html__('Header ajax search active only when magik wooajax search plugin activated and option enable', 'creta'),
            'desc' => esc_html__('', 'creta'),
            'default' => '0'
        ),
        array(
            'id' => 'header_show_info_banner',
            'type' => 'switch',
            'title' => esc_html__('Show Info Banners', 'creta'),
            'default' => '0'
        ),

        
        array(
            'id' => 'header_shipping_banner',
            'type' => 'text',
            'required' => array('header_show_info_banner', '=', '1'),
            'title' => esc_html__('Shipping Banner Text', 'creta'),
        ),

        array(
            'id' => 'header_customer_support_banner',
            'type' => 'text',
            'required' => array('header_show_info_banner', '=', '1'),
            'title' => esc_html__('Customer Support Banner Text', 'creta'),
        ),

        array(
            'id' => 'header_moneyback_banner',
            'type' => 'text',
            'required' => array('header_show_info_banner', '=', '1'),
            'title' => esc_html__('Warrant/Gaurantee Banner Text', 'creta'),
        ),
        array(
            'id' => 'header_returnservice_banner',
            'type' => 'text',
            'required' => array('header_show_info_banner', '=', '1'),
            'title' => esc_html__('Return service Banner Text', 'creta'),
        ),
        
        
        
                ) //fields end
));
      // Edgesettings: Menu Tab
Redux::setSection( $opt_name, array(
    'icon' => 'el el-website icon',
    'title' => esc_html__('Menu', 'creta'),
    'heading' => esc_html__('All Menu related options are listed here.', 'creta'),
    'desc' => esc_html__('', 'creta'),
    'fields' => array(
     array(
        'id' => 'show_menu_arrow',
        'type' => 'switch',
        'title' => esc_html__('Show Menu Arrow', 'creta'),
        'desc'  => esc_html__('Show arrow in menu.', 'creta'),
        
    ),               
     array(
        'id'       => 'login_button_pos',
        'type'     => 'radio',
        'title'    => esc_html__('Show Login/sign and logout link', 'creta'),                   
        'desc'     => esc_html__('Please Select any option from above.', 'creta'),
                     //Must provide key => value pairs for radio options
        'options'  => array(
            'none' => 'None', 
            'toplinks' => 'In Top Menu', 
            'main_menu' => 'In Main Menu'
        ),
        'default' => 'none'
    )
     
                ) // fields ends here
));
 // Edgesettings: Footer Tab
Redux::setSection( $opt_name,array(
    'icon' => 'el-icon-file-alt',
    'title' => esc_html__('Footer', 'creta'),
    'heading' => esc_html__('All footer related options are listed here.', 'creta'),
    'desc' => esc_html__('', 'creta'),
    'fields' => array(
       array(
        'id'       => 'enable_mailchimp_form',
        'type'     => 'switch',                    
        'title'    => esc_html__( 'Enable Mailchimp Form', 'creta' ),
        'subtitle' => esc_html__( 'You can enable/disable Mailchimp Form', 'creta' ),
        'default' => '0'
    ), 
       array(
        'id' => 'footer_color_scheme',
        'type' => 'switch',
        'title' => esc_html__('Custom Footer Color Scheme', 'creta'),
        'subtitle' => esc_html__('', 'creta')
    ),               
       array(
        'id' => 'footer_copyright_background_color',
        'type' => 'color',
        'required' => array('footer_color_scheme', '=', '1'),
        'transparent' => false,
        'title' => esc_html__('Footer Copyright Background Color', 'creta'),
        'subtitle' => esc_html__('', 'creta'),
        'validate' => 'color',
    ),
       array(
        'id' => 'footer_copyright_font_color',
        'type' => 'color',
        'required' => array('footer_color_scheme', '=', '1'),
        'transparent' => false,
        'title' => esc_html__('Footer Copyright Font Color', 'creta'),
        'subtitle' => esc_html__('', 'creta'),
        'validate' => 'color',
    ), 
       
       
       array(
        'id' => 'footer-text1',
        'type' => 'editor',
        'title' => esc_html__('Footer Text Home', 'creta'), 
        'required' => array('theme_layout', '=', 'version2'),                    
        'subtitle' => esc_html__('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'creta'),
        'default' => '',
    ), 
       
       
       array(
        'id' => 'enable_footer_middle',
        'type' => 'switch',                       
        'title' => esc_html__('Enable footer middle', 'creta'),
        'subtitle' => esc_html__('You can enable/disable Footer Middle', 'creta')
    ),

       array(
        'id' => 'footer_middle',
        'type' => 'editor',
        'title' => esc_html__('Footer Middle Text ', 'creta'), 
        'required' => array('enable_footer_middle', '=', '1'),               
        'subtitle' => esc_html__('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'creta'),
        'default' => '',
    ),    
       
       array(
        'id' => 'bottom-footer-text',
        'type' => 'editor',
        'title' => esc_html__('Bottom Footer Text', 'creta'),
        'subtitle' => esc_html__('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'creta'),
        'default' => esc_html__('Powered by Magik', 'creta'),
    ),
       
       
                ) // fields ends here
));

    // -> Blog Tab
Redux::setSection( $opt_name, array(
    'icon' => 'el-icon-pencil',
    'title' => esc_html__('Blog Page', 'creta'),
    'fields' => array( 
     array(
        'id' => 'blog-page-layout',
        'type' => 'image_select',
        'title' => esc_html__('Blog Page Layout', 'creta'),
        'subtitle' => esc_html__('Select main blog listing and category page layout from the available blog layouts.', 'creta'),
        'options' => array(
            '1' => array(
                'alt' => 'Left sidebar',
                'img' => MAGIKCRETA_THEME_URI . '/images/magik_col/category-layout-1.png'

            ),
            '2' => array(
                'alt' => 'Right Right',
                'img' => MAGIKCRETA_THEME_URI . '/images/magik_col/category-layout-2.png'
            ),
            '3' => array(
                'alt' => '2 Column Right',
                'img' => MAGIKCRETA_THEME_URI . '/images/magik_col/category-layout-3.png'
            )                                                                                 
            
        ),
        'default' => '2'
    ), 
     array(
        'id' => 'blog_show_authors_bio',
        'type' => 'switch',
        'title' => esc_html__('Author\'s Bio', 'creta'),
        'subtitle' => esc_html__('Show Author Bio on Blog page.', 'creta'),
        'default' => true,
        'desc' => esc_html__('', 'creta')
    ),                  
     array(
        'id' => 'blog_show_post_by',
        'type' => 'switch',
        'title' => esc_html__('Display Post By', 'creta'),
        'default' => true,
        'subtitle' => esc_html__('Display Psot by Author on Listing Page', 'creta')
    ),
     array(
        'id' => 'blog_display_tags',
        'type' => 'switch',
        'title' => esc_html__('Display Tags', 'creta'),
        'default' => true,
        'subtitle' => esc_html__('Display tags at the bottom of posts.', 'creta')
    ),
     array(
        'id' => 'blog_full_date',
        'type' => 'switch',
        'title' => esc_html__('Display Full Date', 'creta'),
        'default' => true,
        'subtitle' => esc_html__('This will add date of post meta on all blog pages.', 'creta')
    ),
     array(
        'id' => 'blog_display_comments_count',
        'type' => 'switch',
        'default' => true,
        'title' => esc_html__('Display Comments Count', 'creta'),
        'subtitle' => esc_html__('Display Comments Count on Blog Listing.', 'creta')
    ),
     array(
        'id' => 'blog_display_category',
        'type' => 'switch',
        'title' => esc_html__('Display Category', 'creta'),
        'default' => true,
        'subtitle' => esc_html__('Display Comments Category on Blog Listing.', 'creta')
    ),
     array(
        'id' => 'blog_display_view_counts',
        'type' => 'switch',
        'title' => esc_html__('Display View Counts', 'creta'),
        'default' => true,
        'subtitle' => esc_html__('Display View Counts on Blog Listing.', 'creta')
    ),                  
 )
));
  // Edgesettings: Social Media Tab
Redux::setSection( $opt_name,array(
    'icon' => 'el-icon-file',
    'title' => esc_html__('Social Media', 'creta'),
    'fields' => array(
       array(
        'id'       => 'enable_social_link_footer',
        'type'     => 'switch',                    
        'title'    => esc_html__( 'Enable Social Link In Footer', 'creta' ),                        
        'default' => '0'
    ),
       array(
        'id' => 'social_facebook',
        'type' => 'text',
        'title' => esc_html__('Facebook URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Facebook URL.', 'creta'),
    ),
       array(
        'id' => 'social_twitter',
        'type' => 'text',
        'title' => esc_html__('Twitter URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Twitter URL.', 'creta'),
    ),
       array(
        'id' => 'social_googlep',
        'type' => 'text',
        'title' => esc_html__('Google+ URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Google Plus URL.', 'creta'),
    ),
       
       array(
        'id' => 'social_pinterest',
        'type' => 'text',
        'title' => esc_html__('Pinterest URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Pinterest URL.', 'creta'),
    ),
       array(
        'id' => 'social_youtube',
        'type' => 'text',
        'title' => esc_html__('Youtube URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Youtube URL.', 'creta'),
    ),
       array(
        'id' => 'social_linkedin',
        'type' => 'text',
        'title' => esc_html__('LinkedIn URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your LinkedIn URL.', 'creta'),
    ),
       array(
        'id' => 'social_rss',
        'type' => 'text',
        'title' => esc_html__('RSS URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your RSS URL.', 'creta'),
    ) ,  
       array(
        'id' => 'social_instagram',
        'type' => 'text',
        'title' => esc_html__('Instagram URL', 'creta'),
        'subtitle' => esc_html__('Please enter in your Instagram URL.', 'creta'),
    )                 
   )
));


    /*
     * <--- END SECTIONS
     */


    