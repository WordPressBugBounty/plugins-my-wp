<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleMywpCache' ) ) :

final class MywpDeveloperModuleMywpCache extends MywpDeveloperAbstractModule {

  static protected $id = 'mywp_cache';

  static protected $priority = 120;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'mywp',
      'title' => __( 'Cache' , 'my-wp' ),
    );

    return $debug_renders;

  }

  private static function get_caches() {

    $mywp_cache = new MywpCache( 'all_cache' );

    return $mywp_cache->get_cache();

  }

  protected static function mywp_developer_debug() {

    if( ! MywpDeveloper::is_debug_item( 'mywp_cache' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $caches = self::get_caches();

    echo 'Mywp caches = ';

    foreach( $caches as $cache_key => $cache ) {

      echo $cache_key . ' = ';
      print_r( $cache );
      echo "\n";

    }

  }

  protected static function mywp_debug_render() {

    if( ! MywpDeveloper::is_debug_item( 'mywp_cache' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }


    $caches = self::get_caches();

    if( empty( $caches ) ) {

      return false;

    }

    echo '<ul>';

    foreach( $caches as $cache_key => $cache ) {

      printf( '<li>%s = <textarea readonly="readonly">%s</textarea></li>' , $cache_key , print_r( $cache , true ) );

    }

    echo '</ul>';

  }

}

MywpDeveloperModuleMywpCache::init();

endif;
