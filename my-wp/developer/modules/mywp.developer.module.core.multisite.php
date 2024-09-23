<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleCoreMultiSite' ) ) :

final class MywpDeveloperModuleCoreMultiSite extends MywpDeveloperAbstractModule {

  static protected $id = 'core_multisite';

  static protected $priority = 50;

  public static function mywp_debug_renders( $debug_renders ) {

    if( ! is_multisite() ) {

      return $debug_renders;

    }

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'core',
      'title' => __( 'Multisite' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    if( ! is_multisite() ) {

      return false;

    }

    $debug_lists = array(
      'get_current_blog_id()' => get_current_blog_id(),
      'is_main_site()' => is_main_site(),
      'get_current_network_id()' => get_current_network_id(),
      'site_url()' => site_url(),
      'network_site_url()' => network_site_url(),
      'network_home_url()' => network_home_url(),
      'network_admin_url()' =>network_admin_url(),
      'user_admin_url()' => user_admin_url(),
      'get_blog_details()' => get_blog_details(),
      'get_network()' => get_network(),
    );

    $defines = array(
      'WP_ALLOW_MULTISITE',
      'SUBDOMAIN_INSTALL',
      'DOMAIN_CURRENT_SITE',
      'PATH_CURRENT_SITE',
      'SITE_ID_CURRENT_SITE',
      'BLOG_ID_CURRENT_SITE',
    );

    foreach( $defines as $define ) {

      $debug_lists[ $define ] = false;

      if( defined( $define ) ) {

        $debug_lists[ $define ] = constant( $define );

      }

    }

    return $debug_lists;

  }

}

MywpDeveloperModuleCoreMultiSite::init();

endif;
