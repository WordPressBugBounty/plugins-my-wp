<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpDeveloperInit' ) ) :

final class MywpDeveloperInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'add_debug_type_core' ) , 20 );
    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'add_debug_type_mywp' ) , 30 );
    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'add_debug_type_dev' ) , 40 );
    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'add_debug_type_custom' ) , 100 );

    add_filter( 'mywp_debug_renders' , array( __CLASS__ , 'add_debug_renders_custom_example' ) );

    add_action( 'mywp_debug_render_custom_example' , array( __CLASS__ , 'mywp_debug_render_custom_example' ) );

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );
    add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );
    add_action( 'login_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );

    add_action( 'admin_footer' , array( __CLASS__ , 'debug_footer_print' ) , 1000 );
    add_action( 'wp_footer' , array( __CLASS__ , 'debug_footer_print' ) , 1000 );
    add_action( 'login_footer' , array( __CLASS__ , 'debug_footer_print' ) , 1000 );

    /*
    add_action( 'shutdown' , array( __CLASS__ , 'debug_footer_print' ) , 1000 );
    */

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'developer/modules/';

    $includes = array(

      'core_environment' => $dir . 'mywp.developer.module.core.environment.php',
      'core_multisite'   => $dir . 'mywp.developer.module.core.multisite.php',
      'core_site'        => $dir . 'mywp.developer.module.core.site.php',
      'core_theme'       => $dir . 'mywp.developer.module.core.theme.php',
      'core_user'        => $dir . 'mywp.developer.module.core.user.php',

      'dev_actions'      => $dir . 'mywp.developer.module.dev.actions.php',
      'dev_admin'        => $dir . 'mywp.developer.module.dev.admin.php',
      'dev_debugtrace'   => $dir . 'mywp.developer.module.dev.debugtrace.php',
      'dev_frontend'     => $dir . 'mywp.developer.module.dev.frontend.php',
      'dev_request'      => $dir . 'mywp.developer.module.dev.request.php',
      'dev_times'        => $dir . 'mywp.developer.module.dev.times.php',

      'mywp_cache'       => $dir . 'mywp.developer.module.mywp.cache.php',
      'mywp_error'       => $dir . 'mywp.developer.module.mywp.error.php',
      'mywp_info'        => $dir . 'mywp.developer.module.mywp.info.php',

    );

    $includes = apply_filters( 'mywp_developer_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_developer_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function add_debug_type_core( $debug_types ) {

    $debug_types['core'] = __( 'Core' , 'my-wp' );

    return $debug_types;

  }

  public static function add_debug_type_mywp( $debug_types ) {

    $debug_types['mywp'] = __( 'My WP' , 'my-wp' );

    return $debug_types;

  }

  public static function add_debug_type_dev( $debug_types ) {

    $debug_types['dev'] = __( 'Dev' , 'my-wp' );

    return $debug_types;

  }

  public static function add_debug_type_custom( $debug_types ) {

    $debug_types['custom'] = __( 'Custom' , 'my-wp' );

    return $debug_types;

  }

  public static function add_debug_renders_custom_example( $debug_renders ) {

    $debug_renders['custom_example'] = array(
      'debug_type' => 'custom',
      'title' => __( 'You can add custom debug panel :-)' , 'my-wp' ),
    );

    return $debug_renders;

  }

  public static function mywp_debug_render_custom_example() {

    printf( '<a href="%s" target="_blank">%s</a>' , esc_url( 'https://mywpcustomize.com/document/mywp-debug-panel-extends/' ) , __( 'Document for Debug panel' , 'my-wp' ) );

  }

  public static function enqueue_scripts() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    wp_register_style( 'mywp_developer' , MywpApi::get_plugin_url( 'css' ) . 'developer.css' , array() , MYWP_VERSION );
    wp_register_script( 'mywp_developer' , MywpApi::get_plugin_url( 'js' ) . 'developer.js' , array( 'jquery' ) , MYWP_VERSION );

    wp_enqueue_style( 'mywp_developer' );
    wp_enqueue_script( 'mywp_developer' );

  }

  public static function debug_footer_print() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    if( MywpHelper::is_doing( 'cron' ) or MywpHelper::is_doing( 'xmlrpc' ) or MywpHelper::is_doing( 'ajax' ) or MywpHelper::is_doing( 'rest' ) ) {

      return false;

    }

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/debug-footer.php' );

  }

}

endif;
