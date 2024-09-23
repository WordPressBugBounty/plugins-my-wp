<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleDebugGeneral' ) ) :

final class MywpControllerModuleDebugGeneral extends MywpControllerAbstractModule {

  static protected $id = 'debug_general';

  static protected $is_do_controller = true;

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['users'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['users'] = array();

    return $default_data;

  }

  protected static function after_init() {

    add_filter( 'mywp_is_debug' , array( __CLASS__ , 'mywp_is_debug' ) , 9 );

    add_action( 'after_setup_theme' , array( __CLASS__ , 'after_setup_theme' ) , 49 );

  }

  public static function mywp_wp_loaded() {

    add_action( 'mywp_request_admin' , array( __CLASS__ , 'mywp_request_admin' ) , 49 );
    add_action( 'mywp_request_frontend' , array( __CLASS__ , 'mywp_request_frontend' ) , 49 );
    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 49 );

  }

  public static function mywp_is_debug( $is_debug ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['users'] ) ) {

      return false;

    }

    $user_id = get_current_user_id();

    if( empty( $user_id ) ) {

      return false;

    }

    if( in_array( $user_id , $setting_data['users'] ) ) {

      return true;

    }

    self::after_do_function( __FUNCTION__ );

    return false;

  }

  public static function after_setup_theme() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    add_filter( 'mywp_post_types' , array( __CLASS__ , 'mywp_post_types' ) , 49 );
    add_filter( 'mywp_taxonomy_types' , array( __CLASS__ , 'mywp_taxonomy_types' ) , 49 );

    add_filter( 'mywp_debug_renders' , array( __CLASS__ , 'mywp_debug_renders' ) , 11 );
    add_action( 'mywp_debug_render_shortcode' , array( __CLASS__ , 'mywp_debug_render_shortcode' ) , 11 );
    add_action( 'mywp_debug_render_post_type' , array( __CLASS__ , 'mywp_debug_render_post_type' ) , 11 );
    add_action( 'mywp_debug_render_taxonomy' , array( __CLASS__ , 'mywp_debug_render_taxonomy' ) , 11 );
    add_action( 'mywp_debug_render_controller' , array( __CLASS__ , 'mywp_debug_render_controller' ) , 11 );
    add_action( 'mywp_debug_render_current_setting' , array( __CLASS__ , 'mywp_debug_render_current_setting' ) , 11 );
    add_action( 'mywp_debug_render_settings' , array( __CLASS__ , 'mywp_debug_render_settings' ) , 11 );
    add_action( 'mywp_debug_render_thirdparty' , array( __CLASS__ , 'mywp_debug_render_thirdparty' ) , 11 );

  }

  public static function mywp_post_types( $post_types ) {

    foreach( $post_types as $post_type_name => $post_type_setting ) {

      $post_types[ $post_type_name ]['show_ui'] = true;
      $post_types[ $post_type_name ]['delete_with_user'] = true;

    }

    return $post_types;

  }

  public static function mywp_taxonomy_types( $taxonomy_types ) {

    foreach( $taxonomy_types as $taxonomy_name => $taxonomy_setting ) {

      $taxonomy_types[ $taxonomy_name ]['show_ui'] = true;

    }

    return $taxonomy_types;

  }

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders['shortcode'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'My WP Shortcode' , 'my-wp' ),
    );

    $debug_renders['post_type'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'My WP Post_Type' , 'my-wp' ),
    );

    $debug_renders['taxonomy'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'My WP Taxonomy' , 'my-wp' ),
    );

    $debug_renders['current_setting'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'Current Setting' , 'my-wp' ),
    );

    $debug_renders['settings'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'Settings' , 'my-wp' ),
    );

    $debug_renders['controller'] = array(
      'debug_type' => 'mywp',
      'title' => __( 'Controller' , 'my-wp' ),
    );

    $debug_renders['thirdparty'] = array(
      'debug_type' => 'dev',
      'title' => __( 'Thirdparty' , 'my-wp' ),
    );

    return $debug_renders;

  }

  public static function mywp_debug_render_shortcode() {

    echo '<ul>';

    $shortcodes = MywpShortcode::get_shortcodes();

    if( ! empty( $shortcodes ) ) {

      foreach( $shortcodes as $shortcode => $function ) {

        printf( '<li>%s</li>' , $shortcode );

      }

    }

    echo '</ul>';

  }

  public static function mywp_debug_render_post_type() {

    echo '<ul>';

    $post_types = MywpPostType::get_post_types();

    if( ! empty( $post_types ) ) {

      foreach( $post_types as $post_type_name => $args ) {

        printf( '<li>%s <textarea readonly="readonly">%s</textarea>' , $post_type_name , print_r( $args , true ) );

      }

    }

    echo '</ul>';

  }

  public static function mywp_debug_render_taxonomy() {

    echo '<ul>';

    $taxonomy_types = MywpTaxonomy::get_taxonomy_types();

    if( ! empty( $taxonomy_types ) ) {

      foreach( $taxonomy_types as $taxonomy_name => $args ) {

        printf( '<li>%s <textarea readonly="readonly">%s</textarea>' , $taxonomy_name , print_r( $args , true ) );

      }

    }

    echo '</ul>';

  }

  public static function mywp_debug_render_controller() {

    echo '<ul>';

    $controllers = MywpController::get_controllers();

    if( ! empty( $controllers ) ) {

      foreach( $controllers as $controller_id => $controller ) {

        if( ! empty( $controller['model'] ) && is_object( $controller['model'] ) ) {

          $controller['model']->get_setting_data();

        }

        printf( '<li>%s <textarea readonly="readonly">%s</textarea></li>' , $controller_id , print_r( $controller , true ) );

      }

    }

    echo '</ul>';

  }

  public static function mywp_debug_render_current_setting() {

    $current_setting_menu = MywpSettingMenu::get_current_menu();

    if( ! empty( $current_setting_menu ) ) {

      printf( '<p>Current Setting Menu = <textarea readonly="readonly">%s</textarea></p>' , print_r( $current_setting_menu , true ) );

    }

    $current_setting_screen = MywpSettingScreen::get_current_screen();

    if( ! empty( $current_setting_screen ) ) {

      printf( '<p>Current Setting Screen = <textarea readonly="readonly">%s</textarea></p>' , print_r( $current_setting_screen , true ) );

    }

    $mywp_model = false;

    if( ! empty( $current_setting_screen['id'] ) ) {

      $mywp_model = MywpSetting::get_model( $current_setting_screen['id'] );

    }

    printf( '<p>Current Setting Screen Model = <textarea readonly="readonly">%s</textarea></p>' , print_r( $mywp_model , true ) );

    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( ! empty( $current_setting_post_type ) ) {

      printf( '<p>Current Setting Post Type = <textarea readonly="readonly">%s</textarea></p>' , print_r( $current_setting_post_type , true ) );

    }

    $current_setting_taxonomy = MywpSettingTaxonomy::get_current_taxonomy();

    if( ! empty( $current_setting_taxonomy ) ) {

      printf( '<p>Current Setting Taxonomy = <textarea readonly="readonly">%s</textarea></p>' , print_r( $current_setting_taxonomy , true ) );

    }

    $current_menu_id = MywpSettingMenu::get_current_menu_id();

    if( ! empty( $current_menu_id ) ) {

      $current_setting_screens = MywpSettingScreen::get_setting_screens_by_menu_id( MywpSettingMenu::get_current_menu_id() );

      if( ! empty( $current_setting_screens ) ) {

        printf( '<p>Current Setting Screens = <textarea readonly="readonly">%s</textarea></p>' , print_r( $current_setting_screens , true ) );

      }

    }

  }

  public static function mywp_debug_render_settings() {

    $setting_menus = MywpSettingMenu::get_setting_menus();

    echo 'Setting Menus';

    if( ! empty( $setting_menus ) ) {

      printf( '<textarea readonly="readonly">%s</textarea>' , print_r( $setting_menus , true ) );

    }

    $menu_hook_names = MywpSettingMenu::get_menu_hook_names();

    echo 'Menu Hook Names';

    if( ! empty( $menu_hook_names ) ) {

      printf( '<textarea readonly="readonly">%s</textarea>' , print_r( $menu_hook_names , true ) );

    }

    $setting_screens = MywpSettingScreen::get_setting_screens();

    echo 'Setting Screens';

    if( ! empty( $setting_screens ) ) {

      printf( '<textarea readonly="readonly">%s</textarea>' , print_r( $setting_screens , true ) );

    }

    $setting_post_types = MywpSettingPostType::get_setting_post_types();

    echo 'Setting Post Types';

    if( ! empty( $setting_post_types ) ) {

      printf( '<textarea readonly="readonly">%s</textarea>' , print_r( $setting_post_types , true ) );

    }

    $setting_taxonomies = MywpSettingTaxonomy::get_setting_taxonomies();

    echo 'Setting Taxonomies';

    if( ! empty( $setting_taxonomies ) ) {

      printf( '<textarea readonly="readonly">%s</textarea>' , print_r( $setting_taxonomies , true ) );

    }

  }

  public static function mywp_debug_render_thirdparty() {

    $thirdparties = MywpThirdparty::get_plugins( true );

    if( ! empty( $thirdparties ) ) {

      echo '<table class="debug-table">';

      foreach( $thirdparties as $plugin_base_name => $plugin ) {

        echo '<tr>';

        if( $plugin['activate']  ) {

          printf( '<th>%s (%s)</th>' , $plugin['plugin_name'] , $plugin['plugin_data']['Version'] );

        } else {

          printf( '<th>%s</th>' , $plugin['plugin_name'] );

        }

        echo '<td>';

        if( $plugin['activate']  ) {

          printf( '<strong>%s</strong>' , __( 'Activated' , 'my-wp' ) );

        } else {

          _e( 'Not Activated' , 'my-wp' );

        }

        echo '<br />';

        printf( __( 'plugin base name: %s' , 'my-wp' ) . '<br />' , $plugin['plugin_base_name'] );

        if( $plugin['activate']  ) {

          printf( '<pre>%s</pre>' , print_r( $plugin['plugin_data'] , true ) );

        }
        echo '</td>';

        echo '</tr>';

      }

      echo '</table>';

    }

  }

  public static function mywp_request_admin() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    add_filter( 'current_edit_per_page' , array( __CLASS__ , 'current_edit_per_page' ) );

    add_action( 'mywp_setting_admin_sidebar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );
    add_action( 'mywp_setting_admin_toolbar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );
    add_action( 'mywp_setting_frontend_toolbar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );

  }

  public static function mywp_request_frontend() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

  }

  public static function mywp_ajax() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    add_action( 'mywp_setting_admin_sidebar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );
    add_action( 'mywp_setting_admin_toolbar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );
    add_action( 'mywp_setting_frontend_toolbar_print_item_header_pre_add_title' , array( __CLASS__ , 'mywp_setting_print_item_header_pre_add_title' ) , 10 , 2 );

  }

  public static function current_edit_per_page( $per_page ) {

    $per_page = 200;

    return $per_page;

  }

  public static function mywp_setting_print_item_header_pre_add_title( $item ) {

    echo sprintf( '[%d]' , $item->ID );

  }

}

MywpControllerModuleDebugGeneral::init();

endif;
