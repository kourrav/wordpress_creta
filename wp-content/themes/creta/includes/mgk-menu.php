<?php

// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'magikCreta_add_custom_fields' );

function magikCreta_add_custom_fields( $menu_item ) {
    $menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
    $menu_item->nolink = get_post_meta( $menu_item->ID, '_menu_item_nolink', true );
    $menu_item->hide = get_post_meta( $menu_item->ID, '_menu_item_hide', true );
    $menu_item->mobile_hide = get_post_meta( $menu_item->ID, '_menu_item_mobile_hide', true );
    $menu_item->cols = get_post_meta( $menu_item->ID, '_menu_item_cols', true );
    $menu_item->popup_type = get_post_meta( $menu_item->ID, '_menu_item_popup_type', true );
    $menu_item->popup_pos = get_post_meta( $menu_item->ID, '_menu_item_popup_pos', true );
    $menu_item->popup_cols = get_post_meta( $menu_item->ID, '_menu_item_popup_cols', true );
    $menu_item->popup_bg_image = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_image', true );
    $menu_item->popup_bg_pos = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_pos', true );
    $menu_item->popup_bg_repeat = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_repeat', true );
    $menu_item->popup_bg_size = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_size', true );
    $menu_item->popup_style = get_post_meta( $menu_item->ID, '_menu_item_popup_style', true );
    $menu_item->tip_label = get_post_meta( $menu_item->ID, '_menu_item_tip_label', true );
    $menu_item->tip_color = get_post_meta( $menu_item->ID, '_menu_item_tip_color', true );
    $menu_item->tip_bg = get_post_meta( $menu_item->ID, '_menu_item_tip_bg', true );
    return $menu_item;
}

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'magikCreta_update_custom_fields', 10, 3 );

function magikCreta_update_custom_fields( $menu_id, $menu_item_db_id, $args ) {
    $check = array('icon', 'nolink', 'hide', 'mobile_hide', 'cols', 'popup_type', 'popup_pos', 'popup_cols', 'popup_bg_image', 'popup_bg_pos', 'popup_bg_repeat', 'popup_bg_size', 'popup_style', 'block', 'tip_label', 'tip_color', 'tip_bg');

    foreach ( $check as $key ) {

        if (!isset($_POST['menu-item-'.$key][$menu_item_db_id])){
            if (!isset($args['menu-item-'.$key]))
                $value = "";
            else
                $value = $args['menu-item-'.$key];
        } else {
            $value = $_POST['menu-item-'.$key][$menu_item_db_id];
        }

        update_post_meta( $menu_item_db_id, '_menu_item_'.$key, $value );
    }
}

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'magikCreta_menu_edit_walker', 10, 2 );

function magikCreta_menu_edit_walker($walker = '', $menu_id = '') {
    return 'magikCreta_Walker_Nav_Menu_Edit';
}

