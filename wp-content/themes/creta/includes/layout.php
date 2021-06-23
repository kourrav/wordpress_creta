<?php

if(!function_exists('magikCreta_top_navigation'))
{
function magikCreta_top_navigation() {
global $creta_Options;
   
    $html = '';

    if (isset($creta_Options['login_button_pos']) && $creta_Options['login_button_pos'] == 'toplinks') {

        if (is_user_logged_in()) {
            $logout_link = '';
          if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'creta') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '"> ' . esc_html__('Login', 'creta') . '</a></li>';
            if ($register_link) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'creta') . '</a></li>';
            }
        }
    }
    if(isset($creta_Options['show_menu_arrow']) && $creta_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu( 'toplinks' ) ) :
    
        wp_nav_menu(array(
            'theme_location' => 'toplinks',
            'container' => '',
            'menu_class' => 'top-links1 mega-menu1' .$mcls,
            'before' => '',
            'after' => '',          
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new MagikCreta_top_navwalker
        ));
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) { 
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}
}


if(!function_exists('magikCreta_mobile_top_navigation'))
{
function magikCreta_mobile_top_navigation() {
global $creta_Options;
   
    $html = '';

    if (isset($creta_Options['login_button_pos']) && $creta_Options['login_button_pos'] == 'toplinks') {

        if (is_user_logged_in()) {
            $logout_link = '';
          if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'creta') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                 $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '">' . esc_html__('Login', 'creta') . '</a></li>';
            if ($register_link) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'creta') . '</a></li>';
            }
        }
    }
   if(isset($creta_Options['show_menu_arrow']) && $creta_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu( 'toplinks' ) ) :
        wp_nav_menu(array(
            'theme_location' => 'toplinks',
            'container' => '',
            'menu_class' => 'top-links1 accordion-menu' . $mcls,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new MagikCreta_mobile_navwalker
        ));
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) {
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}
}


if(!function_exists('magikCreta_main_menu'))
{
function magikCreta_main_menu() {
    global $creta_Options;
   
    $html = '';

    if (isset($creta_Options['login_button_pos']) && $creta_Options['login_button_pos'] == 'main_menu') {

        if (is_user_logged_in()) {
            $logout_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }

           
                $html .= '<li class="menu-item "><a href="' . esc_url($logout_link) . '"><span>' . esc_html__('Logout', 'creta') . '</span></a></li>';         
        } 
        else {
            $login_link = $register_link = '';
            if (class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
         
                if ($active_signup != 'none')
                     $register_link = wp_registration_url( get_home_url() );
            }
           
                if ($register_link) {
                    $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '"><span>' . esc_html__('Register', 'creta') . '</span></a></li>';
                }
                $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '"><span>' . esc_html__('Login', 'creta') . '</span></a></li>';
           
        }
    }
    if(isset($creta_Options['show_menu_arrow']) && $creta_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu('main_menu') ) :     
        $args = array(
        'container' => '',
        'menu_class' => 'main-menu mega-menu' . $mcls,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new MagikCreta_top_navwalker
        );
       
            $args['theme_location'] = 'main_menu';
        
        wp_nav_menu($args);
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) {

        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}
}


if(!function_exists('magikCreta_mobile_menu'))
{
function magikCreta_mobile_menu() {
    global $creta_Options;

    $html = '';
    if (isset($creta_Options['login_button_pos']) && $creta_Options['login_button_pos'] == 'main_menu') {
        if (is_user_logged_in()) {
            $logout_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
              $logout_link = wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'creta') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '">' . esc_html__('Login', 'creta') . '</a></li>';
            if ($register_link ) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'creta') . '</a></li>';
            }
        }
    }

   
    ob_start();
    if ( has_nav_menu( 'main_menu' ) ) :
      
        $args = array(
            'container' => '',
            'menu_class' => 'mobile-menu accordion-menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new MagikCreta_mobile_navwalker
        );
      
            $args['theme_location'] = 'main_menu';
        
        wp_nav_menu($args);
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());


    if ($output && $html) {
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}
}


?>