<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpLogger' ) ) :

final class MywpLogger {

  private $post_type = 'mywp_logger';

  private $id        = false;
  private $blog_id   = false;
  private $user_id   = false;
  private $log       = false;

  public function __construct( $logger_id = false ) {

    if( $logger_id === false ) {

      return false;

    } else {

      $logger_id = (int) $logger_id;

      $post = get_post( $logger_id );

      if( empty( $post ) ) {

        $called_text = sprintf( 'new %s( %s )' , __CLASS__ , '$logger_id' );

        MywpHelper::error_not_found_message( 'get_post( $logger_id )' , $called_text );

        return false;

      }

      if( $post->post_type !== $this->post_type ) {

        $called_text = sprintf( 'new %s( %s )' , __CLASS__ , '$logger_id' );

        MywpHelper::error_require_message( 'post_type is need ' . $this->post_type , $called_text );

        return false;

      }

      $this->id = $logger_id;

    }

  }

  public function get_id() {

    return $this->id;

  }

  public function get_blog_id() {

    if( ! empty( $this->blog_id ) ) {

      return $this->blog_id;

    }

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->blog_id = MywpPostType::get_post_meta( $id , 'blog_id' );

    return $this->blog_id;

  }

  public function get_user_id() {

    if( ! empty( $this->user_id ) ) {

      return $this->user_id;

    }

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->user_id = MywpPostType::get_post_meta( $id , 'user_id' );

    return $this->user_id;

  }

  public function get_log() {

    if( ! empty( $this->log ) ) {

      return $this->log;

    }

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    $this->log = MywpPostType::get_post_meta( $id , 'log' );

    return $this->log;

  }

  public function delete_log() {

    $id = $this->get_id();

    if( empty( $id ) ) {

      return false;

    }

    wp_delete_post( $id , true );

    do_action( 'mywp_logger_deleted_log' , $id );

  }

  public function add_log( $content = array() , $blog_id = false , $user_id = false ) {

    $id = $this->get_id();

    if( ! empty( $id ) ) {

      return false;

    }

    if( empty( $blog_id ) ) {

      $blog_id = get_current_blog_id();

    }

    if( empty( $user_id ) ) {

      $user_id = get_current_user_id();

    }

    $request_uri = false;

    if( ! empty( $_SERVER['REQUEST_URI'] ) ) {

      $request_uri = strip_tags( $_SERVER['REQUEST_URI'] );

    }

    $args = array(
      'post_status' => 'private',
      'post_type' => $this->post_type,
    );

    $post_id = wp_insert_post( $args );

    if( empty( $post_id ) ) {

      return $post_id;

    }

    if( is_wp_error( $post_id ) ) {

      return is_wp_error( $post_id );

    }

    add_post_meta( $post_id , 'log' , $content , true );

    add_post_meta( $post_id , 'blog_id' , $blog_id , true );

    add_post_meta( $post_id , 'user_id' , $user_id , true );

    add_post_meta( $post_id , 'request_uri' , $request_uri , true );

    do_action( 'mywp_logger_added_log' , $content , $post_id );

  }

}

endif;
