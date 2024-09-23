<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingPostType' ) ) :

final class MywpSettingPostType {

  private static $current_post_type_id;

  private static $current_post_type;

  public static function init() {

    add_action( 'mywp_set_current_setting' , array( __CLASS__ , 'mywp_set_current_setting' ) );

  }

  public static function mywp_set_current_setting() {

    if( ! empty( $_GET['setting_post_type'] ) ) {

      self::set_current_post_type_id( $_GET['setting_post_type'] );

    } else {

      self::set_current_post_type_to_default();

    }

  }

  public static function get_setting_post_types() {

    $post_types = get_post_types( array( 'show_ui' => true ) , 'objects' );

    if( ! empty( $post_types['attachment'] ) ) {

      unset( $post_types['attachment'] );

    }

    return apply_filters( 'mywp_setting_post_types' , $post_types );

  }

  public static function get_setting_post_type( $post_type = false ) {

    if( empty( $post_type ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$post_type' );

      MywpHelper::error_require_message( '$post_type' , $called_text );

      return false;

    }

    $post_types = self::get_setting_post_types();

    if( empty( $post_types ) or empty( $post_types[ $post_type ] ) ) {

      return false;

    }

    return $post_types[ $post_type ];

  }

  public static function set_current_post_type_id( $post_type = false ) {

    $post_type = esc_attr( strip_tags( $post_type ) );

    self::$current_post_type_id = $post_type;

    self::set_current_post_type( $post_type );

  }

  public static function get_current_post_type_id() {

    return self::$current_post_type_id;

  }

  private static function set_current_post_type( $post_type = false ) {

    if( empty( $post_type ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$post_type' );

      MywpHelper::error_require_message( '$post_type' , $called_text );

      return false;

    }

    $post_type_object = self::get_setting_post_type( $post_type );

    if( empty( $post_type_object ) ) {

      return false;

    }

    self::$current_post_type = $post_type_object;

  }

  public static function get_current_post_type() {

    return self::$current_post_type;

  }

  public static function set_current_post_type_to_default() {

    $post_types = self::get_setting_post_types();

    if( empty( $post_types ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ , '$post_types' );

      MywpHelper::error_require_message( '$post_types' , $called_text );

      return false;

    }

    $current_post_type_id = false;

    foreach( $post_types as $post_type ) {

      $current_post_type_id = $post_type->name;

      break;

    }

    if( empty( $current_post_type_id ) ) {

      return false;

    }

    self::set_current_post_type_id( $current_post_type_id );

  }

  public static function get_latest_post( $post_type = false ) {

    if( empty( $post_type ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$post_type' );

      MywpHelper::error_require_message( '$post_type' , $called_text );

      return false;

    }

    $post_type = strip_tags( $post_type );

    $post_type_object = self::get_setting_post_type( $post_type );

    if( empty( $post_type_object ) ) {

      return false;

    }

    $args = array( 'post_type' => $post_type , 'order' => 'DESC' , 'orderby' => 'post_date' , 'numberposts' => 1 );

    $args = apply_filters( "mywp_setting_get_latest_post_args_{$post_type}" , $args );

    $posts = get_posts( $args );

    if( ! empty( $posts ) ) {

      $key = key( $posts );

      return $posts[ $key ];

    } else {

      return false;

    }

  }

  public static function get_one_post_link_edit( $post_type = false ) {

    $post = self::get_latest_post( $post_type );

    if( ! empty( $post ) ) {

      return add_query_arg( array( 'post' => $post->ID , 'action' => 'edit' ) , admin_url( 'post.php' ) );

    } else {

      return add_query_arg( array( 'post_type' => $post_type ) , admin_url( 'post-new.php' ) );

    }

  }

  public static function get_list_link( $post_type = false ) {

    $post_type = strip_tags( $post_type );

    return add_query_arg( array( 'post_type' => $post_type ) , admin_url( 'edit.php' ) );

  }

}

MywpSettingPostType::init();

endif;
