<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpModel' ) ) :

final class MywpModel {

  private $id = false;
  private $model_id = false;
  private $type = false;
  private $network = false;

  private $initial_data = false;
  private $default_data = false;

  private $option_key = false;
  private $option_autoload = false;

  private $pre_get_option = false;
  private $option = false;
  private $change_get_option = false;

  private $pre_setting_data = false;
  private $setting_data = false;
  private $change_setting_data = false;

  public function __construct( $model_id = false , $type = false , $network = false ) {

    if( empty( $model_id ) ) {

      $called_text = sprintf( 'new %s( %s , %s , %s )' , __CLASS__ , '$model_id' , '$type' , '$network' );

      MywpHelper::error_require_message( '$model_id' , $called_text );

      return false;

    }

    $this->model_id = strip_tags( $model_id );

    if( empty( $type ) ) {

      $called_text = sprintf( 'new %s( %s , %s , %s )' , __CLASS__ , '$model_id' , '$type' , '$network' );

      MywpHelper::error_require_message( '$type' , $called_text );

      return false;

    }

    $this->type = strip_tags( $type );

    $this->id = "mywp_{$this->model_id}";

    if( ! empty( $network ) ) {

      $this->network = true;

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

  public function set_initial_data( $initial_data = false ) {

    $this->initial_data = $initial_data;

  }

  public function get_initial_data() {

    return $this->initial_data;

  }

  public function set_default_data( $default_data = false ) {

    $this->default_data = $default_data;

  }

  public function get_default_data() {

    return $this->default_data;

  }

  public function get_option_key() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->option_key = apply_filters( "mywp_model_get_option_key_{$id}" , $id , $this->get_type() , $this->is_network() );

    return $this->option_key;

  }

  public function get_option_autoload() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->option_autoload = apply_filters( "mywp_model_get_option_autoload_{$id}" , $this->option_autoload , $this->get_type() , $this->is_network() );

    return $this->option_autoload;

  }

  public function get_option() {

    $option_key = $this->get_option_key();

    if( empty( $option_key ) ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$option_key' , $called_text );

      return false;

    }

    do_action( 'mywp_model_before_get_option' , $option_key , $this->get_type() , $this->is_network() );

    $this->pre_get_option = apply_filters( "mywp_model_pre_get_option_{$option_key}" , false , $this->get_type() , $this->is_network() );

    if( $this->pre_get_option !== false ) {

      return $this->pre_get_option;

    }

    if( $this->is_network() ) {

      $this->option = get_site_option( $option_key );

    } else {

      $this->option = get_option( $option_key );

    }

    $change_get_option = apply_filters( "mywp_model_change_get_option_{$option_key}" , $this->option , $this->get_type() , $this->is_network() );

    do_action( 'mywp_model_after_get_option' , $this->option , $change_get_option , $option_key , $this->get_type() , $this->is_network() );

    if( $change_get_option !== $this->option ) {

      $this->change_get_option = $change_get_option;

      return $this->change_get_option;

    }

    return $this->option;

  }

  public function update_data( $update_data ) {

    $option_key = $this->get_option_key();

    if( empty( $option_key ) ) {

      $called_text = sprintf( '(object) %s->%s( %s )' , __CLASS__ , __FUNCTION__ , '$update_data' );

      MywpHelper::error_not_found_message( '$option_key' , $called_text );

      return false;

    }

    do_action( "mywp_model_before_update_data_{$option_key}" , $update_data , $this->get_type() , $this->is_network() );

    if( $this->is_network() ) {

      $return = update_site_option( $option_key , $update_data );

    } else {

      $option_autoload = $this->get_option_autoload();

      $return = update_option( $option_key , $update_data , $option_autoload );

    }

    do_action( "mywp_model_after_update_data_{$option_key}" , $return , $update_data , $this->get_type() , $this->is_network() );

    return $return;

  }

  public function remove_data() {

    $option_key = $this->get_option_key();

    if( empty( $option_key ) ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$option_key' , $called_text );

      return false;

    }

    do_action( "mywp_model_before_remove_data_{$option_key}" , $this->get_type() , $this->is_network() );

    if( $this->is_network() ) {

      $return = delete_site_option( $option_key );

    } else {

      $return = delete_option( $option_key );

    }

    do_action( "mywp_model_after_remove_data_{$option_key}" , $return , $this->get_type() , $this->is_network() );

    return $return;

  }

  public function get_setting_data() {

    $option_key = $this->get_option_key();

    if( empty( $option_key ) ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$option_key' , $called_text );

      return false;

    }

    do_action( 'mywp_model_before_get_setting_data' , $option_key , $this->get_type() , $this->is_network() );

    $this->pre_setting_data = apply_filters( "mywp_model_pre_get_setting_data_{$option_key}" , false , $this->get_type() , $this->is_network() );

    if( $this->pre_setting_data !== false ) {

      $this->pre_setting_data = wp_parse_args( $this->pre_setting_data , $this->get_initial_data() );

      return $this->pre_setting_data;

    }

    $this->setting_data = $this->get_option();

    if( $this->setting_data === false ) {

      $this->setting_data = $this->get_default_data();

    }

    $this->setting_data = wp_parse_args( $this->setting_data , $this->get_initial_data() );

    $change_setting_data = apply_filters( "mywp_model_change_get_setting_data_{$option_key}" , $this->setting_data , $this->get_type() , $this->is_network() );

    do_action( 'mywp_model_after_get_setting_data' , $this->setting_data , $change_setting_data , $option_key , $this->get_type() , $this->is_network() );

    if( $change_setting_data !== $this->setting_data ) {

      $this->change_setting_data = $change_setting_data;

      return $this->change_setting_data;

    }

    return $this->setting_data;

  }

}

endif;
