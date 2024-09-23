<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminUserEdit' ) ) :

final class MywpControllerModuleAdminUserEdit extends MywpControllerAbstractModule {

  static protected $id = 'admin_user_edit';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['hide_rich_editing'] = '';
    $initial_data['hide_syntax_highlighting'] = '';
    $initial_data['hide_admin_color'] = '';
    $initial_data['hide_comment_shortcuts'] = '';
    $initial_data['hide_toolbar'] = '';
    $initial_data['hide_language'] = '';
    $initial_data['hide_url'] = '';
    $initial_data['hide_description'] = '';
    $initial_data['hide_picture'] = '';
    $initial_data['hide_session'] = '';
    $initial_data['hide_application_passwords'] = '';

    $initial_data['hide_contact_fields'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['hide_rich_editing'] = false;
    $default_data['hide_syntax_highlighting'] = false;
    $default_data['hide_admin_color'] = false;
    $default_data['hide_comment_shortcuts'] = false;
    $default_data['hide_toolbar'] = false;
    $default_data['hide_language'] = false;
    $default_data['hide_url'] = false;
    $default_data['hide_description'] = false;
    $default_data['hide_picture'] = false;
    $default_data['hide_session'] = false;
    $default_data['hide_application_passwords'] = false;

    $default_data['hide_contact_fields'] = array();

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

    add_action( 'load-profile.php' , array( __CLASS__ , 'load_user_edit' ) , 1000 );
    add_action( 'load-user-edit.php' , array( __CLASS__ , 'load_user_edit' ) , 1000 );

  }

  public static function load_user_edit() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_rich_editing' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_syntax_highlighting' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_admin_color' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_comment_shortcuts' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_toolbar' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_language' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_url' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_description' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_picture' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_session' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_application_passwords' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_contact_fields' ) );

  }

  public static function hide_rich_editing() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_rich_editing'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-rich-editing-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_syntax_highlighting() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_syntax_highlighting'] ) ) {

      return false;

    }

    ?>
    <style>
    body.wp-admin .user-syntax-highlighting-wrap { display: none; }
    </style>
    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_admin_color() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_admin_color'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-admin-color-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_comment_shortcuts() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_comment_shortcuts'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-comment-shortcuts-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_toolbar() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_toolbar'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-admin-bar-front-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_language() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_language'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-language-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_url() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_url'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-url-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_description() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_description'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-description-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_picture() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_picture'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-profile-picture { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_session() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_session'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .user-sessions-wrap { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_application_passwords() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_application_passwords'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .application-passwords { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_contact_fields() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_contact_fields'] ) ) {

      return false;

    }

    echo '<style>';

    foreach( $setting_data['hide_contact_fields'] as $field_name => $v ) {

      $field_name = strip_tags( $field_name );

      echo "body.wp-admin .user-{$field_name}-wrap { display: none; }";

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminUserEdit::init();

endif;
