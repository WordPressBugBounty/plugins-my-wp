<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpNotice' ) ) :

final class MywpNotice {

  private $user_id = false;

  private $notice_key = 'mywp_notice';

  public function __construct( $user_id = false ) {

    if( $user_id === false ) {

      $user_id = get_current_user_id();

    } else {

      $user_id = (int) $user_id;

    }

    if( empty( $user_id ) ) {

      $called_text = sprintf( 'new %s( %s )' , __CLASS__ , '$user_id' );

      MywpHelper::error_require_message( '$user_id' , $called_text );

      return false;

    }

    $this->user_id = $user_id;

  }

  public function get_notices() {

    if( $this->user_id === false ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( 'user_id' , $called_text );

      return false;

    }

    $notices = get_user_meta( $this->user_id , $this->notice_key , true );

    if( empty( $notices ) ) {

      return array();

    }

    return $notices;

  }

  public function get_notice() {

    $notices = $this->get_notices();

    if( empty( $notices ) ) {

      return false;

    }

    $get_notice = false;

    foreach( $notices as $notice ) {

      $get_notice = $notice;

      break;

    }

    return $get_notice;

  }

  public function add_notice( $notice = false , $type = 'update' ) {

    $called_text = sprintf( '(object) %s->%s( %s )' , __CLASS__ , __FUNCTION__ , '$notice' );

    if( $this->user_id === false ) {

      MywpHelper::error_not_found_message( 'user_id' , $called_text );

      return false;

    }

    if( $notice === false ) {

      MywpHelper::error_not_found_message( '$notice' , $called_text );

      return false;

    }

    $type = strip_tags( $type );

    $notices = $this->get_notices();

    $notices[ $type ][] = $notice;

    return $this->update_notice( $notices );

  }

  public function add_notice_update( $notice = false ) {

    return $this->add_notice( $notice , 'update' );

  }

  public function add_notice_error( $notice = false ) {

    return $this->add_notice( $notice , 'error' );

  }

  public function update_notice( $notices = false ) {

    $called_text = sprintf( '(object) %s->%s( %s )' , __CLASS__ , __FUNCTION__ , '$notices' );

    if( $this->user_id === false ) {

      MywpHelper::error_not_found_message( 'user_id' , $called_text );

      return false;

    }

    if( $notices === false ) {

      MywpHelper::error_not_found_message( '$notices' , $called_text );

      return false;

    }

    update_user_meta( $this->user_id , $this->notice_key , $notices );

    return true;

  }

  public function delete_notice() {

    if( $this->user_id === false ) {

      $called_text = sprintf( '(object) %s->%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( 'user_id' , $called_text );

      return false;

    }

    update_user_meta( $this->user_id , $this->notice_key , false );

  }

}

endif;
