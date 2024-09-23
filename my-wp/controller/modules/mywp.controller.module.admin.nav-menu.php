<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminNavMenu' ) ) :

final class MywpControllerModuleAdminNavMenu extends MywpControllerAbstractModule {

  static protected $id = 'admin_nav_menu';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['hide_add_new_menu'] = '';
    $initial_data['hide_delete_menu'] = '';
    $initial_data['hide_manage_locations'] = '';
    $initial_data['hide_menu_settings'] = '';
    $initial_data['hide_live_preview_button'] = '';

    $initial_data['remove_meta_boxes_items'] = array();

    $initial_data['hide_link_target'] = '';
    $initial_data['hide_title_attribute'] = '';
    $initial_data['hide_css_classes'] = '';
    $initial_data['hide_xfn'] = '';
    $initial_data['hide_description'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['hide_add_new_menu'] = false;
    $default_data['hide_delete_menu'] = false;
    $default_data['hide_manage_locations'] = false;
    $default_data['hide_menu_settings'] = false;
    $default_data['hide_live_preview_button'] = false;

    $default_data['remove_meta_boxes_items'] = array();

    $default_data['hide_link_target'] = false;
    $default_data['hide_title_attribute'] = false;
    $default_data['hide_css_classes'] = false;
    $default_data['hide_xfn'] = false;
    $default_data['hide_description'] = false;

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'load-nav-menus.php' , array( __CLASS__ , 'load_nav_menus' ) , 1000 );

  }

  public static function load_nav_menus() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_menus' ) );
    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_advanced_menu_properties' ) );
    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_delete_menu' ) );
    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_manage_locations' ) );
    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_menu_settings' ) );
    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_live_preview_button' ) );
    add_action( 'admin_head-nav-menus.php' , array( __CLASS__ , 'remove_menu_meta_boxes_items' ) );

  }

  public static function hide_menus() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_add_new_menu'] ) ) {

      return false;

    }

    echo '<style>';

    $nav_menus = wp_get_nav_menus();

    if( count( $nav_menus ) > 1 ) {

      echo 'body.wp-admin .wrap > .manage-menus .add-new-menu-action, .locations-row-links .locations-add-menu-link { display: none; }';

    } else {

      echo 'body.wp-admin .wrap > .manage-menus, .locations-row-links .locations-add-menu-link { display: none; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_advanced_menu_properties() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data ) ) {

      return false;

    }

    echo '<style>';

    if(
      ! empty( $setting_data['hide_link_target'] )
      && ! empty( $setting_data['hide_title_attribute'] )
      && ! empty( $setting_data['hide_css_classes'] )
      && ! empty( $setting_data['hide_xfn'] )
      && ! empty( $setting_data['hide_description'] )
     ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) { display: none; }';

    }

    if( ! empty( $setting_data['hide_link_target'] ) ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) label:nth-of-type(1) { display: none; }';
      echo 'body.wp-admin #menu-to-edit .menu-item-settings p.field-link-target { display: none; }';

    }

    if( ! empty( $setting_data['hide_title_attribute'] ) ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) label:nth-of-type(2) { display: none; }';
      echo 'body.wp-admin #menu-to-edit .menu-item-settings p.field-title-attribute { display: none; }';

    }

    if( ! empty( $setting_data['hide_css_classes'] ) ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) label:nth-of-type(3) { display: none; }';
      echo 'body.wp-admin #menu-to-edit .menu-item-settings p.field-css-classes { display: none; }';

    }

    if( ! empty( $setting_data['hide_xfn'] ) ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) label:nth-of-type(4) { display: none; }';
      echo 'body.wp-admin #menu-to-edit .menu-item-settings p.field-xfn { display: none; }';

    }

    if( ! empty( $setting_data['hide_description'] ) ) {

      echo 'body.wp-admin #screen-meta #adv-settings .metabox-prefs:nth-child(2) label:nth-of-type(5) { display: none; }';
      echo 'body.wp-admin #menu-to-edit .menu-item-settings p.field-description { display: none; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_delete_menu() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_delete_menu'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .major-publishing-actions .delete-action { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_manage_locations() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_manage_locations'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin h2.nav-tab-wrapper a:nth-child(2) { display: none; }
    body.wp-admin .nav-tab-wrapper a:nth-child(2) { display: none; }
    </style>
    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_menu_settings() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_menu_settings'] ) ) {

      return false;

    }

    ?>
    <style>
    body.wp-admin .menu-settings { display: none; }
    </style>
    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_live_preview_button() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_live_preview_button'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .wrap h1 a { display: none; }
    body.wp-admin .wrap .page-title-action { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function remove_menu_meta_boxes_items() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['remove_meta_boxes_items'] ) ) {

      return false;

    }

    foreach( $setting_data['remove_meta_boxes_items'] as $meta_box_id => $v ) {

      $context = self::get_meta_box_context( $meta_box_id );

      if( empty( $context ) ) {

        continue;

      }

      remove_meta_box( $meta_box_id , 'nav-menus' , $context );

    }

    self::after_do_function( __FUNCTION__ );

  }

  private static function get_meta_box_context( $find_meta_box_id ) {

    global $wp_meta_boxes;

    if( empty( $wp_meta_boxes['nav-menus'] ) ) {

      return false;

    }

    $nav_menu_meta_boxes = $wp_meta_boxes['nav-menus'];

    $meta_box_context = false;

    foreach( $nav_menu_meta_boxes as $context => $priority_meta_boxes ) {

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( $find_meta_box_id !== $meta_box_id ) {

            continue;

          }

          $meta_box_context = $context;

          break;

        }

      }

    }

    return $meta_box_context;

  }

}

MywpControllerModuleAdminNavMenu::init();

endif;
