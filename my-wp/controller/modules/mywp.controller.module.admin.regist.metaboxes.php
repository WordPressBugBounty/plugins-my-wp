<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminRegistMetaboxes' ) ) :

final class MywpControllerModuleAdminRegistMetaboxes extends MywpControllerAbstractModule {

  static protected $id = 'admin_regist_meta_boxes';

  static protected $is_do_controller = true;

  static private $screen_type = '';

  static private $post_type = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['regist_meta_boxes'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['regist_meta_boxes'] = array();

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! MywpApi::is_manager() ) {

      return false;

    }

    add_action( 'load-index.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-post.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-post-new.php' , array( __CLASS__ , 'load_screen' ) , 999 );

  }

  public static function load_screen() {

    global $pagenow;
    global $typenow;

    if( empty( $pagenow ) ) {

      return false;

    }

    if( $pagenow === 'index.php' ) {

      self::$screen_type = 'dashboard';

    } elseif( in_array( $pagenow , array( 'post-new.php' , 'post.php' ) ) ) {

      self::$screen_type = 'post';

      if( empty( $typenow ) ) {

        return false;

      }

      self::$post_type = $typenow;

    }

    add_action( 'in_admin_header' , array( __CLASS__ , 'regist_meta_boxes' ) , 10 );

  }

  public static function regist_meta_boxes() {

    global $wp_meta_boxes;

    if( empty( self::$screen_type ) && empty( self::$post_type ) ) {

      return false;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

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

    $metabox_screen_id = self::$screen_type;

    if( self::$screen_type === 'post' ) {

      $metabox_screen_id = self::$post_type;

    }

    if( empty( $wp_meta_boxes[ $metabox_screen_id ] ) ) {

      return false;

    }

    $current_meta_boxes = $wp_meta_boxes[ $metabox_screen_id ];

    if( ! isset( $option['regist_meta_boxes'][ $metabox_screen_id ] ) ) {

      $option['regist_meta_boxes'][ $metabox_screen_id ] = array();

    }

    if( ! empty( $_GET['mywp_refresh_metabox'] ) ) {

      $option['regist_meta_boxes'][ $metabox_screen_id ] = array();

    }

    foreach( $current_meta_boxes as $context => $priority_meta_boxes ) {

      if( empty( $priority_meta_boxes ) or ! is_array( $priority_meta_boxes ) ) {

        continue;

      }

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        if( empty( $meta_boxes ) or ! is_array( $meta_boxes ) ) {

          continue;

        }

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( empty( $meta_box ) or ! is_array( $meta_box ) ) {

            continue;

          }

          $regist_meta_box = array(
            'id' => $meta_box_id,
            'context' => $context,
            'priority' => $priority,
            'title' => strip_tags( $meta_box['title'] ),
            'args' => $meta_box['args'],
          );

          $option['regist_meta_boxes'][ $metabox_screen_id ][ $meta_box_id ] = $regist_meta_box;

        }

      }

    }

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminRegistMetaboxes::init();

endif;
