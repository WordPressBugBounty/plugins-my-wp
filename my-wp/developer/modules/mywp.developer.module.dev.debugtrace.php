<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDebugtrace' ) ) :

final class MywpDeveloperModuleDebugtrace extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_debugtrace';

  static protected $priority = 1010;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Debug Backtrace' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function mywp_developer_debug() {

    if( ! MywpDeveloper::is_debug_item( 'debug_debugtrace' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    echo 'debug_backtrace() = ';

    print_r( debug_backtrace() );

  }

  protected static function mywp_debug_render() {

    if( ! MywpDeveloper::is_debug_item( 'debug_debugtrace' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    printf( '<textarea readonly="readonly">%s</textarea>' , print_r( debug_backtrace() , true ) );

  }

}

MywpDeveloperModuleDebugtrace::init();

endif;
