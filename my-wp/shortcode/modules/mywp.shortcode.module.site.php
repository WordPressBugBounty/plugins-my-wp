<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleSite' ) ) :

final class MywpShortcodeModuleSite extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_site';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    if( empty( $atts['field'] ) ) {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $field = strip_tags( $atts['field'] );

    $current_site_id = get_current_blog_id();

    if( empty( $atts['site_id'] ) && empty( $atts['blog_id']) ) {

      $site_id = get_current_blog_id();

    } else {

      if( ! empty( $atts['site_id'] ) ) {

        $site_id = (int) $atts['site_id'];

      } elseif( ! empty( $atts['blog_id'] ) ) {

        $site_id = (int) $atts['blog_id'];

      }

    }

    if( is_multisite() && $site_id !== $current_site_id ) {

      switch_to_blog( $site_id );

    }

    if( $field === 'id' ) {

      $content = $site_id;

    } elseif( $field === 'name' ) {

      $content = get_bloginfo( 'name' );

    } else {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    if( is_multisite() && $site_id !== $current_site_id ) {

      restore_current_blog();

    }

    $content = apply_filters( 'mywp_shortcode_site' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModuleSite::init();

endif;
