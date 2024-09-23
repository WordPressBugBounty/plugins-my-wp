<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpShortcode' ) ) :

final class MywpShortcode {

  public static function get_shortcodes() {

    return apply_filters( 'mywp_shortcode' , array() );

  }

}

endif;
