<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleCoreEnvironment' ) ) :

final class MywpDeveloperModuleCoreEnvironment extends MywpDeveloperAbstractModule {

  static protected $id = 'core_environment';

  static protected $priority = 70;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'core',
      'title' => __( 'Environment' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    $debug_lists = array();

    $defines = array(
      'WP_DEBUG',
      'WP_DEBUG_LOG',
      'WP_DEBUG_DISPLAY',
      'SCRIPT_DEBUG',
      'SAVEQUERIES',
      'WP_HOME',
      'WP_SITEURL',
      'WP_POST_REVISIONS',
      'WP_MEMORY_LIMIT',
      'WP_MAX_MEMORY_LIMIT',
    );

    foreach( $defines as $define ) {

      $debug_lists[ $define ] = false;

      if( defined( $define ) ) {

        $debug_lists[ $define ] = constant( $define );

      }

    }

    $debug_lists['wp_version'] = MywpHelper::get_wp_version();
    $debug_lists['is_multisite()'] = is_multisite();
    $debug_lists['PHP_VERSION'] = PHP_VERSION;

    if( function_exists( 'ini_get' ) ) {

      $debug_lists['max_input_vars'] = ini_get( 'max_input_vars' );
      $debug_lists['max_input_time'] = ini_get( 'max_input_time' );
      $debug_lists['max_execution_time'] = ini_get( 'max_execution_time' );

    } else {

      $debug_lists['max_input_vars'] = '';
      $debug_lists['max_input_time'] = '';
      $debug_lists['max_execution_time'] = '';

    }

    $debug_lists['$_SERVER'] = $_SERVER;

    return $debug_lists;

  }

}

MywpDeveloperModuleCoreEnvironment::init();

endif;
