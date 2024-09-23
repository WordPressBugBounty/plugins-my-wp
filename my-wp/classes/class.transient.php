<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpTransient' ) ) :

final class MywpTransient {

  private $id = false;
  private $transient_id = false;
  private $type = false;
  private $network = false;

  private $option_key = false;
  private $option_timeout_key = false;

  private $trainsient_key = false;

  private $pre_get_data = false;
  private $transient = false;
  private $change_get_data = false;

  public function __construct( $transient_id = false , $type = false , $network = false ) {

    if( empty( $transient_id ) ) {

      $called_text = sprintf( 'new %s( %s , %s , %s )' , __CLASS__ , '$transient_id' , '$type' , '$network' );

      MywpHelper::error_require_message( '$transient_id' , $called_text );

      return false;

    }

    $this->transient_id = strip_tags( $transient_id );

    if( empty( $type ) ) {

      $called_text = sprintf( 'new %s( %s , %s , %s )' , __CLASS__ , '$transient_id' , '$type' , '$network' );

      MywpHelper::error_require_message( '$type' , $called_text );

      return false;

    }

    $this->type = strip_tags( $type );

    $this->id = "mywp_{$this->transient_id}";

    if( ! empty( $network ) ) {

      $this->network = true;

    }

    if( $this->network ) {

      $this->option_key = "_site_transient_{$this->id}";
      $this->option_timeout_key = "_site_transient_timeout_{$this->id}";

    } else {

      $this->option_key = "_transient_{$this->id}";
      $this->option_timeout_key = "_transient_timeout_{$this->id}";

    }

  }

  public function get_id() {

    return $this->id;

  }

  public function get_type() {

    return $this->type;

  }

  public function is_network() {

    if( ! empty( $this->network ) ) {

      return true;

    }

    return false;

  }

  public function get_option_key() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->option_key = apply_filters( "mywp_transient_get_option_key_{$id}" , $id , $this->get_type() , $this->is_network() );

    return $this->option_key;

  }

  public function get_option_timeout_key() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->option_timeout_key = apply_filters( "mywp_transient_get_option_timeout_key_{$id}" , $id , $this->get_type() , $this->is_network() );

    return $this->option_timeout_key;

  }

  public function get_transient_key() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->trainsient_key = apply_filters( "mywp_transient_get_key_{$id}" , $this->id , $this->get_type() , $this->is_network() );

    return $this->trainsient_key;

  }

  public function get_data() {

    $transient_key = $this->get_transient_key();

    if( empty( $transient_key ) ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$transient_key' , $called_text );

      return false;

    }

    do_action( 'mywp_transient_before_get_data' , $transient_key , $this->get_type() , $this->is_network() );

    $this->pre_get_data = apply_filters( "mywp_transient_pre_get_data_{$transient_key}" , false , $this->get_type() , $this->is_network() );

    if( $this->pre_get_data !== false ) {

      return $this->pre_get_data;

    }

    if( $this->network ) {

      $this->transient = get_site_transient( $transient_key );

    } else {

      $this->transient = get_transient( $transient_key );

    }

    $change_get_data = apply_filters( "mywp_transient_change_get_data_{$transient_key}" , $this->transient , $this->get_type() , $this->is_network() );

    do_action( 'mywp_transient_after_get_data' , $this->transient , $change_get_data , $transient_key , $this->get_type() , $this->is_network() );

    if( $change_get_data !== $this->transient ) {

      $this->change_get_data = $change_get_data;

      return $this->change_get_data;

    }

    return $this->transient;

  }

  public function update_data( $update_data , $timeout = HOUR_IN_SECONDS ) {

    $transient_key = $this->get_transient_key();

    if( empty( $transient_key ) ) {

      $called_text = sprintf( '(object) %s->%s( %s , %s )' , __CLASS__ , __FUNCTION__ , '$update_data' , '$timeout' );

      MywpHelper::error_not_found_message( '$transient_key' , $called_text );

      return false;

    }

    do_action( "mywp_transient_before_update_data_{$transient_key}" , $update_data , $this->get_type() , $this->is_network() );

    if( $this->is_network() ) {

      $return = set_site_transient( $transient_key , $update_data , $timeout );

    } else {

      $return = set_transient( $transient_key , $update_data , $timeout );

    }

    do_action( "mywp_transient_after_update_data_{$transient_key}" , $return , $update_data , $this->get_type() , $this->is_network() );

    return $return;

  }

  public function remove_data() {

    $transient_key = $this->get_transient_key();

    if( empty( $transient_key ) ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$transient_key' , $called_text );

      return false;

    }

    do_action( "mywp_transient_before_remove_data_{$transient_key}" , $this->get_type() , $this->is_network() );

    if( $this->is_network() ) {

      $return = delete_site_transient( $transient_key );

    } else {

      $return = delete_transient( $transient_key );

    }

    do_action( "mywp_transient_after_remove_data_{$transient_key}" , $return , $this->get_type() , $this->is_network() );

    return $return;

  }

}

endif;
