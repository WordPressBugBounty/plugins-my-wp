<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpTaxonomy' ) ) :

final class MywpTaxonomy {

  public static function get_taxonomy_types() {

    $pre_taxonomy_types = apply_filters( 'mywp_taxonomy_types' , array() );

    if( empty( $pre_taxonomy_types ) ) {

      return false;

    }

    $capability = MywpApi::get_manager_capability();

    $default = array(
      'public' => false,
      'hierarchical' => false,
      'show_ui' => false,
      'rewrite' => false,
      'query_var' => false,
      'capabilities' => array(
        'manage_terms' => $capability,
        'edit_terms' => $capability,
        'delete_terms' => $capability,
        'assign_terms' => $capability,
      ),
      'post_type' => array(),
    );

    $taxonomy_types = array();

    foreach( $pre_taxonomy_types as $taxonomy_name => $args ) {

      if( empty( $args['labels'] ) ) {

        if( ! empty( $args['label'] ) ) {

          $label = $args['label'];

          $labels_args = array(
            'name' => $label,
            'singular_name' => $label,
          );

          $default['labels'] = $labels_args;

        }

      }

      $taxonomy_types[ $taxonomy_name ] = wp_parse_args( $args , $default );

    }

    return $taxonomy_types;

  }

  public static function get_taxonomies( $args = array() ) {

    $taxonomies = get_taxonomies( $args , 'objects' );

    if( empty( $taxonomies ) ) {

      return false;

    }

    foreach( $taxonomies as $key => $taxonomy ) {

      $taxonomy = apply_filters( 'mywp_taxonomy_get_taxonomy' , $taxonomy , $taxonomy->name );
      $taxonomies[ $key ] = apply_filters( "mywp_taxonomy_get_taxonomy_{$taxonomy->name}" , $taxonomy );

    }

    return $taxonomies;

  }

  public static function get_taxonomy( $taxonomy_name = false ) {

    $taxonomy = get_taxonomy( $taxonomy_name );

    if( empty( $taxonomy ) ) {

      return false;

    }

    $taxonomy = apply_filters( 'mywp_taxonomy_get_taxonomy' , $taxonomy , $taxonomy->name );
    $taxonomy = apply_filters( "mywp_taxonomy_get_taxonomy_{$taxonomy->name}" , $taxonomy );

    return $taxonomy;

  }

  public static function get_terms( $taxonomies = false , $args = array() ) {

    $terms = get_terms( $taxonomies , $args );

    if( empty( $terms ) or is_wp_error( $terms ) ) {

      return false;

    }

    foreach( $terms as $key => $term ) {

      $term = apply_filters( 'mywp_taxonomy_get_terms' , $term , $term->taxonomy );
      $terms[ $key ] = apply_filters( "mywp_taxonomy_get_terms_{$term->taxonomy}" , $term );

    }

    return $terms;

  }

  public static function get_term( $term_id = false , $taxonomy = false ) {

    $term = get_term( $term_id , $taxonomy , 'OBJECT' );

    if( empty( $term ) or is_wp_error( $term )  ) {

      return false;

    }

    $term = apply_filters( 'mywp_post_type_get_post' , $term , $term->taxonomy , $term_id );
    $term = apply_filters( "mywp_post_type_get_post_{$term->taxonomy}" , $term , $term_id );

    return $term;

  }

}

endif;
