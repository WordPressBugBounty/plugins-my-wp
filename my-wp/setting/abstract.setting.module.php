<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAbstractSettingModule' ) ) :

abstract class MywpAbstractSettingModule {

  static protected $id = '';

  static protected $priority = 10;

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    $priority = static::$priority;

    add_filter( 'mywp_setting_menus' , array( $class , 'mywp_setting_menus' ) , static::$priority );

    add_filter( 'mywp_setting_screens' , array( $class , 'mywp_setting_screens' ) , static::$priority );

    add_action( "mywp_setting_load_setting_screen_{$class::$id}"  , array( $class , 'mywp_current_load_setting_screen' ) );

    add_action( 'mywp_ajax' , array( $class , 'mywp_ajax' ) , static::$priority );

    add_action( 'mywp_ajax_manager' , array( $class , 'mywp_ajax_manager' ) , static::$priority );

    add_action( 'mywp_ajax_network_manager' , array( $class , 'mywp_ajax_network_manager' ) , static::$priority );

    add_action( "mywp_setting_admin_enqueue_scripts_{$class::$id}" , array( $class , 'mywp_current_admin_enqueue_scripts' ) );

    add_action( "mywp_setting_admin_print_styles_{$class::$id}" , array( $class , 'mywp_current_admin_print_styles' ) );

    add_action( "mywp_setting_admin_print_scripts_{$class::$id}" , array( $class , 'mywp_current_admin_print_scripts' ) );

    add_action( "mywp_setting_admin_print_footer_scripts_{$class::$id}" , array( $class , 'mywp_current_admin_print_footer_scripts' ) );

    add_action( "mywp_setting_screen_before_header_{$class::$id}" , array( $class , 'mywp_current_setting_screen_before_header' ) );

    add_action( "mywp_setting_screen_header_{$class::$id}" , array( $class , 'mywp_current_setting_screen_header' ) );

    add_action( "mywp_setting_screen_content_{$class::$id}" , array( $class , 'mywp_current_setting_screen_content' ) );

    add_action( "mywp_setting_screen_footer_{$class::$id}" , array( $class , 'mywp_current_setting_screen_footer' )  );

    add_action( "mywp_setting_screen_advance_header_{$class::$id}" , array( $class , 'mywp_current_setting_screen_advance_header' ) );

    add_action( "mywp_setting_screen_advance_content_{$class::$id}" , array( $class , 'mywp_current_setting_screen_advance_content' ) );

    add_action( "mywp_setting_screen_advance_footer_{$class::$id}" , array( $class , 'mywp_current_setting_screen_advance_footer' ) );

    add_action( "mywp_setting_screen_remove_form_{$class::$id}" , array( $class , 'mywp_current_setting_screen_remove_form' ) );

    add_action( "mywp_setting_screen_after_footer_{$class::$id}" , array( $class , 'mywp_current_setting_screen_after_footer' ) );

    add_filter( "mywp_setting_post_data_format_{$class::$id}_update" , array( $class , 'mywp_current_setting_post_data_format_update' ) , 9 );

    add_filter( "mywp_setting_post_data_format_{$class::$id}_remove" , array( $class , 'mywp_current_setting_post_data_format_remove' ) , 9 );

    add_filter( "mywp_setting_post_data_validate_{$class::$id}_update" , array( $class , 'mywp_current_setting_post_data_validate_update' ) , 9 );

    add_filter( "mywp_setting_post_data_validate_{$class::$id}_remove" , array( $class , 'mywp_current_setting_post_data_validate_remove' ) , 9 );

    add_action( "mywp_setting_before_post_data_action_{$class::$id}_update" , array( $class , 'mywp_current_setting_before_post_data_action_update' ) , 9 );

    add_action( "mywp_setting_before_post_data_action_{$class::$id}_remove" , array( $class , 'mywp_current_setting_before_post_data_action_remove' ) , 9 );

    //add_action( "mywp_setting_post_data_action_custom_{$class::$id}_update" , array( $class , 'mywp_current_setting_post_data_action_custom_update' ) , 9 );

    add_action( "mywp_setting_after_post_data_action_{$class::$id}_update" , array( $class , 'mywp_current_setting_after_post_data_action_update' ) , 9 );

    add_action( "mywp_setting_after_post_data_action_{$class::$id}_remove" , array( $class , 'mywp_current_setting_after_post_data_action_remove' ) , 9 );

    add_filter( "mywp_setting_post_data_action_redirect_{$class::$id}_update" , array( $class , 'mywp_setting_post_data_action_redirect_update' ) , 9 );

    static::after_init();

  }

  protected static function after_init() {}

  public static function mywp_setting_menus( $setting_menus ) {

    return $setting_menus;

  }

  public static function mywp_setting_screens( $setting_screens ) {

    return $setting_screens;

  }

  public static function mywp_ajax() {}

  public static function mywp_ajax_manager() {}

  public static function mywp_ajax_network_manager() {}

  public static function mywp_current_load_setting_screen() {}

  public static function mywp_current_admin_enqueue_scripts() {}

  public static function mywp_current_admin_print_styles() {}

  public static function mywp_current_admin_print_scripts() {}

  public static function mywp_current_admin_print_footer_scripts() {}

  public static function mywp_current_setting_screen_before_header() {}

  public static function mywp_current_setting_screen_header() {}

  public static function mywp_current_setting_screen_content() {}

  public static function mywp_current_setting_screen_footer() {}

  public static function mywp_current_setting_screen_advance_header() {}

  public static function mywp_current_setting_screen_advance_content() {}

  public static function mywp_current_setting_screen_advance_footer() {}

  public static function mywp_current_setting_screen_remove_form() {}

  public static function mywp_current_setting_screen_after_footer() {}

  protected static function get_model() {

    $mywp_model = MywpSetting::get_model( static::$id );

    if( empty( $mywp_model ) ) {

      return false;

    }

    return $mywp_model;

  }

  protected static function get_setting_data() {

    $setting_data = MywpSetting::get_setting_data( static::$id );

    return $setting_data;

  }

  protected static function get_document_url( $url = false ) {

    $plugin_info = MywpApi::plugin_info();

    $document_url = $plugin_info['website_url'] . $url;

    return $document_url;

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_format_remove( $formatted_data ) {

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_validate_update( $validated_data ) {

    return $validated_data;

  }

  public static function mywp_current_setting_post_data_validate_remove( $validated_data ) {

    return $validated_data;

  }

  public static function mywp_current_setting_before_post_data_action_update( $validated_data ) {}

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {}

  //public static function mywp_setting_post_data_action_custom_update( $validated_data ) {}

  public static function mywp_current_setting_after_post_data_action_update( $validated_data ) {}

  public static function mywp_current_setting_after_post_data_action_remove( $validated_data ) {}

  public static function mywp_setting_post_data_action_redirect_update( $is_redirect ) {

    return $is_redirect;

  }

}

endif;
