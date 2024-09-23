<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleCommentCount' ) ) :

final class MywpShortcodeModuleCommentCount extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_comment_count';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    $status = 'moderated';

    if( ! empty( $atts['status'] ) ) {

      $status = strip_tags( $atts['status'] );

    }

    $comments_count = self::get_comments_count( $status );

    if( ! empty( $atts['tag'] ) ) {

      if( ! empty( $comments_count ) ) {

        $content = sprintf(
          '<span class="awaiting-mod count-%d"><span class="%s-count">%s</span></span>',
          $comments_count,
          $status,
          number_format_i18n( $comments_count )
        );

      }

    } else {

      $content = $comments_count;

    }

    $content = apply_filters( 'mywp_shortcode_comment_count' , $content , $atts );

    return $content;

  }

  private static function get_comments_count( $status = '' ) {

    if( empty( $status ) ) {

      return false;

    }

    $comments_counts = wp_count_comments();

    if( empty( $comments_counts->$status ) ) {

      return 0;

    }

    return (int) $comments_counts->$status;

  }

}

MywpShortcodeModuleCommentCount::init();

endif;
