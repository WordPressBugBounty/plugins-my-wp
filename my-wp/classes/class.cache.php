<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpCache' ) ) :

final class MywpCache {

  private $cache_key = false;

  public function __construct( $cache_key = false ) {

    $cache_key = strip_tags( $cache_key );

    if( empty( $cache_key ) ) {

      $called_text = sprintf( 'new %s( %s )' , __CLASS__ , '$cache_key' );

      MywpHelper::error_require_message( '$cache_key' , $called_text );

      return false;

    }

    $this->cache_key = $cache_key;

  }

  public function get_cache() {

    if( $this->cache_key === false ) {

      return false;

    }

    return wp_cache_get( $this->cache_key , 'mywp' );

  }

  public function add_cache( $data = false ) {

    if( $this->cache_key === false ) {

      return false;

    }

    if( $data === false ) {

      return false;

    }

    $cache = $this->get_cache();

    if( empty( $cache ) ) {

      $cache = array();

    }

    $cache[] = $data;

    $this->update_cache( $cache );

    return true;

  }

  public function update_cache( $data = false ) {

    if( $this->cache_key === false ) {

      return false;

    }

    if( $data === false ) {

      return false;

    }

    wp_cache_set( $this->cache_key , $data , 'mywp' );

    $all_cache = wp_cache_get( 'all_cache' , 'mywp' );

    if( empty( $all_cache ) ) {

      $all_cache = array();

    }

    $all_cache[ $this->cache_key ] = $data;

    wp_cache_set( 'all_cache' , $all_cache , 'mywp' );

    return true;

  }

  public function delete_cache() {

    if( $this->cache_key === false ) {

      return false;

    }

    return wp_cache_delete( $this->cache_key , 'mywp' );

  }

}

endif;
