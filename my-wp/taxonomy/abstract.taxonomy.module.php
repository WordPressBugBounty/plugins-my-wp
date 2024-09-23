<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpTaxonomyAbstractModule' ) ) :

abstract class MywpTaxonomyAbstractModule {

  protected static $id;

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    add_filter( 'mywp_taxonomy_types' , array( $class , 'mywp_taxonomy_types' ) );

    add_filter( "mywp_taxonomy_get_taxonomy_{$class::$id}" , array( $class , 'current_mywp_taxonomy_get_taxonomy' ) );

    add_filter( "manage_edit-{$class::$id}_columns" , array( $class , 'current_manage_term_columns' ) );

    add_filter( "manage_{$class::$id}_custom_column" , array( $class , 'current_manage_terms_custom_columns' ) , 10 , 3 );

  }

  public static function mywp_taxonomy_types( $taxonomy_types ) {

    $class = get_called_class();

    $taxonomy_types[ static::$id ] = static::get_regist_taxonomy_type_args();

    return $taxonomy_types;

  }

  protected static function get_regist_taxonomy_type_args() {

    return array();

  }

  public static function current_mywp_taxonomy_get_taxonomy( $taxonomy ) {

    return $taxonomy;

  }

  public static function current_manage_term_columns( $terms_columns ) {

    return $terms_columns;

  }

  public static function current_manage_terms_custom_columns( $false , $column_name , $term_id ) {}

}

endif;
