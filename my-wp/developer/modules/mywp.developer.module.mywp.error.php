<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleMywpError' ) ) :

final class MywpDeveloperModuleMywpError extends MywpDeveloperAbstractModule {

  static protected $id = 'mywp_error';

  static protected $priority = 10;

  protected static function after_init() {

    add_action( 'admin_footer' , array( __CLASS__ , 'show_error_count' ) );

  }

  public static function show_error_count() {

    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'mywp_debug_types_show_error_count' ) , 100 );

    add_filter( 'mywp_debug_renders' , array( __CLASS__ , 'mywp_debug_renders_show_error_count' ) , 100 );

  }

  public static function mywp_debug_types_show_error_count( $debug_types ) {

    $errors = self::get_errors();

    if( empty( $errors ) ) {

      return $debug_types;

    }

    if( isset( $debug_types['mywp'] ) ) {

      $debug_types['mywp'] .= ' (error) ';

    }

    return $debug_types;

  }

  public static function mywp_debug_renders_show_error_count( $debug_renders ) {

    $errors = self::get_errors();

    if( empty( $errors ) ) {

      return $debug_renders;

    }

    if( isset( $debug_renders[ self::$id ] ) ) {

      $debug_renders[ self::$id ]['title'] .= sprintf( ' (%s) ' , count( $errors ) );

    }

    return $debug_renders;

  }

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'mywp',
      'title' => sprintf( '%s Error' , MYWP_NAME ),
    );

    return $debug_renders;

  }

  private static function get_errors() {

    $errors = MywpApi::get_errors();

    return $errors;

  }

  protected static function mywp_developer_debug() {

    $errors = self::get_errors();

    echo 'Mywp error = ';

    if( empty( $errors ) ) {

      return false;

    }

    foreach( $errors as $key => $val ) {

      echo $val . "\n";

    }

  }

  protected static function mywp_debug_render() {

    $errors = self::get_errors();

    if( empty( $errors ) ) {

      printf( '<p>%s</p>' , __( 'No errors.' , 'my-wp' ) );

    } else {

      echo '<ul>';

      foreach( $errors as $error ) {

        printf( '<li>%s</li>' , $error );

      }

      echo '</ul>';

    }

  }

}

MywpDeveloperModuleMywpError::init();

endif;
