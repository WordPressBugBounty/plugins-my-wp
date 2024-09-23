<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminToolbar' ) ) :

final class MywpControllerModuleAdminToolbar extends MywpAbstractControllerToolbarModule {

  static protected $id = 'admin_toolbar';

  static protected $post_type = 'mywp_admin_toolbar';

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

    return MywpAdminToolbar::default_item_convert( $item );

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_filter( 'mywp_controller_' . self::$id . '_get_toolbar_item' , array( __CLASS__ , 'admin_get_toolbar_item' ) );

    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'remove_detault_menus' ) , 1000 );
    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'customize_admin_bar' ) , 1000 );
    add_action( 'wp_after_admin_bar_render' , array( __CLASS__ , 'wp_after_admin_bar_render' ) );

  }

  private static function is_view_post_remove( $toolbar_items ) {

    if( empty( $toolbar_items ) ) {

      return true;

    }

    $current_screen = get_current_screen();

    if( empty( $current_screen ) ) {

      return true;

    }

    if( $current_screen->base !== 'post' ) {

      return true;

    }

    $post = get_post();

    if( empty( $post ) ) {

      return true;

    }

    if( ! current_user_can( 'read_post' , $post->ID ) ) {

      return true;

    }

    $post_type_object = get_post_type_object( $post->post_type );

    if( empty( $post_type_object ) ) {

      return true;

    }

    if( ! $post_type_object->public ) {

      return true;

    }

    if( ! $post_type_object->show_in_admin_bar ) {

      return true;

    }

    self::$current_post = $post;

    return false;

  }

  public static function admin_get_toolbar_item( $toolbar_items ) {

    $is_view_post_remove = self::is_view_post_remove( $toolbar_items );

    foreach( $toolbar_items as $key => $toolbar_item ) {

      if( $toolbar_item->item_type !== 'view_post' ) {

        continue;

      }

      if( $is_view_post_remove ) {

        unset( $toolbar_items[ $key ] );

      } else {

        if( empty( self::$current_post ) ) {

          unset( $toolbar_items[ $key ] );

        } else {

          $post_type_object = get_post_type_object( self::$current_post->post_type );

          $toolbar_items[ $key ]->item_capability = '';

          $toolbar_items[ $key ]->item_link_title = $post_type_object->labels->view_item;

          if( self::$current_post->post_status === 'draft' ) {

            $toolbar_items[ $key ]->item_link_url = get_preview_post_link( self::$current_post );

            $toolbar_items[ $key ]->item_meta = array( 'target' => 'wp-preview-' . self::$current_post->ID );

          } else {

            $toolbar_items[ $key ]->item_link_url = get_permalink( self::$current_post->ID );

          }

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

    printf( '<link rel="stylesheet" id="mywp_admin_toolbar-css"  href="%sadmin-toolbar.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , $wp_styles->default_version );

    printf( '<script type="text/javascript" src="%sadmin-toolbar.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , $wp_styles->default_version );

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['custom_menu_ui'] ) ) {

      printf( '<link rel="stylesheet" id="mywp_admin_toolbar-custom-ui-css"  href="%sadmin-toolbar-custom-ui.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , MYWP_VERSION );

      printf( '<script type="text/javascript" src="%sadmin-toolbar-custom-ui.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , MYWP_VERSION );

    }

  }

}

MywpControllerModuleAdminToolbar::init();

endif;
