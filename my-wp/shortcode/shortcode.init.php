<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpShortcodeInit' ) ) :

final class MywpShortcodeInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_action( 'mywp_init' , array( __CLASS__ , 'regist_shortcode' ) );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'shortcode/modules/';

    $includes = array(
      'comment_count' => $dir . 'mywp.shortcode.module.comment_count.php',
      'post'          => $dir . 'mywp.shortcode.module.post.php',
      'site'          => $dir . 'mywp.shortcode.module.site.php',
      'term'          => $dir . 'mywp.shortcode.module.term.php',
      'theme'         => $dir . 'mywp.shortcode.module.theme.php',
      'toolbar_item'  => $dir . 'mywp.shortcode.module.toolbar_item.php',
      'update_count'  => $dir . 'mywp.shortcode.module.update_count.php',
      'url'           => $dir . 'mywp.shortcode.module.url.php',
      'user'          => $dir . 'mywp.shortcode.module.user.php',
    );

    $includes = apply_filters( 'mywp_shortcode_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_shortcode_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function regist_shortcode() {

    $shortcodes = MywpShortcode::get_shortcodes();

    if( empty( $shortcodes ) ) {

      return false;

    }

    foreach( $shortcodes as $shotecode => $function ) {

      add_shortcode( $shotecode , $function , 11 , 3 );

    }

  }

}

endif;
