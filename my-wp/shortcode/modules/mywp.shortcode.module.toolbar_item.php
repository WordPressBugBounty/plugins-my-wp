<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleToolbarItem' ) ) :

final class MywpShortcodeModuleToolbarItem extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_toolbar_item';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    if( empty( $atts['item'] ) ) {

      return $content;

    }

    if( $atts['item'] === 'search' ) {

      $content  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch">';
      $content .= '<input class="adminbar-input" name="s" id="adminbar-search" type="text" value="" maxlength="150" />';
      $content .= '<label for="adminbar-search" class="screen-reader-text">' . __( 'Search' ) . '</label>';
      $content .= '<input type="submit" class="adminbar-button" value="' . __( 'Search' ) . '"/>';
      $content .= '</form>';

    }

    $content = apply_filters( 'mywp_shortcode_toolbar_item' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModuleToolbarItem::init();

endif;
