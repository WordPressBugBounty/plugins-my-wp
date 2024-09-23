<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingScreen' ) ) :

final class MywpSettingScreen {

  private static $current_screen_id;

  private static $current_screen;

  public static function get_setting_screens() {

    $pre_setting_screens = apply_filters( 'mywp_setting_screens' , array() );

    if( empty( $pre_setting_screens ) ) {

      return false;

    }

    $default = array(
      'id' => '',
      'title' => '',
      'menu' => '',
      'controller' => '',
      'use_form' => true,
      'use_advance' => false,
      'document_url' => false,
    );

    $setting_screens = array();

    foreach( $pre_setting_screens as $setting_screen_id => $setting_screen ) {

      $setting_screen = wp_parse_args( $setting_screen , $default );

      $setting_screen['id'] = $setting_screen_id;

      $setting_screens[ $setting_screen_id ] = $setting_screen;

    }

    return $setting_screens;

  }

  public static function get_setting_screen( $setting_screen_id = false ) {

    if( empty( $setting_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' );

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screens = self::get_setting_screens();

    if( empty( $setting_screens[ $setting_screen_id ] ) ) {

      return false;

    }

    return $setting_screens[ $setting_screen_id ];

  }

  public static function set_current_screen_id( $setting_screen_id = false ) {

    $setting_screen_id = esc_attr( strip_tags( $setting_screen_id ) );

    self::$current_screen_id = $setting_screen_id;

    self::set_current_screen( $setting_screen_id );

  }

  public static function get_current_screen_id() {

    return self::$current_screen_id;

  }

  private static function set_current_screen( $setting_screen_id = false ) {

    if( empty( $setting_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' );

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screen = self::get_setting_screen( $setting_screen_id );

    if( empty( $setting_screen ) ) {

      return false;

    }

    self::$current_screen = $setting_screen;

  }

  public static function get_current_screen() {

    return self::$current_screen;

  }

  public static function set_current_screen_by_menu_id( $setting_menu_id = false ) {

    if( empty( $setting_menu_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$setting_menu_id' );

      MywpHelper::error_require_message( '$setting_menu_id' , $called_text );

      return false;

    }

    $current_setting_screens = self::get_setting_screens_by_menu_id( $setting_menu_id );

    if( empty( $current_setting_screens ) ) {

      return false;

    }

    $current_setting_screen_id = false;

    foreach( $current_setting_screens as $setting_screens ) {

      $current_setting_screen_id = $setting_screens['id'];
      break;

    }

    if( empty( $current_setting_screen_id ) ) {

      return false;

    }

    self::set_current_screen_id( $current_setting_screen_id );

  }

  public static function get_setting_screens_by_menu_id( $find_setting_menu_id = false ) {

    if( empty( $find_setting_menu_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$find_setting_menu_id' );

      MywpHelper::error_require_message( '$find_setting_menu_id' , $called_text );

      return false;

    }

    $setting_screens = self::get_setting_screens();

    if( empty( $setting_screens ) ) {

      return false;

    }

    $found_setting_screens = array();

    foreach( $setting_screens as $setting_screen_id => $setting_screen ) {

      if( empty( $setting_screen['menu'] ) ) {

        continue;

      }

      if( $setting_screen['menu'] !== $find_setting_menu_id ) {

        continue;

      }

      $found_setting_screens[] = $setting_screen;

    }

    return $found_setting_screens;

  }

}

endif;