// Create HTML list of nav menu input items.
// Extend from Walker_Nav_Menu class
class magikCreta_Walker_Nav_Menu_Edit extends Walker_Nav_Menu  {
    /**
     * @see Walker_Nav_Menu::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
    }

    /**
     * @see Walker_Nav_Menu::end_lvl()
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {
    }

  
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;

        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $item_id = esc_html( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );
        ob_start();
        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = $original_object->post_title;
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_html( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( '%s (Invalid)', $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( '%s (Pending)', $item->title );
        }

        $title = empty( $item->label ) ? $title : $item->label;

        ?>
    <li id="menu-item-<?php echo esc_html($item_id); ?>" class="<?php echo implode(' ', $classes ); ?>">
    <dl class="menu-item-bar">
        <dt class="menu-item-handle">
            <span class="item-title"><?php echo esc_html( $title ); ?></span>
            <span class="item-controls">
                <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                <span class="item-order hide-if-js">
                    <a href="<?php
                        echo wp_nonce_url(
                            esc_url( add_query_arg(
                                array(
                                    'action' => 'move-up-menu-item',
                                    'menu-item' => $item_id,
                                ),
                                esc_url( remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) )
                            ) ),
                            'move-menu_item'
                        );
                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e( 'Move up','creta');?>">&#8593;</abbr></a>
                    |
                    <a href="<?php
                        echo wp_nonce_url(
                            esc_url( add_query_arg(
                                array(
                                    'action' => 'move-down-menu-item',
                                    'menu-item' => $item_id,
                                ),
                                esc_url( remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) )
                            ) ),
                            'move-menu_item'
                        );
                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down','creta');?>">&#8595;</abbr></a>
                </span>
                <a class="item-edit" id="edit-<?php echo esc_html($item_id); ?>" title="<?php esc_attr_e('Edit Menu Item','creta');?>" href="<?php
                    echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] )
                        ? admin_url( 'nav-menus.php' )
                        : esc_url( add_query_arg( 'edit-menu-item', $item_id,
                            esc_url( remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) ) );
                    ?>"><?php esc_attr_e( 'Edit Menu Item','creta'); ?></a>
            </span>
        </dt>
    </dl>

    <div class="menu-item-settings" id="menu-item-settings-<?php echo esc_html($item_id); ?>">
        <?php if( 'custom' == $item->type ) : ?>
    <p class="description description-wide">
        <label for="edit-menu-item-url-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e('URL','creta'); ?><br />
            <input type="text" id="edit-menu-item-url-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-url"
                <?php if (esc_html( $item->url )) : ?>
                   name="menu-item-url[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-url[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->url ); ?>" />
        </label>
    </p>
        <?php endif; ?>
    <p class="description description-wide">
        <label for="edit-menu-item-title-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Navigation Label','creta'); ?><br />
            <input type="text" id="edit-menu-item-title-<?php echo esc_html($item_id); ?>" class="widefat edit-menu-item-title"
                <?php if (esc_html( $item->title )) : ?>
                   name="menu-item-title[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-title[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->title ); ?>" />
        </label>
    </p>
    <p class="description">
        <label for="edit-menu-item-target-<?php echo esc_html($item_id); ?>">
            <input type="checkbox" id="edit-menu-item-target-<?php echo esc_html($item_id); ?>" value="_blank"
                <?php if ($item->target == '_blank') : ?>
                   name="menu-item-target[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-target[<?php echo esc_html($item_id); ?>]"
                <?php checked( $item->target, '_blank' ); ?> />
            <?php esc_attr_e( 'Open link in a new window/tab','creta'); ?>
        </label>
    </p>
    <?php
    /* New fields insertion starts here */
    ?>
    <p class="description description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Font Awesome Icon Class','creta'); ?><br />
            <input type="text" id="edit-menu-item-icon-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-icon"
                <?php if (esc_html( $item->icon )) : ?>
                    name="menu-item-icon[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-icon[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->icon ); ?>" />
            <span><?php echo esc_html__('Input font awesome icon or icon class. You can see', 'creta').' <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">'.esc_html__('Font Awesome Icons in here', 'creta').'</a>.' ?></span>
        </label>
    </p>
    <p class="description">
        <label for="edit-menu-item-nolink-<?php echo esc_html($item_id); ?>">
            <input type="checkbox" id="edit-menu-item-nolink-<?php echo esc_html($item_id); ?>" class="code edit-menu-item-custom" value="nolink"
                <?php if ($item->nolink == 'nolink') : ?>
                   name="menu-item-nolink[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-nolink[<?php echo esc_html($item_id); ?>]"
                <?php checked( $item->nolink, 'nolink' ); ?> />
            <?php esc_attr_e( "Don't link",'creta'); ?>
        </label>
    </p>
    <p class="description">
        <label for="edit-menu-item-hide-<?php echo esc_html($item_id); ?>">
            <input type="checkbox" id="edit-menu-item-hide-<?php echo esc_html($item_id); ?>" class="code edit-menu-item-custom" value="hide"
                <?php if ($item->hide == 'hide') : ?>
                   name="menu-item-hide[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-hide[<?php echo esc_html($item_id); ?>]"
                <?php checked( $item->hide, 'hide' ); ?> />
            <?php esc_attr_e( "Don't show a link",'creta'); ?>
        </label>
    </p>
    <p class="description">
        <label for="edit-menu-item-mobile_hide-<?php echo esc_html($item_id); ?>">
            <input type="checkbox" id="edit-menu-item-mobile_hide-<?php echo esc_html($item_id); ?>" class="code edit-menu-item-custom" value="hide"
                <?php if ($item->mobile_hide == 'hide') : ?>
                    name="menu-item-mobile_hide[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-mobile_hide[<?php echo esc_html($item_id); ?>]"
                <?php checked( $item->mobile_hide, 'hide' ); ?> />
            <?php esc_attr_e( "Don't show a link on mobile panel",'creta'); ?>
        </label>
    </p>
    <div class="menu-item-level0-<?php echo esc_html($item_id); ?>" style="<?php if ($depth == 0) echo 'display:block;'; else echo 'display:none;' ?>">
        <div style="clear:both;"></div>
        <p class="description description-thin description-thin-custom">
            <label for="edit-menu-item-type-menu-<?php echo esc_html($item_id); ?>">
                <?php esc_attr_e( 'Menu Type','creta'); ?><br />
                <select id="edit-menu-item-type-menu-<?php echo esc_html($item_id); ?>"
                    <?php if (esc_html($item->popup_type)) : ?>
                        name="menu-item-popup_type[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                    data-name="menu-item-popup_type[<?php echo esc_html($item_id); ?>]"
                    >
                    <option value="" <?php if(esc_html($item->popup_type) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e('Narrow','creta'); ?></option>
                    <option value="wide" <?php if(esc_html($item->popup_type) == "wide"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Wide','creta'); ?></option>
                </select>
            </label>
        </p>
        <p class="description description-thin description-thin-custom">
            <label for="edit-menu-item-popup_pos-<?php echo esc_html($item_id); ?>">
                <?php esc_attr_e( 'Popup Position','creta'); ?><br />
                <select id="edit-menu-item-popup_pos-<?php echo esc_html($item_id); ?>"
                    <?php if (esc_html($item->popup_pos)) : ?>
                        name="menu-item-popup_pos[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                    data-name="menu-item-popup_pos[<?php echo esc_html($item_id); ?>]"
                >
                    <option value="pos-left" <?php if(esc_html($item->popup_pos) == "pos-left"){echo 'selected="selected"';} ?>><?php esc_attr_e('Left','creta') ?></option>
                    <option value="pos-right" <?php if(esc_html($item->popup_pos) == "pos-right"){echo 'selected="selected"';} ?>><?php esc_attr_e('Right','creta') ?></option>
                    <option value="" <?php if(esc_html($item->popup_pos) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e('Justify (only for wide)','creta') ?></option>
                    <option value="pos-center" <?php if(esc_html($item->popup_pos) == "pos-center"){echo 'selected="selected"';} ?>><?php esc_attr_e('Center (only for wide)','creta') ?></option>
                </select>
            </label>
        </p>
        <div style="clear:both;"></div>
        <p class="description description-wide">
            <label for="edit-menu-item-popup_cols-<?php echo esc_html($item_id); ?>">
                <br/><?php esc_attr_e( 'Popup Columns (only for wide)','creta'); ?><br />
                <select id="edit-menu-item-popup_cols-<?php echo esc_html($item_id); ?>"
                    <?php if ($item->popup_cols) : ?>
                        name="menu-item-popup_cols[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                        data-name="menu-item-popup_cols[<?php echo esc_html($item_id); ?>]"
                    >
                    <option value="" <?php if(esc_html($item->popup_cols) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Select','creta'); ?></option>
                    <option value="col-2" <?php if(esc_html($item->popup_cols) == "col-2"){echo 'selected="selected"';} ?>><?php esc_attr_e( '2 Columns','creta'); ?></option>
                    <option value="col-3" <?php if(esc_html($item->popup_cols) == "col-3"){echo 'selected="selected"';} ?>><?php esc_attr_e( '3 Columns','creta'); ?></option>
                    <option value="col-4" <?php if(esc_html($item->popup_cols) == "col-4"){echo 'selected="selected"';} ?>><?php esc_attr_e( '4 Columns','creta'); ?></option>
                    <option value="col-5" <?php if(esc_html($item->popup_cols) == "col-5"){echo 'selected="selected"';} ?>><?php esc_attr_e( '5 Columns','creta'); ?></option>
                    <option value="col-6" <?php if(esc_html($item->popup_cols) == "col-6"){echo 'selected="selected"';} ?>><?php esc_attr_e( '6 Columns','creta'); ?></option>
                </select>
            </label>
        </p>
      
        <br/>
    </div>
    <div class="menu-item-level1-<?php echo esc_html($item_id); ?>" style="<?php if ($depth == 1) echo 'display:block;'; else echo 'display:none;' ?>">
        <div style="clear:both;"></div>
        <p class="description description-wide">
            <label for="edit-menu-item-cols-<?php echo esc_html($item_id); ?>">
                <?php esc_attr_e( 'Columns (only for wide)','creta'); ?><br />
                <input type="text" id="edit-menu-item-cols-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-cols"
                    <?php if (esc_html( $item->cols )) : ?>
                        name="menu-item-cols[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                       data-name="menu-item-cols[<?php echo esc_html($item_id); ?>]"
                       value="<?php echo esc_html( $item->cols?$item->cols:1 ); ?>" />
                <span class="description"><?php esc_attr_e('will occupy x columns of parent popup columns','creta'); ?></span>
            </label>
        </p>
    
        <br/>
    </div>
    <div class="menu-item-level2-<?php echo esc_html($item_id); ?>" style="<?php if ($depth == 0 || $depth == 1) echo 'display:block;'; else echo 'display:none;' ?>">
        <p class="description description-wide">
            <label for="edit-menu-item-popup_bg_image-<?php echo esc_html($item_id); ?>">
                <?php esc_attr_e( 'Background Image (only for wide)','creta'); ?><br />
                <input type="text" id="edit-menu-item-popup_bg_image-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-popup_bg_image"
                    <?php if (esc_html( $item->popup_bg_image )) : ?>
                        name="menu-item-popup_bg_image[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                       data-name="menu-item-popup_bg_image[<?php echo esc_html($item_id); ?>]"
                       value="<?php echo esc_html( $item->popup_bg_image ); ?>" />
                <br/>
                <input class="button_upload_image button" id="edit-menu-item-popup_bg_image-<?php echo esc_html($item_id); ?>" type="button" value="<?php esc_attr_e('Upload Image','creta');?>" />&nbsp;
                <input class="button_remove_image button" id="edit-menu-item-popup_bg_image-<?php echo esc_html($item_id); ?>" type="button" value="<?php esc_attr_e('Remove Image','creta');?>" />
            </label>
        </p>
        <p class="description description-wide">
            <label for="edit-menu-item-popup_bg_pos-<?php echo esc_html($item_id); ?>">
                <br/><?php esc_attr_e( 'Background Position (only for wide)','creta'); ?><br />
                <select id="edit-menu-item-popup_bg_pos-<?php echo esc_html($item_id); ?>"
                    <?php if ($item->popup_bg_pos) : ?>
                        name="menu-item-popup_bg_pos[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                        data-name="menu-item-popup_bg_pos[<?php echo esc_html($item_id); ?>]"
                    >
                    <option value="" <?php if(esc_html($item->popup_bg_pos) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e('Select','creta'); ?></option>
                    <option value="left top" <?php if(esc_html($item->popup_bg_pos) == "left top"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Left Top','creta'); ?></option>
                    <option value="left center" <?php if(esc_html($item->popup_bg_pos) == "left center"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Left Center','creta'); ?></option>
                    <option value="left center" <?php if(esc_html($item->popup_bg_pos) == "left center"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Left Center','creta'); ?></option>
                    <option value="left bottom" <?php if(esc_html($item->popup_bg_pos) == "left bottom"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Left Bottom','creta'); ?></option>
                    <option value="center top" <?php if(esc_html($item->popup_bg_pos) == "center top"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Center Top','creta'); ?></option>
                    <option value="center center" <?php if(esc_html($item->popup_bg_pos) == "center center"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Center Center','creta'); ?></option>
                    <option value="center bottom" <?php if(esc_html($item->popup_bg_pos) == "center bottom"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Center Bottom','creta'); ?></option>
                    <option value="right top" <?php if(esc_html($item->popup_bg_pos) == "right top"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Right Top','creta'); ?></option>
                    <option value="right center" <?php if(esc_html($item->popup_bg_pos) == "right center"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Right Center','creta'); ?></option>
                    <option value="right bottom" <?php if(esc_html($item->popup_bg_pos) == "right bottom"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Right Bottom','creta'); ?></option>
                    );
                </select>
            </label>
        </p>
        <p class="description description-wide">
            <label for="edit-menu-item-popup_bg_repeat-<?php echo esc_html($item_id); ?>">
                <br/><?php esc_attr_e( 'Background Repeat (only for wide)','creta'); ?><br />
                <select id="edit-menu-item-popup_bg_repeat-<?php echo esc_html($item_id); ?>"
                    <?php if ($item->popup_bg_repeat) : ?>
                        name="menu-item-popup_bg_repeat[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                        data-name="menu-item-popup_bg_repeat[<?php echo esc_html($item_id); ?>]"
                    >
                    <option value="" <?php if(esc_html($item->popup_bg_repeat) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Select','creta'); ?></option>
                    <option value="no-repeat" <?php if(esc_html($item->popup_bg_repeat) == "no-repeat"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'No Repeat','creta'); ?></option>
                    <option value="repeat" <?php if(esc_html($item->popup_bg_repeat) == "repeat"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Repeat All','creta'); ?></option>
                    <option value="repeat-x" <?php if(esc_html($item->popup_bg_repeat) == "repeat-x"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Repeat Horizontally','creta'); ?></option>
                    <option value="repeat-y" <?php if(esc_html($item->popup_bg_repeat) == "repeat-y"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Repeat Vertically','creta'); ?></option>
                    <option value="inherit" <?php if(esc_html($item->popup_bg_repeat) == "inherit"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Inherit','creta'); ?></option>
                </select>
            </label>
        </p>
        <p class="description description-wide">
            <label for="edit-menu-item-popup_bg_size-<?php echo esc_html($item_id); ?>">
                <br/><?php esc_attr_e( 'Background Size (only for wide)','creta'); ?><br />
                <select id="edit-menu-item-popup_bg_size-<?php echo esc_html($item_id); ?>"
                    <?php if ($item->popup_bg_size) : ?>
                        name="menu-item-popup_bg_size[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                        data-name="menu-item-popup_bg_size[<?php echo esc_html($item_id); ?>]"
                    >
                    <option value="" <?php if(esc_html($item->popup_bg_size) == ""){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Select','creta'); ?></option>
                    <option value="inherit" <?php if(esc_html($item->popup_bg_size) == "inherit"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Inherit','creta'); ?></option>
                    <option value="cover" <?php if(esc_html($item->popup_bg_size) == "cover"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Cover','creta'); ?></option>
                    <option value="contain" <?php if(esc_html($item->popup_bg_size) == "contain"){echo 'selected="selected"';} ?>><?php esc_attr_e( 'Contain','creta'); ?></option>
                </select>
            </label>
        </p>
        <p class="description description-wide">
            <label for="edit-menu-item-popup_style-<?php echo esc_html($item_id); ?>">
                <?php esc_attr_e( 'Custom Styles (only for wide)','creta'); ?><br />
                <textarea id="edit-menu-item-popup_style-<?php echo esc_html($item_id); ?>" class="widefat edit-menu-item-popup_style" rows="3" cols="20"
                    <?php if (esc_html( $item->popup_style )) : ?>
                        name="menu-item-popup_style[<?php echo esc_html($item_id); ?>]"
                    <?php endif; ?>
                          data-name="menu-item-popup_style[<?php echo esc_html($item_id); ?>]"
                    ><?php echo esc_html( $item->popup_style ); // textarea_escaped ?></textarea>
            </label>
        </p>
        <br/>
    </div>
    <p class="description description-thin">
        <label for="edit-menu-item-tip_label-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Tip Label','creta'); ?><br />
            <input type="text" id="edit-menu-item-tip_label-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-tip_label"
                <?php if (esc_html( $item->tip_label )) : ?>
                    name="menu-item-tip_label[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-tip_label[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->tip_label ); ?>" />
        </label>
    </p>
    <p class="description description-thin">
        <label for="edit-menu-item-tip_color-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Tip Text Color','creta'); ?><br />
            <input type="text" id="edit-menu-item-tip_color-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-tip_color"
                <?php if (esc_html( $item->tip_color )) : ?>
                    name="menu-item-tip_color[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-tip_color[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->tip_color ); ?>" />
        </label>
    </p>
    <p class="description description-thin">
        <label for="edit-menu-item-tip_bg-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Tip BG Color','creta'); ?><br />
            <input type="text" id="edit-menu-item-tip_bg-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-tip_bg"
                <?php if (esc_html( $item->tip_bg )) : ?>
                    name="menu-item-tip_bg[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-tip_bg[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->tip_bg ); ?>" />
        </label>
    </p><br/>
    <?php
    /* New fields insertion ends here */
    ?><div style="clear:both;"></div><br/>
    <p class="description description-wide">
        <label for="edit-menu-item-attr-title-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Title Attribute','creta'); ?><br />
            <input type="text" id="edit-menu-item-attr-title-<?php echo esc_html($item_id); ?>" class="widefat edit-menu-item-attr-title"
                <?php if (esc_html( $item->post_excerpt )) : ?>
                   name="menu-item-attr-title[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-attr-title[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->post_excerpt ); ?>" />
        </label>
    </p>
    <p class="description description-thin">
        <label for="edit-menu-item-classes-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'CSS Classes (optional)','creta'); ?><br />
            <input type="text" id="edit-menu-item-classes-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-classes"
                <?php if (esc_html( implode(' ', $item->classes ) )) : ?>
                   name="menu-item-classes[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-classes[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( implode(' ', $item->classes ) ); ?>" />
        </label>
    </p>
    <p class="description description-thin">
        <label for="edit-menu-item-xfn-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Link Relationship (XFN)','creta'); ?><br />
            <input type="text" id="edit-menu-item-xfn-<?php echo esc_html($item_id); ?>" class="widefat code edit-menu-item-xfn"
                <?php if (esc_html( $item->xfn )) : ?>
                   name="menu-item-xfn[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                   data-name="menu-item-xfn[<?php echo esc_html($item_id); ?>]"
                   value="<?php echo esc_html( $item->xfn ); ?>" />
        </label>
    </p>
    <p class="description description-wide">
        <label for="edit-menu-item-description-<?php echo esc_html($item_id); ?>">
            <?php esc_attr_e( 'Description','creta'); ?><br />
            <textarea id="edit-menu-item-description-<?php echo esc_html($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20"
                <?php if (esc_html( $item->description )) : ?>
                      name="menu-item-description[<?php echo esc_html($item_id); ?>]"
                <?php endif; ?>
                      data-name="menu-item-description[<?php echo esc_html($item_id); ?>]"
                    ><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
            <span class="description"><?php esc_attr_e( 'The description will be displayed in the menu if the current theme supports it.','creta');  ?></span>
        </label>
    </p>
    <div class="menu-item-actions description-wide submitbox">
        <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
        <p class="link-to-original">
            <?php printf( 'Original: %s', '<a href="' . esc_url( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
        </p>
        <?php endif; ?>
        <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_html($item_id); ?>" href="<?php
            echo wp_nonce_url(
                esc_url( add_query_arg(
                    array(
                        'action' => 'delete-menu-item',
                        'menu-item' => $item_id,
                    ),
                    esc_url( remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) )
                ) ),
                'delete-menu_item_' . $item_id
            ); ?>"><?php esc_attr_e( 'Remove','creta'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo esc_html($item_id); ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), esc_url( remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) ) );
        ?>#menu-item-settings-<?php echo esc_html($item_id); ?>"><?php esc_attr_e( 'Cancel','creta'); ?></a>
    </div>

    <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html($item_id); ?>" />
    <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html( $item->object_id ); ?>" />
    <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html( $item->object ); ?>" />
    <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html( $item->menu_item_parent ); ?>" />
    <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html( $item->menu_order ); ?>" />
    <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_html($item_id); ?>]" value="<?php echo esc_html( $item->type ); ?>" />
    </div><!-- .menu-item-settings-->
    <ul class="menu-item-transport"></ul>
    </li>
    <?php
        $output .= ob_get_clean();
    }
}

/* Top Navigation Menu */
if (!class_exists('MagikCreta_top_navwalker')) {
    class MagikCreta_top_navwalker extends Walker_Nav_Menu {

        // add classes to ul sub menus
        function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
            $id_field = $this->db_fields['id'];
            if ( is_object( $args[0] ) ) {
                $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
            }
            return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
        }

        // add popup class to ul sub-menus
        function start_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);

            if ( $depth == 0 ) {
                $out_div = '<div class="mgk-popup"><div class="inner" style="'.$args->popup_style.'">';
            } else {
                $out_div = '';
            }
            $output .= "\n$indent$out_div<ul class=\"sub-menu\">\n";
        }

        function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            if ( $depth == 0 ) {
                $out_div = '</div></div>';
            } else {
                $out_div = '';
            }
            $output .= "$indent</ul>$out_div\n";
        }

        // add main/sub classes to li's and links
        function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            global $wp_query;

            $sub = "";
            $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
            if ( $depth == 0 && $args->has_children )
                $sub = ' has-sub';

            if ( $depth == 1 && $args->has_children )
                $sub = ' sub';

            $active = "";

            // depth dependent classes
           
             if ( $item->current)
                $active = 'active';

            // passed classes
            $classes = empty( $item->classes ) ? array() : (array)$item->classes;

            $class_names = esc_html( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

            // menu type, type, column class, popup style
            $menu_type = "";
            $popup_pos = "";
            $popup_cols = "";
            $popup_style = "";
            $cols = 1;

            if ($depth == 0) {
                if ($item->popup_type == "wide") {
                    $menu_type = " wide";
                    if ($item->popup_cols == "")
                        $item->popup_cols = 'col-4';
                    $popup_cols = " ". $item->popup_cols;

                    $popup_bg_image = $item->popup_bg_image ? 'background-image:url('.str_replace(array('http://', 'https://'), array('//', '//'), $item->popup_bg_image).');' : '';
                    $popup_bg_pos = $item->popup_bg_pos ? ';background-position:'.$item->popup_bg_pos.';' : '';
                    $popup_bg_repeat = $item->popup_bg_repeat ? ';background-repeat:'.$item->popup_bg_repeat.';' : '';
                    $popup_bg_size = $item->popup_bg_size ? ';background-size:'.$item->popup_bg_size.';' : '';
                   

                    $popup_style = str_replace('"', '\'', $item->popup_style . $popup_bg_image . $popup_bg_pos . $popup_bg_repeat . $popup_bg_size );
                } else {
                    $menu_type = " narrow";
                }
                $popup_pos = " ". $item->popup_pos;
            }

            // build html
            if ($depth == 1) {
                $sub_popup_style = '';
                if ($item->popup_style || $item->popup_bg_image || $item->popup_bg_pos || $item->popup_bg_repeat || $item->popup_bg_size) {
                    $sub_popup_image = $item->popup_bg_image ? 'background-image:url('.str_replace(array('http://', 'https://'), array('//', '//'), $item->popup_bg_image).');' : '';
                    $sub_popup_pos = $item->popup_bg_pos ? ';background-position:'.$item->popup_bg_pos.';' : '';;
                    $sub_popup_repeat = $item->popup_bg_repeat ? ';background-repeat:'.$item->popup_bg_repeat.';' : '';;
                    $sub_popup_size = $item->popup_bg_size ? ';background-size:'.$item->popup_bg_size.';' : '';;
                    $sub_popup_style = ' style="'.str_replace('"', '\'', $item->popup_style).$sub_popup_image.$sub_popup_pos.$sub_popup_repeat.$sub_popup_size.'"';
                    $class_names .=' imgitem';
                }
                if ($item->cols > 1) {
                    $cols = (int)$item->cols;
                }
              
                $output .= $indent . '<li id="nav-menu-item-'. esc_html($item->ID) . '" class="' . $class_names . ' ' . $active . $sub . $menu_type . $popup_pos . $popup_cols . '" data-cols="'.$cols.'"'.$sub_popup_style.'>';
            } else {
                $output .= $indent . '<li id="nav-menu-item-'. esc_html($item->ID) . '" class="' . $class_names . ' ' . $active . $sub . $menu_type . $popup_pos . $popup_cols . '">';
            }

            $current_a = "";

            // link attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_html( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_html( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_html( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url        ) .'"' : '';

            if ( ( $item->current && $depth == 0 ) ||  ( $item->current_item_ancestor && $depth == 0 ) )
                $current_a .= ' current ';

            $attributes .= ' class="'. $current_a . '"';
            $item_output = $args->before;
            if ( $item->hide == "" ) {
                if ( $item->nolink == "" ) {
                    $item_output .= '<a'. $attributes .'><span>';
                } else{
                    $item_output .= '<h5>';
                }
                $item_output .= $args->link_before . ($item->icon ? '<i class="fa fa-' . str_replace('fa-', '', $item->icon) . '"></i>' : '') . apply_filters( 'the_title', $item->title, $item->ID );
                $item_output .= $args->link_after;
                if ($item->tip_label) {
                    $item_style = '';
                    $item_arrow_style = '';
                    if ($item->tip_color) {
                        $item_style .= 'color:'.$item->tip_color.';';
                    }
                    if ($item->tip_bg) {
                        $item_style .= 'background:'.$item->tip_bg.';';
                        $item_arrow_style .= 'color:'.$item->tip_bg.';';
                    }
                    $item_output .= '<span class="tip" style="'.$item_style.'"><span class="tip-arrow" style="'.$item_arrow_style.'"></span>'.$item->tip_label.'</span>';
                }
                if ( $item->nolink == "" ) {
                    $item_output .= '</span></a>';
                } else {
                    $item_output .= '</h5>';
                }
            }
          
            $item_output .= $args->after;
            $args->popup_style = $popup_style;

            // build html
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }
}

/* Mobile Menu */
if (!class_exists('MagikCreta_mobile_navwalker')) {
    class MagikCreta_mobile_navwalker extends Walker_Nav_Menu {

        // add classes to ul sub menus
        function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
            $id_field = $this->db_fields['id'];
            if ( is_object( $args[0] ) ) {
                $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
            }
            return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
        }

        // add main/sub classes to li's and links
        function start_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "\n$indent<span class=\"arrow\"></span><ul class=\"sub-menu\">\n";
        }

        function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        // add main/sub classes to li's and links
        function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

            global $wp_query;

            $sub = "";
            $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
            if ( ( $depth >= 0 && $args->has_children ) || ( $depth >= 0 && $item->recentpost != "" ) )
                $sub = ' has-sub';

            $active = "";

            if ( $item->current || $item->current_item_ancestor || $item->current_item_parent )
                $active = 'active';

            // passed classes
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;

            $class_names = esc_html( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

            // build html
            $output .= $indent . '<li id="accordion-menu-item-'. esc_html($item->ID) . '" class="' . $class_names . ' ' . $active . $sub .'">';

            $current_a = "";

            // link attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_html( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_html( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_html( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url        ) .'"' : '';
            if ( ( $item->current && $depth == 0 ) || ( $item->current_item_ancestor && $depth == 0 ) )
                $current_a .= ' current ';

            $attributes .= ' class="'. $current_a . '"';
            $item_output = $args->before;

            if ( $item->hide == "" && $item->mobile_hide == "" ) {
                if ( $item->nolink == "" ) {
                    $item_output .= '<a'. $attributes .'>';
                } else {
                    $item_output .= '<h5>';
                }
                $item_output .= $args->link_before . ($item->icon ? '<i class="fa fa-' . str_replace('fa-', '', $item->icon) . '"></i>' : '') . apply_filters( 'the_title', $item->title, $item->ID );
                $item_output .= $args->link_after;
                if ($item->tip_label) {
                    $item_style = '';
                    $item_arrow_style = '';
                    if ($item->tip_color) {
                        $item_style .= 'color:'.$item->tip_color.';';
                    }
                    if ($item->tip_bg) {
                        $item_style .= 'background:'.$item->tip_bg.';';
                        $item_arrow_style .= 'color:'.$item->tip_bg.';';
                    }
                    $item_output .= '<span class="tip" style="'.$item_style.'"><span class="tip-arrow" style="'.$item_arrow_style.'"></span>'.$item->tip_label.'</span>';
                }
                if ( $item->nolink == "" ) {
                    $item_output .= '</a>';
                } else {
                    $item_output .= '</h5>';
                }
            }
            $item_output .= $args->after;

            
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }
}



