<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleLoginUser' ) ) :

final class MywpControllerModuleLoginUser extends MywpControllerAbstractModule {

  static protected $id = 'login_user';

  private static $user = false;

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['login_redirect_url'] = '';
    $initial_data['logout_redirect_url'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['login_redirect_url'] = '';
    $default_data['logout_redirect_url'] = '';

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( is_admin() ) {

      return false;

    }

    add_action( 'login_init' , array( __CLASS__ , 'login_init' ) );

  }

  public static function login_init() {

    add_filter( 'login_redirect' , array( __CLASS__ , 'login_redirect' ) , 10 , 3 );

    add_filter( 'logout_redirect' , array( __CLASS__ , 'logout_redirect' ) , 10 , 3 );

  }

  public static function mywp_user_set_user_id( $user_id ) {

    if( is_wp_error( self::$user ) ) {

      return false;

    }

    if( ! empty( $user_id ) ) {

      return $user_id;

    }

    if( empty( self::$user->ID ) ) {

      return false;

    }

    $user_id = self::$user->ID;

    return $user_id;

  }

  public static function login_redirect( $redirect_to , $requested_redirect_to , $user ) {

    self::$user = $user;

    if( is_wp_error( $user ) ) {

      return $redirect_to;

    }

    if( empty( $user->ID ) ) {

      return $redirect_to;

    }

    add_filter( 'mywp_user_set_user_id' , array( __CLASS__ , 'mywp_user_set_user_id' ) );

    if( self::is_do_controller() ) {

      if( self::is_do_function( __FUNCTION__ ) ) {

        $setting_data = self::get_setting_data();

        if( ! empty( $setting_data['login_redirect_url'] ) ) {

          $login_redirect_url = do_shortcode( $setting_data['login_redirect_url'] );

          if( ! empty( $login_redirect_url ) ) {

            $redirect_to = $login_redirect_url;

          }

        }

      }

    }

    remove_filter( 'mywp_user_set_user_id' , array( __CLASS__ , 'mywp_user_set_user_id' ) );

    self::after_do_function( __FUNCTION__ );

    return $redirect_to;

  }

  public static function logout_redirect( $redirect_to , $requested_redirect_to , $user ) {

    self::$user = $user;

    if( is_wp_error( $user ) ) {

      return $redirect_to;

    }

    if( empty( $user->ID ) ) {

      return $redirect_to;

    }

    add_filter( 'mywp_user_set_user_id' , array( __CLASS__ , 'mywp_user_set_user_id' ) );

    if( self::is_do_controller() ) {

      if( self::is_do_function( __FUNCTION__ ) ) {

        $setting_data = self::get_setting_data();

        if( ! empty( $setting_data['logout_redirect_url'] ) ) {

          $login_redirect_url = do_shortcode( $setting_data['logout_redirect_url'] );

          if( ! empty( $login_redirect_url ) ) {

            $redirect_to = $login_redirect_url;

          }

        }

      }

    }

    remove_filter( 'mywp_user_set_user_id' , array( __CLASS__ , 'mywp_user_set_user_id' ) );

    self::after_do_function( __FUNCTION__ );

    return $redirect_to;

  }

}

MywpControllerModuleLoginUser::init();

endif;
