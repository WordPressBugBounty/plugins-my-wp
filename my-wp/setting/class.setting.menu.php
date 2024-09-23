<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingMenu' ) ) :

final class MywpSettingMenu {

  private static $setting_menus;

  private static $current_menu_id;

  private static $current_menu;

  private static $menu_hook_names;

  public static function get_setting_menus() {

    if( did_action( 'mywp_request_admin' ) ) {

      return self::$setting_menus;

    }

    $pre_setting_menus = apply_filters( 'mywp_setting_menus' , array() );

    if( empty( $pre_setting_menus ) ) {

      return false;

    }

    $default = array(
      'id' => '',
      'main' => false,
      'network' => false,
      'parent' => '',
      'menu_title' => '',
      'page_title' => '',
      'slug' => '',
      'render_function' => '',
      'multiple_screens' => true,
      'icon_url' => false,
    );

    $setting_menus = array();

    foreach( $pre_setting_menus as $setting_menu_id => $setting_menu ) {

      $setting_menu = wp_parse_args( $setting_menu , $default );

      $setting_menu['id'] = $setting_menu_id;

      if( empty( $setting_menu['menu_title'] ) ) {

        $setting_menu['menu_title'] = $setting_menu_id;

      }

      if( empty( $setting_menu['page_title'] ) ) {

        $setting_menu['page_title'] = $setting_menu['menu_title'];

      }

      if( empty( $setting_menu['slug'] ) ) {

        $setting_menu['slug'] = sprintf( 'mywp_%s' , $setting_menu_id );

      }

      if( empty( $setting_menu['render_function'] ) ) {

        $setting_menu['render_function'] = array( 'MywpSetting' , 'setting_view' );

      }

      if( empty( $setting_menu['main'] ) ) {

        if( empty( $setting_menu['parent'] ) ) {

          if( ! empty( $setting_menu['network'] ) ) {

            $setting_menu['parent'] = 'mywp_network';

          } else {

            $setting_menu['parent'] = 'mywp';

          }

        }

      }

      $setting_menus[ $setting_menu_id ] = $setting_menu;

    }

    self::$setting_menus = $setting_menus;

    return $setting_menus;

  }

  public static function get_setting_menu( $setting_menu_id = false ) {

    if( empty( $setting_menu_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$setting_menu_id' );

      MywpHelper::error_require_message( '$setting_menu_id' , $called_text );

      return false;

    }

    $setting_menus = self::get_setting_menus();

    if( empty( $setting_menus[ $setting_menu_id ] ) ) {

      return false;

    }

    return $setting_menus[ $setting_menu_id ];

  }

  public static function set_current_menu_id( $setting_menu_id = false ) {

    $setting_menu_id = esc_attr( strip_tags( $setting_menu_id ) );

    self::$current_menu_id = $setting_menu_id;

    self::set_current_menu( $setting_menu_id );

  }

  public static function get_current_menu_id() {

    return self::$current_menu_id;

  }

  private static function set_current_menu( $setting_menu_id = false ) {

    if( empty( $setting_menu_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$setting_menu_id' );

      MywpHelper::error_require_message( '$setting_menu_id' , $called_text );

      return false;

    }

    $setting_menu = self::get_setting_menu( $setting_menu_id );

    if( empty( $setting_menu ) ) {

      return false;

    }

    self::$current_menu = $setting_menu;

  }

  public static function get_current_menu() {

    return self::$current_menu;

  }

  public static function add_menu( $setting_menu_id = false , $setting_menu = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_menu_id' , '$setting_menu' );

    if( empty( $setting_menu_id ) ) {

      MywpHelper::error_require_message( '$setting_menu_id' , $called_text );

      return false;

    }

    if( empty( $setting_menu ) ) {

      MywpHelper::error_not_found_message( '$setting_menu' , $called_text );

      return false;

    }

    if( empty( $setting_menu['menu_title'] ) ) {

      MywpHelper::error_not_found_message( '$setting_menu["menu_title"]' , $called_text );

      return false;

    }

    $capability = MywpApi::get_manager_capability();

    if( ! empty( $setting_menu['network'] ) ) {

      $capability = MywpApi::get_network_manager_capability();

    }

    if( $setting_menu['main'] ) {

      $menu_hook_name = add_menu_page( $setting_menu['page_title'] , $setting_menu['menu_title'] , $capability , $setting_menu['slug'] , $setting_menu['render_function'] , $setting_menu['icon_url'] );

    } else {

      $menu_hook_name = add_submenu_page( $setting_menu['parent'] , $setting_menu['page_title'] , $setting_menu['menu_title'] , $capability , $setting_menu['slug'] , $setting_menu['render_function'] );

    }

    self::$menu_hook_names[ $setting_menu_id ] = $menu_hook_name;

  }

  public static function get_menu_hook_names() {

    return self::$menu_hook_names;

  }

  public static function set_current_menu_by_page_hook( $find_page_hook = false ) {

    $setting_menu_id = self::get_setting_menu_id_by_page_hook( $find_page_hook );

    if( empty( $setting_menu_id ) ) {

      return false;

    }

    self::set_current_menu_id( $setting_menu_id );

  }

  public static function get_setting_menu_id_by_page_hook( $find_page_hook = false ) {

    if( empty( $find_page_hook ) ) {

      return false;

    }

    $menu_hook_names = self::get_menu_hook_names();

    if( empty( $menu_hook_names ) ) {

      return false;

    }

    $current_setting_menu_id = array_search( $find_page_hook , $menu_hook_names );

    if( empty( $current_setting_menu_id ) ) {

      return false;

    }

    return $current_setting_menu_id;

  }

}

endif;
