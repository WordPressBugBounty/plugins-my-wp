<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevRequest' ) ) :

final class MywpDeveloperModuleDevRequest extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_request';

  static protected $priority = 80;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Request' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    global $wpdb;
    global $wp;
    global $wp_query;
    global $wp_rewrite;
    global $wp_locale;


    $debug_lists = array(
      'DOING_AJAX' => defined( 'DOING_AJAX' ),
      'DOING_CRON' => defined( 'DOING_CRON' ),
      'XMLRPC_REQUEST' => defined( 'XMLRPC_REQUEST' ),
      'REST_REQUEST' => defined( 'REST_REQUEST' ),
      'is_ssl()' => is_ssl(),
      'is_admin()' => is_admin(),
      'is_network_admin()' => is_network_admin(),
      '$_POST' => $_POST,
      '$_GET' => $_GET,
      '$_FILES' => $_FILES,
      '$_COOKIE' => $_COOKIE,
      'parse_url' => parse_url( $_SERVER['REQUEST_URI'] ),
      '$wp' => $wp,
      '$wp_query' => $wp_query,
      '$wp_rewrite' => $wp_rewrite,
      '$wp_locale' => $wp_locale,
    );

    $savequeries = MywpHelper::get_define( 'SAVEQUERIES' );

    if( ! empty( $savequeries ) ) {

      $debug_lists['queries'] = $wpdb->queries;

    } else {

      $debug_lists['not_queries'] = __( 'Require the define( "SAVEQUERIES" , true ).' , 'my-wp' );

    }

    return $debug_lists;

  }

  protected static function mywp_debug_render() {

    if( ! MywpDeveloper::is_debug_item( 'debug_request' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $debug_lists = self::get_debug_lists();

    if( empty( $debug_lists ) ) {

      return false;

    }

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( in_array( $key , array( '$_POST' , '$_GET' , '$_FILES' , '$_COOKIE' , 'parse_url' , 'queries' , '$wp' , '$wp_query' , '$wp_rewrite' , '$wp_locale' ) ) ) {

        if( is_array( $val ) ) {

          printf( 'Count: %s<br />' , number_format( count( $val ) ) );

        }

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

      echo '</tr>';

    }

    echo '</table>';

  }

}

MywpDeveloperModuleDevRequest::init();

endif;
