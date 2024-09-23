<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleTerm' ) ) :

final class MywpShortcodeModuleTerm extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_term';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    if( empty( $atts['field'] ) ) {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $field = strip_tags( $atts['field'] );

    $term_id = false;

    if( ! empty( $atts['term_id'] ) ) {

      $term_id = (int) $atts['term_id'];

    }

    if( empty( $term_id ) ) {

      return $content;

    }

    $get_term = get_term( $term_id );

    if( empty( $get_term ) or is_wp_error( $get_term ) ) {

      return $content;

    }

    if( $field === 'id' or $field === 'ID' or $field === 'term_id' ) {

      $content = $term_id;

    } elseif( $field === 'name' ) {

      $content = $get_term->name;

    } elseif( $field === 'slug' ) {

      $content = $get_term->slug;

    } elseif( $field === 'link' ) {

      $content = get_term_link( $get_term );

    } else {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $content = apply_filters( 'mywp_shortcode_term' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModuleTerm::init();

endif;
