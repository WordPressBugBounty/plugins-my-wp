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

    $actions = self::get_wp_actions();

    echo '<ul class="core-actions">';

    foreach( $actions as $wp_action => $count ) {

      $add_class = '';

      if( self::is_mywp_function( $wp_action ) ) {

        $add_class = 'mywp-action ';

      }

      echo '<li class="core-action ' . esc_attr( $add_class ) . '">' . $wp_action;

      $filter_to_func = MywpDeveloper::get_filter_to_func( $wp_action );

      if( ! empty( $filter_to_func ) ) {

        echo '<ul class="core-action-filters">';

        foreach( $filter_to_func as $func ) {

          echo '<li class="core-action-filter">';

          printf( '<strong class="priority">(%d)</strong> %s' , $func['priority'] , $func['print_format'] );

          echo '</li>';

        }

        echo '</ul>';

      }

      echo '</li>';

    }

    echo '</ul>';

  }

  public static function mywp_debug_render_footer() {

?>
<style>
#mywp-debug .core-actions .core-action.mywp-action {
  background: rgba(255, 150, 0, 0.1);
  color: #AEAEAE;
}
#mywp-debug .core-actions .core-action .priority {
  background: rgba(0, 0, 0, 0.2);
}
</style>
<?php

  }

}

MywpDeveloperModuleDevActions::init();

endif;
