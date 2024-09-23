<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAdminToolbar' ) ) :

final class MywpAdminToolbar {

  private static $raw_toolbar_menus = array();

  private static $toolbar_left_menus = array();

  private static $toolbar_right_menus = array();

  private static $default_toolbar;

  public static function init() {

    add_action( 'mywp_wp_loaded' , array( __CLASS__ , 'mywp_wp_loaded' ) );

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'set_default_toolbar' ) , 999 );

  }

  public static function set_default_toolbar() {

    global $wp_admin_bar;

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

    $current_left_menus = apply_filters( 'mywp_admin_toolbar_set_default_toolbar_left_menus' , self::$toolbar_left_menus );
    $current_right_menus = apply_filters( 'mywp_admin_toolbar_set_default_toolbar_right_menus' , self::$toolbar_right_menus );

    self::$default_toolbar = array( 'left' => $current_left_menus , 'right' => $current_right_menus  );

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

  public static function get_default_toolbar() {

    return self::$default_toolbar;

  }

  private static function find_default_menu( $find_id = false , $find_parent_id = false ) {

    if( empty( $find_id ) ) {

      return false;

    }

    $default_toolbar = self::get_default_toolbar();

    if( empty( $default_toolbar['left'] ) && empty( $default_toolbar['right'] ) ) {

      return false;

    }

    $find_id = strip_tags( do_shortcode( $find_id ) );
    $find_parent_id = strip_tags( $find_parent_id );

    $found_current_default = false;
    $found_parent_default = array();
    $found_childs_default = array();

    foreach( $default_toolbar as $menu_location => $menus ) {

      foreach( $menus as $menu_key => $menu ) {

        if( (string) $menu->id === (string) $find_id && (string) $menu->parent === (string) $find_parent_id ) {

          $found_current_default = $menu;
          break;

        }

      }

    }

    if( ! empty( $found_current_default ) ) {

      foreach( $default_toolbar as $menu_location => $menus ) {

        foreach( $menus as $menu_key => $menu ) {

          if( (string) $menu->parent === (string) $found_current_default->id ) {

            $found_childs_default[] = $menu;

          }

        }

      }

      if( ! empty( $found_current_default->parent) ) {

        foreach( $default_toolbar as $menu_location => $menus ) {

          foreach( $menus as $menu_key => $menu ) {

            if( (string) $menu->id === (string) $found_current_default->parent ) {

              $found_parent_default[] = $menu;

            }

          }

        }
      }

    }

    return array( 'current' => $found_current_default , 'parent' => $found_parent_default , 'childs' => $found_childs_default );

  }

  public static function default_item_convert( $item = false ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$item' );

    if( empty( $item ) or ! is_object( $item ) ) {

      MywpHelper::error_not_found_message( '$item' , $called_text );

      return false;

    }

    if( empty( $item->item_type ) ) {

      return false;

    }

    if( $item->item_type !== 'default') {

      return false;

    }

    if( empty( $item->item_default_id ) && empty( $item->item_default_parent_id ) ) {

      return false;

    }

    $default_menu = self::find_default_menu( $item->item_default_id , $item->item_default_parent_id );

    if( empty( $default_menu['current'] ) ) {

      return false;

    }

    $item->item_default_title = $default_menu['current']->title;
    $item->item_meta = $default_menu['current']->meta;
    $item->item_link_url = $default_menu['current']->href;

    return apply_filters( 'mywp_admin_toolbar_default_item_convert' , $item );

  }

}

MywpAdminToolbar::init();

endif;
