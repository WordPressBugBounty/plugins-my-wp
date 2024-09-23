<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleFrontendToolbar' ) ) :

final class MywpControllerModuleFrontendToolbar extends MywpAbstractControllerToolbarModule {

  static protected $id = 'frontend_toolbar';

  static protected $post_type = 'mywp_front_toolbar';

  static private $current_post = '';

  static protected $toolbar = false;

  static protected $toolbar_items = false;

  static protected $toolbar_items_added_classes = false;

  static protected $child_items = array();

  static protected $parent_items = array();

  static protected $found_parent_item_ids = array();

  static protected $current_url = false;

  static protected $find_parent_id = array();

  protected static function default_item_convert( $item ) {

    return MywpFrontendToolbar::default_item_convert( $item );

  }

  public static function mywp_wp_loaded() {

    if( is_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_filter( 'mywp_controller_' . self::$id . '_get_toolbar_item' , array( __CLASS__ , 'frontend_get_toolbar_item' ) );

    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'remove_detault_menus' ) , 1000 );
    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'customize_admin_bar' ) , 1000 );
    add_action( 'wp_after_admin_bar_render' , array( __CLASS__ , 'wp_after_admin_bar_render' ) );

  }

  private static function is_edit_post_remove( $toolbar_items ) {

    global $wp_the_query;

    if( empty( $toolbar_items ) ) {

      return true;

    }

    if( empty( $wp_the_query ) ) {

      return true;

    }

    $current_object = $wp_the_query->get_queried_object();

    if( empty( $current_object->post_type ) ) {

      return true;

    }

    $post_type_object = get_post_type_object( $current_object->post_type );

    if( empty( $post_type_object ) ) {

      return true;

    }

    if( ! $post_type_object->show_in_admin_bar ) {

      return true;

    }

    $edit_post_link = get_edit_post_link( $current_object->ID );

    if( empty( $edit_post_link ) ) {

      return true;

    }

    if( ! current_user_can( 'edit_post' , $current_object->ID ) ) {

      return true;

    }

    self::$current_post = $current_object;

    return false;

  }

  public static function frontend_get_toolbar_item( $toolbar_items ) {

    $is_edit_post_remove = self::is_edit_post_remove( $toolbar_items );

    foreach( $toolbar_items as $key => $toolbar_item ) {

      if( $toolbar_item->item_type !== 'edit_post' ) {

        continue;

      }

      if( $is_edit_post_remove ) {

        unset( $toolbar_items[ $key ] );

      } else {

        if( empty( self::$current_post ) ) {

          unset( $toolbar_items[ $key ] );

        } else {

          $post_type_object = get_post_type_object( self::$current_post->post_type );

          $toolbar_items[ $key ]->item_capability = '';

          $toolbar_items[ $key ]->item_link_title = $post_type_object->labels->edit_item;

          $toolbar_items[ $key ]->item_link_url = get_edit_post_link( self::$current_post->ID );

        }

      }

    }

    return $toolbar_items;

  }

  public static function wp_after_admin_bar_render() {

    $toolbar = self::get_toolbar();

    if( empty( $toolbar ) ) {

      return false;

    }

    $wp_styles = wp_styles();

    printf( '<link rel="stylesheet" id="mywp_frontend_toolbar-css"  href="%sfrontend-toolbar.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , $wp_styles->default_version );

    printf( '<script type="text/javascript" src="%sfrontend-toolbar.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , $wp_styles->default_version );

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['custom_menu_ui'] ) ) {

      printf( '<link rel="stylesheet" id="mywp_frontend_toolbar-custom-ui-css"  href="%sfrontend-toolbar-custom-ui.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , MYWP_VERSION );

      printf( '<script type="text/javascript" src="%sfrontend-toolbar-custom-ui.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , MYWP_VERSION );

    }

  }

}

MywpControllerModuleFrontendToolbar::init();

endif;
