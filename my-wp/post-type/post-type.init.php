<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpPostTypeInit' ) ) :

final class MywpPostTypeInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_action( 'mywp_init' , array( __CLASS__ , 'regist_post_type' ) );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'post-type/modules/';

    $includes = array(

      'admin_sidebar' => $dir . 'mywp.post-type.module.admin.sidebar.php',
      'admin_toolbar' => $dir . 'mywp.post-type.module.admin.toolbar.php',

      'front_toolbar' => $dir . 'mywp.post-type.module.front.toolbar.php',

      'logger'        => $dir . 'mywp.post-type.module.logger.php',

    );

    $includes = apply_filters( 'mywp_post_type_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_post_type_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function regist_post_type() {

    $post_types = MywpPostType::get_post_types();

    if( empty( $post_types ) ) {

      return false;

    }

    foreach( $post_types as $post_type_name => $args ) {

      register_post_type( $post_type_name , $args );

    }

  }

}

endif;
