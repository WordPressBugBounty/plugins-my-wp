<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingMetaBox' ) ) :

final class MywpSettingMetaBox {

  private static $current_meta_box_screen_id;

  private static $current_meta_boxes;

  private static $current_metabox_screen_url;

  private static $current_metabox_setting_data;

  private static function get_regist_meta_boxes_model() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $mywp_controller = MywpController::get_controller( 'admin_regist_meta_boxes' );

    if( empty( $mywp_controller['model'] ) ) {

      MywpHelper::error_require_message( '$mywp_controller["model"]' , $called_text );

      return false;

    }

    return $mywp_controller['model'];

  }

  public static function get_setting_meta_boxes() {

    $regist_meta_boxes_model = self::get_regist_meta_boxes_model();

    if( empty( $regist_meta_boxes_model ) ) {

      MywpHelper::error_require_message( '$regist_meta_boxes_model' , $called_text );

      return false;

    }

    $option = $regist_meta_boxes_model->get_option();

    if( empty( $option['regist_meta_boxes'] ) ) {

      return false;

    }

    $setting_meta_boxes = $option['regist_meta_boxes'];

    return apply_filters( 'mywp_setting_meta_boxes' , $setting_meta_boxes );

  }

  public static function get_setting_meta_box( $meta_box_screen_id = false ) {

    if( empty( $meta_box_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$meta_box_screen_id' );

      MywpHelper::error_require_message( '$meta_box_screen_id' , $called_text );

      return false;

    }

    $setting_meta_boxes = self::get_setting_meta_boxes();

    if( empty( $setting_meta_boxes ) or empty( $setting_meta_boxes[ $meta_box_screen_id ] ) ) {

      return false;

    }

    return $setting_meta_boxes[ $meta_box_screen_id ];

  }

  public static function set_current_meta_box_screen_id( $meta_box_screen_id = false ) {

    $meta_box_screen_id = strip_tags( $meta_box_screen_id );

    self::$current_meta_box_screen_id = $meta_box_screen_id;

    self::set_current_meta_boxes( $meta_box_screen_id );

  }

  public static function get_current_meta_box_screen_id() {

    return self::$current_meta_box_screen_id;

  }

  private static function set_current_meta_boxes( $meta_box_screen_id = false ) {

    if( empty( $meta_box_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$meta_box_screen_id' );

      MywpHelper::error_require_message( '$meta_box_screen_id' , $called_text );

      return false;

    }

    $meta_boxes = self::get_setting_meta_box( $meta_box_screen_id );

    if( empty( $meta_boxes ) ) {

      return false;

    }

    self::$current_meta_boxes = $meta_boxes;

  }

  public static function get_current_meta_boxes() {

    return self::$current_meta_boxes;

  }

  public static function set_current_meta_box_screen_url( $meta_box_screen_url = false ) {

    $meta_box_screen_url = strip_tags( $meta_box_screen_url );

    self::$current_metabox_screen_url = $meta_box_screen_url;

  }

  public static function get_current_meta_box_screen_url() {

    return self::$current_metabox_screen_url;

  }

  public static function set_current_meta_box_setting_data( $meta_box_setting_data = array() ) {

    if( ! is_array( $meta_box_setting_data ) ) {

      $meta_box_setting_data = array();

    }

    if( ! empty( $meta_box_setting_data ) ) {

      foreach( $meta_box_setting_data as $key => $data ) {

        if( ! isset( $data['action'] ) ) {

          unset( $meta_box_setting_data[ $key ] );

        }

      }

    }

    self::$current_metabox_setting_data = $meta_box_setting_data;

  }

  public static function get_current_meta_box_setting_data() {

    return self::$current_metabox_setting_data;

  }

  public static function delete_current_meta_boxes() {

    if( empty( self::$current_meta_box_screen_id ) ) {

      return false;

    }

    $regist_meta_boxes_model = self::get_regist_meta_boxes_model();

    if( empty( $regist_meta_boxes_model ) ) {

      MywpHelper::error_require_message( '$regist_meta_boxes_model' , $called_text );

      return false;

    }

    $option = $regist_meta_boxes_model->get_option();

    if( ! isset( $option['regist_meta_boxes'][ self::$current_meta_box_screen_id ] ) ) {

      return false;

    }

    unset( $option['regist_meta_boxes'][ self::$current_meta_box_screen_id ] );

    $regist_meta_boxes_model->update_data( $option );

    return true;

  }

}

endif;
