<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpShortcodeAbstractModule' ) ) :

abstract class MywpShortcodeAbstractModule {

  protected static $id;

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    add_filter( 'mywp_shortcode' , array( $class , 'mywp_shortcode' ) );

  }

  public static function mywp_shortcode( $shortcodes ) {

    $class = get_called_class();

    $shortcodes[ static::$id ] = array( $class , 'do_shortcode' );

    return $shortcodes;

  }

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    return $content;

  }

}

endif;
