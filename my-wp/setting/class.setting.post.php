<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingPost' ) ) :

final class MywpSettingPost {

  private static $current_post_id;

  private static $current_post;

  public static function init() {

    add_action( 'mywp_set_current_setting' , array( __CLASS__ , 'mywp_set_current_setting' ) );

  }

  public static function mywp_set_current_setting() {

    if( ! empty( $_GET['setting_post_id'] ) ) {

      self::set_current_post_id( $_GET['setting_post_id'] );

    } else {

      self::set_current_post_to_default();

    }

  }

  public static function get_setting_post() {

    $args = array(
      'post_type' => 'any',
      'post_status' => 'any',
      'posts_per_page' => 1,
      'order' => 'ASC',
      'orderby' => 'ID',
    );

    $posts = get_posts( $args );

    if( empty( $posts[0] ) ) {

      return false;

    }

    return apply_filters( 'mywp_setting_post' , $posts[0] );

  }

  public static function set_current_post_id( $post_id = false ) {

    $post_id = (int) $post_id;

    if( empty( $post_id ) ) {

      return false;

    }

    self::$current_post_id = $post_id;

    self::set_current_post( $post_id );

  }

  public static function get_current_post_id() {

    return self::$current_post_id;

  }

  private static function set_current_post( $post_id = false ) {

    if( empty( $post_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$post_id' );

      MywpHelper::error_require_message( '$post_id' , $called_text );

      return false;

    }

    $post = get_post( $post_id );

    if( empty( $post ) ) {

      return false;

    }

    self::$current_post = $post;

  }

  public static function get_current_post() {

    return self::$current_post;

  }

  public static function set_current_post_to_default() {

    $post = self::get_setting_post();

    if( empty( $post->ID ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ , '$post' );

      MywpHelper::error_require_message( '$post' , $called_text );

      return false;

    }

    self::set_current_post_id( $post->ID );

  }

}

MywpSettingPost::init();

endif;
