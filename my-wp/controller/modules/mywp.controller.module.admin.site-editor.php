<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminSiteEditor' ) ) :

final class MywpControllerModuleAdminSiteEditor extends MywpControllerAbstractModule {

  static protected $id = 'admin_site_editor';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['top_left_icon'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['top_left_icon'] = false;

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

    add_action( 'load-site-editor.php' , array( __CLASS__ , 'load_site_editor' ) , 1000 );

  }

  public static function load_site_editor() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'top_left_icon' ) );

  }

  public static function top_left_icon() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['top_left_icon'] ) ) {

      return false;

    }

    ?>

    <style>
    #site-editor .edit-site-layout__view-mode-toggle.components-button:before {
      font: normal 32px/1 dashicons;
      content: "\f341";
      display: inline-block;
      top: 14px;
      left: 12px;
    }
    #site-editor .edit-site-layout__view-mode-toggle.components-button svg {
      display: none;
    }
    #site-editor .edit-site-layout__view-mode-toggle.components-button .edit-site-layout__view-mode-toggle-icon {
      display: none;
    }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminSiteEditor::init();

endif;
