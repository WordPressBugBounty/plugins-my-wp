<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpController' ) ) :

final class MywpController {

  private static $controllers;

  public static function get_controllers() {

    if( self::$controllers ) {

      return self::$controllers;

    }

    $pre_controllers = apply_filters( 'mywp_controllers' , array() );

    if( empty( $pre_controllers ) ) {

      return false;

    }

    $default = array(
      'id' => '',
      'initial_data' => array(),
      'default_data' => array(),
      'model' => '',
      'network' => false,
    );

    $controllers = array();

    foreach( $pre_controllers as $controller_id => $controller ) {

      $controller = wp_parse_args( $controller , $default );

      $controller['id'] = $controller_id;
      $controller['initial_data'] = self::get_initial_data( $controller['initial_data'] , $controller_id );
      $controller['default_data'] = self::get_default_data( $controller['default_data'] , $controller_id );
      $controller['model'] = self::get_model( $controller , $controller_id );

      $controllers[ $controller_id ] = $controller;

    }

    return $controllers;

  }

  public static function get_controller( $controller_id = false ) {

    if( empty( $controller_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$controller_id' );

      MywpHelper::error_require_message( '$controller_id' , $called_text );

      return false;

    }

    $controllers = self::get_controllers();

    if( empty( $controllers[ $controller_id ] ) ) {

      return false;

    }

    return $controllers[ $controller_id ];

  }

  public static function set_controllers() {

    self::$controllers = self::get_controllers();

  }

  private static function get_initial_data( $initial_data , $controller_id ) {

    $initial_data = apply_filters( "mywp_controller_initial_data_{$controller_id}" , $initial_data );
    $initial_data = apply_filters( 'mywp_controller_initial_data' , $initial_data , $controller_id );

    return $initial_data;

  }

  private static function get_default_data( $default_data , $controller_id ) {

    $default_data = apply_filters( "mywp_controller_default_data_{$controller_id}" , $default_data );
    $default_data = apply_filters( 'mywp_controller_default_data' , $default_data , $controller_id );

    return $default_data;

  }

  private static function get_model( $controller , $controller_id ) {

    do_action( 'mywp_controller_before_get_model' , $controller_id );

    $pre_model = apply_filters( "mywp_controller_pre_get_model_{$controller_id}" , false , $controller );
    $pre_model = apply_filters( 'mywp_controller_pre_get_model' , $pre_model , $controller_id , $controller );

    if( $pre_model !== false ) {

      return $pre_model;

    }

    $mywp_model = new MywpModel( $controller_id , 'controller' , $controller['network'] );

    $mywp_model->set_initial_data( $controller['initial_data'] );
    $mywp_model->set_default_data( $controller['default_data'] );

    do_action( 'mywp_controller_after_get_model' , $controller_id );

    return $mywp_model;

  }

  public static function get_posts( $args = array() , $controller_id = false ) {

    do_action( 'mywp_controller_before_get_posts' , $args , $controller_id );

    $args = apply_filters( "mywp_controller_pre_get_posts_args_{$controller_id}" , $args );
    $args = apply_filters( 'mywp_controller_pre_get_posts_args' , $args , $controller_id );

    $posts = apply_filters( "mywp_controller_pre_get_posts_{$controller_id}" , false , $args , $controller_id );
    $posts = apply_filters( 'mywp_controller_pre_get_posts' , $posts , $args , $controller_id );

    if( $posts !== false ) {

      return $posts;

    }

    $posts = MywpPostType::get_posts( $args );

    $posts = apply_filters( "mywp_controller_change_get_posts_{$controller_id}" , $posts , $args );
    $posts = apply_filters( 'mywp_controller_change_get_posts' , $posts , $args , $controller_id );

    do_action( 'mywp_controller_after_get_posts' , $posts , $args , $controller_id );

    return $posts;

  }

}

endif;
