<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminDashboard' ) ) :

final class MywpControllerModuleAdminDashboard extends MywpControllerAbstractModule {

  static protected $id = 'admin_dashboard';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['meta_boxes'] = array();

    $initial_data['hide_welcome_panel'] = '';
    $initial_data['prevent_meta_box'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['meta_boxes'] = array();

    $default_data['hide_welcome_panel'] = false;
    $default_data['prevent_meta_box'] = false;

    return $default_data;

  }

  public static function get_meta_box_default() {

    $meta_box_default = array(
      'action' => '',
      'title' => ''
    );

    return $meta_box_default;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'load-index.php' , array( __CLASS__ , 'load_index' ) , 1000 );

  }

  public static function load_index() {

    add_action( 'admin_head' , array( __CLASS__ , 'hide_welcome_panel' ) );

    add_action( 'admin_head' , array( __CLASS__ , 'hide_meta_boxes' ) );

    add_action( 'in_admin_header' , array( __CLASS__ , 'remove_meta_boxes' ) );

    add_action( 'in_admin_header' , array( __CLASS__ , 'change_title_meta_boxes' ) );

    add_action( 'admin_print_footer_scripts' , array( __CLASS__ , 'prevent_meta_boxes' ) );

  }

  public static function hide_welcome_panel() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_welcome_panel'] ) ) {

      return false;

    }

    ?>
    <style>
    #welcome-panel { display: none; }
    </style>
    <script>
    jQuery(function( $ ) {

      $('#screen-options-wrap .metabox-prefs label[for=wp_welcome_panel-hide]').css('display', 'none');

    });
    </script>
    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_meta_boxes() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $hide_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( $meta_box_setting['action'] !== 'hide' ) {

        continue;

      }

      $hide_meta_boxes[] = $meta_box_id;

    }

    if( empty( $hide_meta_boxes ) ) {

      return false;

    }

    echo '<style>';

    foreach( $hide_meta_boxes as $meta_box_id ) {

      printf( '.postbox#%s { height: 0; overflow: hidden; margin: 0; box-shadow: none; border: 0 none; }' , $meta_box_id );

    }

    echo '</style>';

    echo '<script>jQuery(function( $ ) {';

    foreach( $hide_meta_boxes as $meta_box_id ) {

      printf( '$("#screen-options-wrap .metabox-prefs label[for=%s-hide]").css("display", "none");' , $meta_box_id );

    }

    echo '});</script>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function remove_meta_boxes() {

    global $wp_meta_boxes;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $remove_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( $meta_box_setting['action'] !== 'remove' ) {

        continue;

      }

      $remove_meta_boxes[] = $meta_box_id;

    }

    if( empty( $remove_meta_boxes ) ) {

      return false;

    }

    if( empty( $wp_meta_boxes['dashboard'] ) ) {

      return false;

    }

    $current_meta_boxes = $wp_meta_boxes['dashboard'];

    foreach( $current_meta_boxes as $context => $priority_meta_boxes ) {

      if( empty( $priority_meta_boxes ) or ! is_array( $priority_meta_boxes ) ) {

        continue;

      }

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        if( empty( $meta_boxes ) or ! is_array( $meta_boxes ) ) {

          continue;

        }

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( empty( $meta_box ) or ! is_array( $meta_box ) ) {

            continue;

          }

          if( ! in_array( $meta_box_id , $remove_meta_boxes ) ) {

            continue;

          }

          remove_meta_box( $meta_box_id , 'dashboard' , $context );

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function change_title_meta_boxes() {

    global $wp_meta_boxes;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $change_title_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( ! empty( $meta_box_setting['action'] ) ) {

        continue;

      }

      if( empty( $meta_box_setting['title'] ) ) {

        continue;

      }

      $change_title_meta_boxes[ $meta_box_id ] = $meta_box_setting['title'];

    }

    if( empty( $change_title_meta_boxes ) ) {

      return false;

    }

    if( empty( $wp_meta_boxes['dashboard'] ) ) {

      return false;

    }

    $current_meta_boxes = $wp_meta_boxes['dashboard'];

    foreach( $current_meta_boxes as $context => $priority_meta_boxes ) {

      if( empty( $priority_meta_boxes ) or ! is_array( $priority_meta_boxes ) ) {

        continue;

      }

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        if( empty( $meta_boxes ) or ! is_array( $meta_boxes ) ) {

          continue;

        }

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( empty( $meta_box ) or ! is_array( $meta_box ) ) {

            continue;

          }

          if( empty( $change_title_meta_boxes[ $meta_box_id ] ) ) {

            continue;

          }

          $wp_meta_boxes['dashboard'][ $context ][ $priority ][ $meta_box_id ]['title'] = do_shortcode( $change_title_meta_boxes[ $meta_box_id ] );

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function prevent_meta_boxes() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['prevent_meta_box'] ) ) {

      return false;

    }

    ?>

    <style>
    .js .postbox .hndle { cursor: pointer; }
    .postbox .handle-order-higher, .postbox .handle-order-lower { display: none; }
    </style>
    <script>
    jQuery(function( $ ) {

      $(".meta-box-sortables").sortable("disable");

      $(".meta-box-sortables").sortable("destroy");

    });
    </script>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminDashboard::init();

endif;
