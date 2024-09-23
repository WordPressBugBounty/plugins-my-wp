<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingTaxonomy' ) ) :

final class MywpSettingTaxonomy {

  private static $current_taxonomy_id;

  private static $current_taxonomy;

  public static function init() {

    add_action( 'mywp_set_current_setting' , array( __CLASS__ , 'mywp_set_current_setting' ) );

  }

  public static function mywp_set_current_setting() {

    if( ! empty( $_GET['setting_taxonomy'] ) ) {

      self::set_current_taxonomy_id( $_GET['setting_taxonomy'] );

    } else {

      self::set_current_taxonomy_to_default();

    }

  }

  public static function get_setting_taxonomies() {

    $taxonomies = get_taxonomies( array( 'show_ui' => true , 'public' => true ) , 'objects' );

    return apply_filters( 'mywp_setting_taxonomies' , $taxonomies );

  }

  public static function get_setting_taxonomy( $taxonomy = false ) {

    if( empty( $taxonomy ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$taxonomy' );

      MywpHelper::error_require_message( '$taxonomy' , $called_text );

      return false;

    }

    $taxonomies = self::get_setting_taxonomies();

    if( empty( $taxonomies ) or empty( $taxonomies[ $taxonomy ] ) ) {

      return false;

    }

    return $taxonomies[ $taxonomy ];

  }

  public static function set_current_taxonomy_id( $taxonomy = false ) {

    $taxonomy = esc_attr( strip_tags( $taxonomy ) );

    self::$current_taxonomy_id = $taxonomy;

    self::set_current_taxonomy( $taxonomy );

  }

  public static function get_current_taxonomy_id() {

    return self::$current_taxonomy_id;

  }

  private static function set_current_taxonomy( $taxonomy = false ) {

    if( empty( $taxonomy ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$taxonomy' );

      MywpHelper::error_require_message( '$taxonomy' , $called_text );

      return false;

    }

    $taxonomy_object = self::get_setting_taxonomy( $taxonomy );

    if( empty( $taxonomy_object ) ) {

      return false;

    }

    self::$current_taxonomy = $taxonomy_object;

  }

  public static function get_current_taxonomy() {

    return self::$current_taxonomy;

  }

  public static function set_current_taxonomy_to_default() {

    $taxonomies = self::get_setting_taxonomies();

    if( empty( $taxonomies ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ , '$taxonomies' );

      MywpHelper::error_require_message( '$taxonomies' , $called_text );

      return false;

    }

    $current_taxonomy_id = false;

    foreach( $taxonomies as $taxonomy ) {

      $current_taxonomy_id = $taxonomy->name;

      break;

    }

    if( empty( $current_taxonomy_id ) ) {

      return false;

    }

    self::set_current_taxonomy_id( $current_taxonomy_id );

  }

  public static function get_one_term( $taxonomy = false ) {

    if( empty( $taxonomy ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$taxonomy' );

      MywpHelper::error_require_message( '$taxonomy' , $called_text );

      return false;

    }

    $taxonomy = strip_tags( $taxonomy );

    $taxonomy_object = self::get_setting_taxonomy( $taxonomy );

    if( empty( $taxonomy_object ) ) {

      return false;

    }

    $args = array( 'taxonomy' => $taxonomy , 'order' => 'DESC' , 'orderby' => 'term_id' );

    $args = apply_filters( "mywp_setting_get_one_term_args_{$taxonomy}" , $args );

    $terms = get_terms( $args );

    if( ! empty( $terms ) ) {

      $key = key( $terms );

      return $terms[ $key ];

    } else {

      return false;

    }

  }

  public static function get_one_term_archive_link( $taxonomy = false ) {

    $term = self::get_one_term( $taxonomy );

    if( ! empty( $term ) ) {

      return get_term_link( $term );

    }

  }

  public static function get_list_link( $taxonomy = false ) {

    $taxonomy = strip_tags( $taxonomy );

    return add_query_arg( array( 'taxonomy' => $taxonomy ) , admin_url( 'edit-tags.php' ) );

  }

}

MywpSettingTaxonomy::init();

endif;
