<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModulePost' ) ) :

final class MywpShortcodeModulePost extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_post';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    global $post;

    if( empty( $atts['field'] ) ) {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $field = strip_tags( $atts['field'] );

    $post_id = false;

    if( ! empty( $atts['current'] ) ) {

      if( ! empty( $post->ID ) ) {

        $post_id = (int) $post->ID;

      }

    } else {

      if( ! empty( $atts['post_id'] ) ) {

        $post_id = (int) $atts['post_id'];

      }

    }

    if( empty( $post_id ) ) {

      return $content;

    }

    $get_post = get_post( $post_id );

    if( empty( $get_post ) ) {

      return $content;

    }

    if( $field === 'id' or $field === 'ID' ) {

      $content = $post_id;

    } elseif( $field === 'title' ) {

      $content = get_the_title( $post_id );

    } elseif( $field === 'post_type' ) {

      $content = $get_post->post_type;

    } elseif( $field === 'post_status' ) {

      $content = $get_post->post_status;

    } elseif( $field === 'edit_link' ) {

      $content = get_edit_post_link( $post_id );

    } else {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $content = apply_filters( 'mywp_shortcode_post' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModulePost::init();

endif;
