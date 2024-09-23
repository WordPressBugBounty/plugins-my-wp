<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleMainGeneral' ) ) :

final class MywpControllerModuleMainGeneral extends MywpControllerAbstractModule {

  static protected $id = 'main_general';

  static protected $is_do_controller = true;

  protected static function after_init() {

    add_filter( 'mywp_controller_pre_get_model_' . self::$id , array( __CLASS__ , 'mywp_controller_pre_get_model' ) );

  }

  public static function mywp_controller_pre_get_model( $pre_model ) {

    $pre_model = true;

    return $pre_model;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    add_filter( 'plugin_row_meta' , array( __CLASS__ , 'plugin_row_meta' ) , 10 , 4 );

    if( is_multisite() ) {

      add_filter( 'network_admin_plugin_action_links_' . MYWP_PLUGIN_BASENAME , array( __CLASS__ , 'plugin_action_links' ) , 10 , 4 );

    } else {

      add_filter( 'plugin_action_links_' . MYWP_PLUGIN_BASENAME , array( __CLASS__ , 'plugin_action_links' ) , 10 , 4 );

    }

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'admin_enqueue_scripts' ) );

    add_action( 'admin_body_class' , array( __CLASS__ , 'admin_body_class' ) );

    add_action( 'all_admin_notices' , array( __CLASS__ , 'all_admin_notices' ) );

  }

  private static function is_mywp_file( $plugin_file_name = false ) {

    if( empty( $plugin_file_name ) ) {

      return false;

    }

    $plugin_file_name = strip_tags( $plugin_file_name );

    if ( strpos( MYWP_PLUGIN_BASENAME , $plugin_file_name ) === false ) {

      return false;

    }

    return true;

  }

  public static function plugin_row_meta( $plugin_meta , $plugin_file , $plugin_data , $status ) {

    if ( ! self::is_mywp_file( $plugin_file ) ) {

      return $plugin_meta;

    }

    $plugin_info = MywpApi::plugin_info();

    if( ! empty( $plugin_info['document_url'] ) ) {

      $plugin_meta[] =  sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , esc_url( $plugin_info['document_url'] ) , __( 'Documents' , 'my-wp' ) );

    }

    if( ! empty( $plugin_info['forum_url'] ) ) {

      $plugin_meta[] =  sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , esc_url( $plugin_info['forum_url'] ) , __( 'Support Forums' ) );

    }

    if( ! empty( $plugin_info['review_url'] ) ) {

      $plugin_meta[] =  sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , esc_url( $plugin_info['review_url'] ) , __( 'Review' , 'my-wp' ) );

    }

    $plugin_meta = apply_filters( 'mywp_plugin_row_meta' , $plugin_meta , $plugin_file , $plugin_data , $status );

    return $plugin_meta;

  }

  public static function plugin_action_links( $actions , $plugin_file , $plugin_data , $context ) {

    if ( ! self::is_mywp_file( $plugin_file ) ) {

      return $actions;

    }

    $plugin_info = MywpApi::plugin_info();

    if( ! empty( $plugin_info['admin_url'] ) ) {

      $action_link = array( 'setting' => sprintf( '<a href="%1$s">%2$s</a>' , esc_url( $plugin_info['admin_url'] ) , __( 'Settings' ) ) );

      $actions = wp_parse_args( $actions , $action_link );

    }

    $actions = apply_filters( 'mywp_plugin_action_links' , $actions , $plugin_file , $plugin_data , $context );

    return $actions;

  }

  public static function admin_enqueue_scripts() {

    $dir_css = MywpApi::get_plugin_url( 'css' );

    wp_register_style( 'mywp_admin' , $dir_css . 'mywp-admin.css' , array() , MYWP_VERSION );

    wp_enqueue_style( 'mywp_admin' );

  }

  public static function admin_body_class( $admin_body_class ) {

    $admin_body_class .= ' mywp ';

    if( MywpApi::is_manager() ) {

      $admin_body_class .= ' mywp-manager ';

    }

    if( is_network_admin() ) {

      if( MywpApi::is_network_manager() ) {

        $admin_body_class .= ' mywp-network-manager ';

      }

    }

    return $admin_body_class;

  }

  public static function all_admin_notices() {

    $mywp_notice = new MywpNotice();

    $notices = $mywp_notice->get_notices();

    if( empty( $notices ) ) {

      return false;

    }

    if( ! empty( $notices['update'] ) ) {

      echo '<div class="updated mywp-notice">';

      foreach( $notices['update'] as $message ) {

        printf( '<p class="update-notice">%s</p>' , $message );

      }

      echo '</div>';

    }

    if( ! empty( $notices['error'] ) ) {

      echo '<div class="error mywp-notice">';

      foreach( $notices['error'] as $code => $message ) {

        printf( '<p class="error-notice error_%s">%s</p>' , $code , $message );

      }

      echo '</div>';

    }

    $mywp_notice->delete_notice();

  }

}

MywpControllerModuleMainGeneral::init();

endif;
