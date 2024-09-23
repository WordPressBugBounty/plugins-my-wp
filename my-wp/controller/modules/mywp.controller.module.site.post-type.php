<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleSitePostType' ) ) :

final class MywpControllerModuleSitePostType extends MywpControllerAbstractModule {

  static protected $id = 'site_post_type';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['change_cap_create_posts'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['change_cap_create_posts'] = false;

    return $default_data;

  }

  protected static function after_init() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'registered_post_type' , array( __CLASS__ , 'change_cap_create_posts' ) , 10 , 2 );

  }

  public static function change_cap_create_posts( $post_type , $post_type_object ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( empty( $post_type ) or empty( $post_type_object ) ) {

      return false;

    }

    if( empty( $post_type_object->cap ) or empty( $post_type_object->cap->create_posts ) ) {

      return false;

    }

    if( strpos( $post_type_object->cap->create_posts , 'edit_' ) === false ) {

      return false;

    }

    $exclude = array( 'custom_css' );

    if( in_array( $post_type , $exclude ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['change_cap_create_posts'] ) ) {

      return false;

    }

    $post_type_object->cap->create_posts = str_replace( 'edit_' , 'create_' , $post_type_object->cap->create_posts );

    self::after_do_function( __FUNCTION__ );

  }

}


MywpControllerModuleSitePostType::init();

endif;
