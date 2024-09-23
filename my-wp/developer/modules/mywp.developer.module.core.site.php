<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleCoreSite' ) ) :

final class MywpDeveloperModuleCoreSite extends MywpDeveloperAbstractModule {

  static protected $id = 'core_site';

  static protected $priority = 40;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'core',
      'title' => __( 'Current Site' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    $debug_lists = array(
      'site_url()' => site_url(),
      'get_site_url()' => get_site_url(),
      'home_url()' => home_url(),
      'get_home_url()' => get_home_url(),
      'get_bloginfo( "name" , "display" )' => get_bloginfo( 'name' , 'display' ),
      'admin_url()' => admin_url(),
      'get_admin_url()' => get_admin_url(),
      'self_admin_url()' => self_admin_url(),
      //'get_dashboard_url()' => get_dashboard_url(),
      'content_url()' => content_url(),
      'get_locale()' => get_locale(),
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

      if( in_array( $key , array( 'site_url()' , 'home_url()' ) ) ) {

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

MywpDeveloperModuleCoreSite::init();

endif;
