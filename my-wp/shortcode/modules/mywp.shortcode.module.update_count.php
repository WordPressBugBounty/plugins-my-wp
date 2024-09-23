<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleUpdateCount' ) ) :

final class MywpShortcodeModuleUpdateCount extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_update_count';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    $type = 'total';

    if( ! empty( $atts['type'] ) ) {

      $type = strip_tags( $atts['type'] );

    }

    $update_count = self::get_update_count( $type );

    if( empty( $update_count ) ) {

      return $content;

    }

    if( ! empty( $atts['tag'] ) ) {

      $class = 'update';

      if( $type === 'plugins' ) {

        $class = 'plugin';

      } elseif( $type === 'themes' ) {

        $class = 'theme';

      } elseif( $type === 'translations' ) {

        $class = 'translation';

      }

      $content = sprintf(
        '<span class="update-plugins count-%d"><span class="%s-count">%s</span></span>',
        $update_count,
        $class,
        number_format_i18n( $update_count )
      );

    } else {

      $content = $update_count;

    }

    $content = apply_filters( 'mywp_shortcode_update_count' , $content , $atts );

    return $content;

  }

  private static function get_update_count( $type = '' ) {

    if( empty( $type ) ) {

      return 0;

    }

    $type = strip_tags( $type );

    $wp_update_data = wp_get_update_data();

    if( ! isset( $wp_update_data['counts'][ $type ] ) ) {

      MywpHelper::error_not_found_message( $type , sprintf( '[%s] shortcode' , self::$id ) );

      return 0;

    }

    if( empty( $wp_update_data['counts'] ) ) {

      return 0;

    }

    return (int) $wp_update_data['counts'][ $type ];

  }

}

MywpShortcodeModuleUpdateCount::init();

endif;
