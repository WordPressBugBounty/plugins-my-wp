<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpControllerAbstractModule' ) ) :

abstract class MywpControllerAbstractModule {

  static protected $id = '';

  static protected $priority = 10;

  static protected $network = false;

  static protected $is_do_controller = false;

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    add_filter( 'mywp_controllers' , array( $class , 'mywp_controllers' ) , static::$priority );

    add_filter( "mywp_controller_initial_data_{$class::$id}" , array( $class , 'mywp_controller_initial_data' ) , 9 );

    add_filter( "mywp_controller_default_data_{$class::$id}" , array( $class , 'mywp_controller_default_data' ) , 9 );

    add_action( 'mywp_wp_loaded' , array( $class , 'mywp_wp_loaded' ) , 1000 );

    static::after_init();

  }

  protected static function after_init() {}

  public static function mywp_controllers( $controllers ) {

    $controllers[ static::$id ] = array(
      'network' => static::$network,
    );

    return $controllers;

  }

  public static function mywp_controller_initial_data( $initial_data ) {

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    return $default_data;

  }

  public static function get_model() {

    $controller = MywpController::get_controller( static::$id );

    if( empty( $controller['model'] ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$controller["model"]' , $called_text );

      return false;

    }

    return $controller["model"];

  }

  public static function get_setting_data() {

    $mywp_model = static::get_model();

    if( empty( $mywp_model ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$mywp_model' , $called_text );

      return false;

    }

    return $mywp_model->get_setting_data();

  }

  protected static function is_do_controller() {

    if( ! empty( static::$is_do_controller ) ) {

      return true;

    }

    $class = get_called_class();

    $is_do_controller = apply_filters( "mywp_controller_is_do_{$class::$id}" , true );
    $is_do_controller = apply_filters( 'mywp_controller_is_do' , $is_do_controller , static::$id );

    return $is_do_controller;

  }

  protected static function is_do_function( $function_name = false ) {

    if( empty( $function_name ) ) {

      return false;

    }

    $class = get_called_class();

    $function_name = strip_tags( $function_name );

    $is_do_function = apply_filters( "mywp_controller_is_do_function_{$class::$id}_{$function_name}" , true );

    return $is_do_function;

  }

  protected static function after_do_function( $function_name = false ) {

    if( empty( $function_name ) ) {

      return false;

    }

    $class = get_called_class();

    $function_name = strip_tags( $function_name );

    do_action( "mywp_controller_after_do_{$class::$id}_{$function_name}" );

  }

  public static function mywp_wp_loaded() {}

}

endif;
