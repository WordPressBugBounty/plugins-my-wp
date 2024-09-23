<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpFrontendToolbar' ) ) :

final class MywpFrontendToolbar {

  private static $default_toolbar;

  public static function init() {}

  public static function get_default_toolbar() {

    if( ! empty( self::$default_toolbar ) ) {

      return self::$default_toolbar;

    }

    $mywp_model = new MywpModel( 'frontend_regist_toolbar' , 'class' );

    $option = $mywp_model->get_option();

    if( empty( $option['home'] ) ) {

      return false;

    }

    self::$default_toolbar = $option['home'];

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

    if( ! in_array( $item->item_default_id , array( 'site-name' , 'edit' , 'logout' ) ) ) {

      $item->item_link_url = $default_menu['current']->href;

    }

    return apply_filters( 'mywp_frontend_toolbar_default_item_convert' , $item );

  }

}

MywpFrontendToolbar::init();

endif;
