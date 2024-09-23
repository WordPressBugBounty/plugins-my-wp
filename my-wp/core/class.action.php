<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAction' ) ) :

final class MywpAction {

  public static function init() {

    add_action( 'plugins_loaded' , array( __CLASS__ , 'do_mywp_start' ) , 11 );

    add_action( 'plugins_loaded' , array( __CLASS__ , 'plugins_loaded' ) , 20 );
    add_action( 'setup_theme' , array( __CLASS__ , 'setup_theme' ) , 20 );
    add_action( 'after_setup_theme' , array( __CLASS__ , 'after_setup_theme' ) , 20 );
    add_action( 'init' , array( __CLASS__ , 'wp_init' ) , 20 );
    add_action( 'wp_loaded' , array( __CLASS__ , 'wp_loaded' ) , 20 );

    add_action( 'mywp_request' , array( __CLASS__ , 'mywp_request' ) , 20 );
    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 20 );

  }

  public static function do_mywp_start() {

    do_action( 'mywp_start' );

  }

  public static function plugins_loaded() {

    do_action( 'mywp_plugins_loaded' );

  }

  public static function setup_theme() {

    do_action( 'mywp_setup_theme' );

  }

  public static function after_setup_theme() {

    do_action( 'mywp_after_setup_theme' );

  }

  public static function wp_init() {

    do_action( 'mywp_init' );

  }

  public static function wp_loaded() {

    do_action( 'mywp_wp_loaded' );

    if( ! class_exists( 'MywpHelper' ) ) {

      return false;

    }

    if( MywpHelper::is_doing( 'cron' ) ) {

      do_action( 'mywp_cron' );

    } elseif( MywpHelper::is_doing( 'xmlrpc' ) ) {

      do_action( 'mywp_xmlrpc' );

    } elseif( MywpHelper::is_doing( 'ajax' ) ) {

      do_action( 'mywp_ajax' );

    } else {

      do_action( 'mywp_request' );

    }

  }

  public static function mywp_request() {

    if( is_multisite() ) {

      if( is_network_admin() ) {

        if( MywpApi::is_network_manager() ) {

          do_action( 'mywp_request_network_admin_manager' );

        }

        do_action( 'mywp_request_network_admin' );

      }

    }

    if( is_admin() ) {

      if( MywpApi::is_manager() ) {

        do_action( 'mywp_request_admin_manager' );

      }

      do_action( 'mywp_request_admin' );

    } else {

      if( MywpApi::is_manager() ) {

        do_action( 'mywp_request_frontend_manager' );

      }

      do_action( 'mywp_request_frontend' );

    }

  }

  public static function mywp_ajax() {

    if( is_multisite() ) {

      if( MywpApi::is_network_manager() ) {

        do_action( 'mywp_ajax_network_manager' );

      }

    }

    if( MywpApi::is_manager() ) {

      do_action( 'mywp_ajax_manager' );

    }

  }

}

endif;
