<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpTaxonomyInit' ) ) :

final class MywpTaxonomyInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_action( 'mywp_init' , array( __CLASS__ , 'regist_taxonomy' ) );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'taxonomy/modules/';

    $includes = array(
      'mywp_term' => $dir . 'mywp.taxonomy.module.mywp-term.php',
    );

    $includes = apply_filters( 'mywp_taxonomy_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_taxonomy_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function regist_taxonomy() {

    $taxonomy_types = MywpTaxonomy::get_taxonomy_types();

    if( empty( $taxonomy_types ) ) {

      return false;

    }

    foreach( $taxonomy_types as $taxonomy_name => $args ) {

      if( empty( $args['post_type'] ) ) {

        continue;

      }

      $post_types = false;

      if( ! empty( $args['post_type'] ) ) {

        $post_types = $args['post_type'];
        unset( $args['post_type'] );

      }

      register_taxonomy( $taxonomy_name , $post_types , $args );

    }

  }

}

endif;
