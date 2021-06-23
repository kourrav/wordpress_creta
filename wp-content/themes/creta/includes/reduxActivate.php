<?php 
/* Active Redux if found */

if ( !class_exists( 'ReduxFramework' ) && file_exists(get_template_directory()  . '/includes/ReduxFramework/ReduxCore/framework.php') ) {
    require_once( get_template_directory()  . '/includes/ReduxFramework/ReduxCore/framework.php' );
}

?>