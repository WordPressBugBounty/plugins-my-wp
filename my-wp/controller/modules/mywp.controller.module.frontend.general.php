<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleFrontendGeneral' ) ) :

final class MywpControllerModuleFrontendGeneral extends MywpControllerAbstractModule {

  static protected $id = 'frontend_general';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['admin_bar'] = '';

    $initial_data['hide_rest_link_header'] = '';
    $initial_data['hide_shortlink_header'] = '';
    $initial_data['hide_x_pingback'] = '';
    $initial_data['x_frame_option'] = '';

    $initial_data['hide_wp_generator'] = '';
    $initial_data['hide_wlwmanifest_link'] = '';
    $initial_data['hide_rsd_link'] = '';
    $initial_data['hide_feed_links'] = '';
    $initial_data['hide_feed_links_extra'] = '';
    $initial_data['hide_rest_link'] = '';
    $initial_data['hide_shortlink'] = '';
    $initial_data['include_css_file'] = '';
    $initial_data['include_js_file'] = '';
    $initial_data['custom_header_meta'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['admin_bar'] = '';

    $default_data['hide_rest_link_header'] = false;
    $default_data['hide_shortlink_header'] = false;
    $default_data['hide_x_pingback'] = false;
    $default_data['x_frame_option'] = '';

    $default_data['hide_wp_generator'] = false;
    $default_data['hide_wlwmanifest_link'] = false;
    $default_data['hide_rsd_link'] = false;
    $default_data['hide_feed_links'] = false;
    $default_data['hide_feed_links_extra'] = false;
    $default_data['hide_rest_link'] = '';
    $default_data['hide_shortlink'] = '';
    $default_data['include_css_file'] = '';
    $default_data['include_js_file'] = '';
    $default_data['custom_header_meta'] = '';

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( is_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'send_headers' , array( __CLASS__ , 'send_headers' ) );

    add_filter( 'show_admin_bar' , array( __CLASS__ , 'is_show_admin_bar' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_rest_link_header' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_shortlink_header' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_wp_generator' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_wlwmanifest_link' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_rsd_link' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_feed_links' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_feed_links_extra' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_rest_link' ) );

    add_action( 'wp' , array( __CLASS__ , 'hide_shortlink' ) );

    add_filter( 'wp_headers' , array( __CLASS__ , 'wp_headers' ) );

    add_action( 'wp_head' , array( __CLASS__ , 'wp_head' ) );

    add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'wp_enqueue_scripts' ) );

  }

  public static function send_headers() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['x_frame_option'] ) ) {

      return false;

    }

    header( sprintf( 'X-Frame-Options: %s' , $setting_data['x_frame_option']) );

    self::after_do_function( __FUNCTION__ );

  }

  public static function is_show_admin_bar( $show_admin_bar ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $show_admin_bar;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['admin_bar'] ) ) {

      return $show_admin_bar;

    }

    if( $setting_data['admin_bar'] === 'hide' ) {

      $show_admin_bar = false;

    } elseif( $setting_data['admin_bar'] === 'show' ) {

      $show_admin_bar = true;

    }

    self::after_do_function( __FUNCTION__ );

    return $show_admin_bar;

  }

  public static function hide_rest_link_header() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_rest_link_header'] ) ) {

      return false;

    }

    remove_action( 'template_redirect' , 'rest_output_link_header' , 11 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_shortlink_header() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_shortlink_header'] ) ) {

      return false;

    }

    remove_action( 'template_redirect' , 'wp_shortlink_header' , 11 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function wp_headers( $headers ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $headers;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_x_pingback'] ) ) {

      return $headers;

    }

    if( isset( $headers['X-Pingback'] ) ) {

      unset( $headers['X-Pingback'] );

    }

    self::after_do_function( __FUNCTION__ );

    return $headers;

  }

  public static function hide_wp_generator() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_wp_generator'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'wp_generator' );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_wlwmanifest_link() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_wlwmanifest_link'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'wlwmanifest_link' );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_rsd_link() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_rsd_link'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'rsd_link' );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_feed_links() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_feed_links'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'feed_links' , 2 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_feed_links_extra() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_feed_links_extra'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'feed_links_extra' , 3 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_rest_link() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_rest_link'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'rest_output_link_wp_head' , 10 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_shortlink() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_shortlink'] ) ) {

      return false;

    }

    remove_action( 'wp_head' , 'wp_shortlink_wp_head' , 10 );

    self::after_do_function( __FUNCTION__ );

  }

  public static function wp_head() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['custom_header_meta'] ) ) {

      return false;

    }

    $custom_header_meta = do_shortcode( $setting_data['custom_header_meta'] );

    echo $custom_header_meta;

    self::after_do_function( __FUNCTION__ );

  }

  public static function wp_enqueue_scripts() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['include_js_file'] ) && empty( $setting_data['include_css_file'] ) ) {

      return false;

    }

    $include_js_file = do_shortcode( $setting_data['include_js_file'] );
    $include_css_file = do_shortcode( $setting_data['include_css_file'] );

    if( ! empty( $include_js_file ) ) {

      wp_enqueue_script( 'mywp_frontend_include' , $include_js_file , array( 'jquery' ) , MYWP_VERSION , true );

    }

    if( ! empty( $include_css_file ) ) {

      wp_enqueue_style( 'mywp_frontend_include' , $include_css_file , array() , MYWP_VERSION );

    }


    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleFrontendGeneral::init();

endif;
