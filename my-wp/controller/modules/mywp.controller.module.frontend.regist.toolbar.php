<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleFrontendRegistToolbar' ) ) :

final class MywpControllerModuleFrontendRegistToolbar extends MywpControllerAbstractModule {

  static protected $id = 'frontend_regist_toolbar';

  static protected $is_do_controller = true;

  private static $raw_toolbar_menus = array();

  private static $toolbar_left_menus = array();

  private static $toolbar_right_menus = array();

  private static $default_toolbar;

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['regist_toolbar'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['regist_toolbar'] = array();

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( is_admin() ) {

      return false;

    }

    if( ! MywpApi::is_manager() ) {

      return false;

    }

    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'set_default_toolbar' ) , 999 );

  }

  public static function set_default_toolbar() {

    global $wp_admin_bar;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( empty( $_POST['mywp_regist_frontend_toolbar'] ) ) {

      return false;

    }

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return false;

    }

    $option = $mywp_model->get_option();

    if( empty( $option ) ) {

      $option = array();

    }

    $toolbar_menus = $wp_admin_bar->get_nodes();

    if( empty( $toolbar_menus ) ) {

      return false;

    }

    self::$raw_toolbar_menus = $toolbar_menus;

    self::set_parent_toolbar_menus( 'top-secondary' , 'right' );
    self::set_parent_toolbar_menus( '' , 'left' );

    if( isset( self::$toolbar_left_menus['top-secondary'] ) ) {

      unset( self::$toolbar_left_menus['top-secondary'] );

    }

    if( ! empty( self::$toolbar_right_menus ) ) {

      foreach( self::$toolbar_right_menus as $menu_id => $menu ) {

        if( $menu->parent === 'top-secondary' ) {

          self::$toolbar_right_menus[ $menu_id ]->parent = '';

        }

      }

    }

    $current_left_menus = apply_filters( 'mywp_frontend_toolbar_set_default_toolbar_left_menus' , self::$toolbar_left_menus );
    $current_right_menus = apply_filters( 'mywp_frontend_toolbar_set_default_toolbar_right_menus' , self::$toolbar_right_menus );

    self::$default_toolbar = array( 'left' => $current_left_menus , 'right' => $current_right_menus  );

    $option['home'] = self::$default_toolbar;

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

  }

  private static function set_parent_toolbar_menus( $find_parent_id = false , $menu_location = false ) {

    if( $find_parent_id === false or empty( $menu_location ) ) {

      return false;

    }

    reset( self::$raw_toolbar_menus );

    $find_parent_toolbar_menus = array();

    foreach( self::$raw_toolbar_menus as $key => $menu ) {

      if( (string) $find_parent_id === (string) $menu->parent ) {

        $find_parent_toolbar_menus[ $key ] = $menu;

        unset( self::$raw_toolbar_menus[ $key ] );

      }

    }

    if( ! empty( $find_parent_toolbar_menus ) ) {

      foreach( $find_parent_toolbar_menus as $key => $menu ) {

        if( $menu_location === 'left' ) {

          self::$toolbar_left_menus[ $key ] = $menu;

        } elseif( $menu_location === 'right' ) {

          self::$toolbar_right_menus[ $key ] = $menu;

        }

        self::set_parent_toolbar_menus( $menu->id , $menu_location );

      }

    }

  }

}

MywpControllerModuleFrontendRegistToolbar::init();

endif;
