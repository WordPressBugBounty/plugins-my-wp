<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleCoreTheme' ) ) :

final class MywpDeveloperModuleCoreTheme extends MywpDeveloperAbstractModule {

  static protected $id = 'core_theme';

  static protected $priority = 60;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'core',
      'title' => __( 'Current Theme' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    if( ! did_action( 'after_setup_theme' ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_after_called_message( 'after_setup_theme' , $called_text );

      return false;

    }

    $debug_lists = array(
      'get_template()' => get_template(),
      'get_template_directory()' => get_template_directory(),
      'get_template_directory_uri()' => get_template_directory_uri(),
      'is_child_theme()' => is_child_theme(),
      'get_stylesheet()' => get_stylesheet(),
      'get_stylesheet_directory()' => get_stylesheet_directory(),
      'get_stylesheet_directory_uri()' => get_stylesheet_directory_uri(),
      'WP_DEFAULT_THEME' => WP_DEFAULT_THEME,
    );

    return $debug_lists;

  }

  protected static function mywp_debug_render() {

    $debug_lists = self::get_debug_lists();

    if( empty( $debug_lists ) ) {

      return false;

    }

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( in_array( $key , array( 'get_template_directory_uri()' , 'get_stylesheet_uri()' ) ) ) {

        echo esc_url( $val );

      } else {

        echo $val;

      }

      echo '</td>';

      echo '</tr>';

    }

    echo '</table>';

  }

}

MywpDeveloperModuleCoreTheme::init();

endif;
