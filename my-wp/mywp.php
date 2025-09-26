<?php
/*
Plugin Name: My WP Customize Admin/Frontend
Plugin URI: https://mywpcustomize.com/
Description: My WP is powerful admin and frontend customize and debug and extendable plugin.
Version: 1.27.0
Author: gqevu6bsiz
Author URI: https://mywpcustomize.com/
Text Domain: my-wp
Domain Path: /languages/
Requires at least: 4.7
Requires PHP: 5.6
*/


if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'Mywp' ) ) :

final class Mywp {

  public static function init() {

    self::define_constants();
    self::include_core();
    self::include_classes();

    self::do_action();

    self::start_developer();
    self::start_post_type();
    self::start_taxonomy();
    self::start_shortcode();
    self::start_thirdparty();
    self::start_controller();
    self::start_setting();

  }

  private static function define_constants() {

    define( 'MYWP_NAME' , 'My WP' );
    define( 'MYWP_VERSION' , '1.27.0' );
    define( 'MYWP_PLUGIN_FILE' , __FILE__ );
    define( 'MYWP_PLUGIN_BASENAME' , plugin_basename( MYWP_PLUGIN_FILE ) );
    define( 'MYWP_PLUGIN_DIRNAME' , dirname( MYWP_PLUGIN_BASENAME ) );
    define( 'MYWP_PLUGIN_PATH' , plugin_dir_path( MYWP_PLUGIN_FILE ) );
    define( 'MYWP_PLUGIN_URL' , plugin_dir_url( MYWP_PLUGIN_FILE ) );
    define( 'MYWP_REQUIRED_WP_VERSION' , '4.7' );

  }

  private static function include_core() {

    $dir = MYWP_PLUGIN_PATH . 'core/';

    require_once( $dir . 'class.action.php' );
    require_once( $dir . 'class.api.php' );
    require_once( $dir . 'class.helper.php' );
    require_once( $dir . 'class.imcompatible.php' );
    require_once( $dir . 'class.setup.php' );

  }

  private static function include_classes() {

    $dir = MYWP_PLUGIN_PATH . 'classes/';

    require_once( $dir . 'class.admin-sidebar.php' );
    require_once( $dir . 'class.admin-toolbar.php' );
    require_once( $dir . 'class.frontend-toolbar.php' );
    require_once( $dir . 'class.cache.php' );
    require_once( $dir . 'class.logger.php' );
    require_once( $dir . 'class.model.php' );
    require_once( $dir . 'class.notice.php' );
    require_once( $dir . 'class.transient.php' );
    require_once( $dir . 'class.user.php' );

  }

  private static function do_action() {

    $wp_compare = version_compare( MywpHelper::get_wp_version() , MYWP_REQUIRED_WP_VERSION , '>=' );

    if( ! $wp_compare ) {

      add_action( 'mywp_ready' , array( 'MywpIncompatible' , 'init' ) );

    } else {

      add_action( 'mywp_ready' , array( 'MywpAction' , 'init' ) );

    }

    do_action( 'mywp_ready' );

  }

  private static function start_developer() {

    $dir = MYWP_PLUGIN_PATH . 'developer/';

    require_once( $dir . 'abstract.developer.module.php' );
    require_once( $dir . 'class.developer.php' );
    require_once( $dir . 'developer.init.php' );

    add_action( 'mywp_start' , array( 'MywpDeveloperInit' , 'init' ) );

  }

  private static function start_post_type() {

    $dir = MYWP_PLUGIN_PATH . 'post-type/';

    require_once( $dir . 'abstract.post-type.module.php' );
    require_once( $dir . 'class.post-type.php' );
    require_once( $dir . 'post-type.init.php' );

    add_action( 'mywp_start' , array( 'MywpPostTypeInit' , 'init' ) );

  }

  private static function start_taxonomy() {

    $dir = MYWP_PLUGIN_PATH . 'taxonomy/';

    require_once( $dir . 'abstract.taxonomy.module.php' );
    require_once( $dir . 'class.taxonomy.php' );
    require_once( $dir . 'taxonomy.init.php' );

    add_action( 'mywp_start' , array( 'MywpTaxonomyInit' , 'init' ) );

  }

  private static function start_shortcode() {

    $dir = MYWP_PLUGIN_PATH . 'shortcode/';

    require_once( $dir . 'abstract.shortcode.module.php' );
    require_once( $dir . 'class.shortcode.php' );
    require_once( $dir . 'shortcode.init.php' );

    add_action( 'mywp_start' , array( 'MywpShortcodeInit' , 'init' ) );

  }

  private static function start_thirdparty() {

    $dir = MYWP_PLUGIN_PATH . 'thirdparty/';

    require_once( $dir . 'abstract.thirdparty.module.php' );
    require_once( $dir . 'class.thirdparty.php' );
    require_once( $dir . 'thirdparty.init.php' );

    add_action( 'mywp_start' , array( 'MywpThirdpartyInit' , 'init' ) );

  }

  private static function start_controller() {

    $dir = MYWP_PLUGIN_PATH . 'controller/';

    require_once( $dir . 'abstract.controller.module.php' );
    require_once( $dir . 'abstract.controller.list.module.php' );
    require_once( $dir . 'abstract.controller.toolbar.module.php' );
    require_once( $dir . 'class.controller.php' );
    require_once( $dir . 'controller.init.php' );

    add_action( 'mywp_start' , array( 'MywpControllerInit' , 'init' ) );

  }

  private static function start_setting() {

    $dir = MYWP_PLUGIN_PATH . 'setting/';

    require_once( $dir . 'abstract.setting.module.php' );
    require_once( $dir . 'abstract.setting.columns.module.php' );
    require_once( $dir . 'abstract.setting.toolbar.module.php' );
    require_once( $dir . 'abstract.setting.bulk.module.php' );
    require_once( $dir . 'class.setting.php' );
    require_once( $dir . 'class.setting.menu.php' );
    require_once( $dir . 'class.setting.post.php' );
    require_once( $dir . 'class.setting.post-type.php' );
    require_once( $dir . 'class.setting.taxonomy.php' );
    require_once( $dir . 'class.setting.block-editor.php' );
    require_once( $dir . 'class.setting.meta-box.php' );
    require_once( $dir . 'class.setting.screen.php' );
    require_once( $dir . 'setting.init.php' );

    add_action( 'mywp_start' , array( 'MywpSettingInit' , 'init' ) );

  }

}

Mywp::init();

endif;
