<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleFrontendAuthorArchive' ) ) :

final class MywpControllerModuleFrontendAuthorArchive extends MywpControllerAbstractModule {

  static protected $id = 'frontend_author_archive';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['disable_archive'] = '';
    $initial_data['disable_archive_add_robots_txt'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['disable_archive'] = false;
    $default_data['disable_archive_add_robots_txt'] = '';

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( is_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'pre_get_posts' , array( __CLASS__ , 'disable_archive' ) );

    add_filter( 'robots_txt' , array( __CLASS__ , 'robots_txt' ) );

  }

  public static function disable_archive( $wp_query ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $wp_query;

    }

    if( ! $wp_query->is_main_query() ) {

      return $wp_query;

    }

    if( ! $wp_query->is_archive() ) {

      return $wp_query;

    }

    if( ! $wp_query->is_author() ) {

      return $wp_query;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['disable_archive'] ) ) {

      return $wp_query;

    }

    $wp_query->set_404();

    self::after_do_function( __FUNCTION__ );

  }

  public static function robots_txt( $robots_txt ) {

    global $wp_rewrite;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $robots_txt;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['disable_archive_add_robots_txt'] ) ) {

      return $robots_txt;

    }

    $author_permalink_structure = $wp_rewrite->get_author_permastruct();

    if( empty( $author_permalink_structure ) ) {

      return $robots_txt;

    }

    $disallow_author_link = str_replace( '%author%' , '' , $author_permalink_structure );

    $home_url = home_url();

    $site_url_parse = parse_url( $home_url );

    $path = '';

    if( ! empty( $site_url_parse['path'] ) ) {

      $path = $site_url_parse['path'];

    }

    $robots_txt .= "Disallow: $path$disallow_author_link\n";

    return $robots_txt;

  }

}

MywpControllerModuleFrontendAuthorArchive::init();

endif;
