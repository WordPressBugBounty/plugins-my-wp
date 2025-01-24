<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevActions' ) ) :

final class MywpDeveloperModuleDevActions extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_actions';

  static protected $priority = 1000;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Action Fooks' , 'my-wp' ),
    );

    return $debug_renders;

  }

  private static function is_mywp_function( $name ) {

    if( strpos( $name , 'mywp' ) !== false ) {

      return true;

    }

    if( strpos( $name , 'Mywp' ) !== false ) {

      return true;

    }

    return false;

  }

  private static function get_wp_actions() {

    global $wp_actions;

    return $wp_actions;

  }

  protected static function mywp_developer_debug() {

    if( ! MywpDeveloper::is_debug_item( 'debug_action' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $actions = self::get_wp_actions();

    echo '$wp_actions = ' . "\n";

    foreach( $actions as $wp_action => $count ) {

      echo ' - ' . $wp_action . "\n";

      $filter_to_func = MywpDeveloper::get_filter_to_func( $wp_action );

      if( ! empty( $filter_to_func ) ) {

        foreach( $filter_to_func as $func ) {

          echo '  ';
          printf( '(%d) %s' , $func['priority'] , $func['print_format'] );
          echo "\n";

        }

        echo "\n";

      }

    }

  }

  protected static function mywp_debug_render() {

    if( ! MywpDeveloper::is_debug_item( 'debug_action' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $actions = self::get_wp_actions();

    echo '<ol class="core-actions">';

    foreach( $actions as $wp_action => $count ) {

      $add_class = '';

      if( self::is_mywp_function( $wp_action ) ) {

        $add_class = 'mywp-action ';

      }

      echo '<li class="core-action ' . esc_attr( $add_class ) . '">' . $wp_action;

      $filter_to_func = MywpDeveloper::get_filter_to_func( $wp_action );

      if( ! empty( $filter_to_func ) ) {

        echo '<textarea readonly="readonly" class="large-text" style="height: 100px;">';

        foreach( $filter_to_func as $func ) {

          echo esc_html( sprintf( '(%d) %s' , $func['priority'] , $func['print_format'] ) );

          echo "\n";

        }

        echo '</textarea>';

        /*
        echo '<ul class="core-action-filters">';

        foreach( $filter_to_func as $func ) {

          echo '<li class="core-action-filter">';

          printf( '<strong class="priority">(%d)</strong> %s' , $func['priority'] , $func['print_format'] );

          echo '</li>';

        }

        echo '</ul>';
        */

      }

      echo '</li>';

    }

    echo '</ol>';

  }

}

MywpDeveloperModuleDevActions::init();

endif;
