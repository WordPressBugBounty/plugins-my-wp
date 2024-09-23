<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpThirdpartyAbstractModule' ) ) :

abstract class MywpThirdpartyAbstractModule {

  protected static $id;

  protected static $base_name;

  protected static $name;

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    if( empty( static::$base_name ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $base_name"' , $called_text );

      return false;

    }

    if( self::is_current_plugin_activate() ) {

      add_action( 'mywp_init' , array( $class , 'mywp_init' ) );

    }

    add_filter( 'mywp_thirdparties' , array( $class , 'mywp_thirdparties' ) );

    add_filter( 'mywp_thirdparty_pre_plugin_activate_' . static::$base_name , array( $class , 'current_pre_plugin_activate' ) );

    add_filter( 'mywp_debug_renders' , array( $class , 'mywp_debug_renders' ) );

    add_action( 'mywp_debug_render_' . static::$id , array( $class , 'mywp_debug_render' ) );

    add_action( 'mywp_debug_render_footer' , array( $class , 'mywp_debug_render_footer' ) );

    static::after_init();

  }

  protected static function after_init() {}

  public static function mywp_init() {}

  public static function mywp_thirdparties( $thirdparties ) {

    $thirdparties[ static::$id ] = array( 'base_name' => static::$base_name , 'name' => static::$name );

    return $thirdparties;

  }

  public static function current_pre_plugin_activate( $is_plugin_activate ) {

    return $is_plugin_activate;

  }

  protected static function is_current_plugin_activate() {

    return MywpThirdparty::is_plugin_activate( static::$base_name );

  }

  protected static function get_model( $controller_id = false ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$controller_id' );

    if( empty( $controller_id ) ) {

      MywpHelper::error_require_message( '$controller_id' , $called_text );

      return false;

    }

    $controller = MywpController::get_controller( $controller_id );

    if( empty( $controller['model'] ) ) {

      MywpHelper::error_not_found_message( '$controller_id' , $called_text );

      return false;

    }

    return $controller['model'];

  }

  protected static function get_setting_data( $controller_id = false ) {

    $mywp_model = self::get_model( $controller_id );

    if( empty( $mywp_model ) ) {

      return false;

    }

    return $mywp_model->get_setting_data();

  }

  public static function mywp_debug_renders( $debug_renders ) {

    return $debug_renders;

  }

  public static function mywp_debug_render() {}

  public static function mywp_debug_render_footer() {}

}

endif;
