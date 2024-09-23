<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleSiteSitemap' ) ) :

final class MywpControllerModuleSiteSitemap extends MywpControllerAbstractModule {

  static protected $id = 'site_sitemap';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['hide_sitemap'] = false;
    $initial_data['hide_users'] = false;
    $initial_data['hide_post_type'] = array();
    $initial_data['hide_taxonomy'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['hide_sitemap'] = false;
    $default_data['hide_users'] = false;
    $default_data['hide_post_type'] = array();
    $default_data['hide_taxonomy'] = array();

    return $default_data;

  }

  protected static function after_init() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_filter( 'wp_sitemaps_enabled' , array( __CLASS__ , 'wp_sitemaps_enabled' ) );

    add_filter( 'wp_sitemaps_add_provider' , array( __CLASS__ , 'wp_sitemaps_add_provider' ) , 10 , 2 );

    add_filter( 'wp_sitemaps_post_types' , array( __CLASS__ , 'wp_sitemaps_post_types' ) );

    add_filter( 'wp_sitemaps_taxonomies' , array( __CLASS__ , 'wp_sitemaps_taxonomies' ) );

  }

  public static function wp_sitemaps_enabled( $wp_sitemaps_enabled ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $wp_sitemaps_enabled;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_sitemap'] ) ) {

      return $wp_sitemaps_enabled;

    }

    $wp_sitemaps_enabled = false;

    self::after_do_function( __FUNCTION__ );

    return $wp_sitemaps_enabled;

  }

  public static function wp_sitemaps_add_provider( $provider , $name ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $provider;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_users'] ) ) {

      return $provider;

    }

    if( $name === 'users' ) {

      $provider = false;

    }

    self::after_do_function( __FUNCTION__ );

    return $provider;

  }

  public static function wp_sitemaps_post_types( $post_types ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $post_types;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_post_type'] ) ) {

      return $post_types;

    }

    foreach( $post_types as $post_type => $post_type_object ) {

      if( ! empty( $setting_data['hide_post_type'][ $post_type ] ) ) {

        unset( $post_types[ $post_type ] );

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $post_types;

  }

  public static function wp_sitemaps_taxonomies( $taxonomies ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $taxonomies;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_taxonomy'] ) ) {

      return $taxonomies;

    }

    foreach( $taxonomies as $taxonomy => $taxonomy_object ) {

      if( ! empty( $setting_data['hide_taxonomy'][ $taxonomy ] ) ) {

        unset( $taxonomies[ $taxonomy ] );

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $taxonomies;

  }

}

MywpControllerModuleSiteSitemap::init();

endif;
