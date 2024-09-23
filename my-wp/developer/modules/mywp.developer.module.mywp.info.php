<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleMywpInfo' ) ) :

final class MywpDeveloperModuleMywpInfo extends MywpDeveloperAbstractModule {

  static protected $id = 'mywp_info';

  static protected $priority = 110;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'mywp',
      'title' => MYWP_NAME,
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    $debug_lists = array(
      'manage_capability' => MywpApi::get_manager_capability(),
      'network_manage_capability' => MywpApi::get_network_manager_capability(),
      'is_manager' => MywpApi::is_manager(),
      'is_network_manager' => MywpApi::is_network_manager(),
      'MYWP_VERSION' => MYWP_VERSION,
      'MYWP_PLUGIN_BASENAME' => MYWP_PLUGIN_BASENAME,
      'MYWP_PLUGIN_DIRNAME' => MYWP_PLUGIN_DIRNAME,
      'MYWP_PLUGIN_PATH' => MYWP_PLUGIN_PATH,
      'MYWP_PLUGIN_URL' => esc_url( MYWP_PLUGIN_URL ),
      'MYWP_REQUIRED_WP_VERSION' => MYWP_REQUIRED_WP_VERSION,
    );

    return $debug_lists;

  }

}

MywpDeveloperModuleMywpInfo::init();

endif;
