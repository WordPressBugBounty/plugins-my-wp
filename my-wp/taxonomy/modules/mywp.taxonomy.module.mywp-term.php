<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpTaxonomyAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpTaxonomyModuleMywpTerm' ) ) :

final class MywpTaxonomyModuleMywpTerm extends MywpTaxonomyAbstractModule {

  protected static $id = 'mywp_term';

  private static $post_types = array( 'mywp_admin_sidebar' , 'mywp_admin_toolbar' , 'mywp_frontend_toolbar' );

  protected static function get_regist_taxonomy_type_args() {

    $args = array(
      'post_type' => self::$post_types,
      'label' => 'My WP Term',
    );

    return $args;

  }

  public static function current_manage_term_columns( $terms_columns ) {

    if( isset( $terms_columns['description'] ) ) {

      unset( $terms_columns['description'] );

    }

    if( isset( $terms_columns['slug'] ) ) {

      unset( $terms_columns['slug'] );

    }

    if( isset( $terms_columns['posts'] ) ) {

      unset( $terms_columns['posts'] );

    }

    $terms_columns['publish'] = __( 'Publish' );
    $terms_columns['draft'] = __( 'Draft' );

    return $terms_columns;

  }

  private static function get_post_args() {

    $post_args = array(
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => self::$id,
          'field' => 'slug',
          'terms' => 'default',
        ),
      ),
    );

    return $post_args;

  }

  public static function current_manage_terms_custom_columns( $false , $column_name , $term_id ) {

    $term = get_term( $term_id );

    if( empty( $term ) ) {

      return false;

    } elseif( is_wp_error( $term ) ) {

      echo $term->get_error_message();

    }

    if( empty( $_GET['post_type'] ) ) {

      return false;

    }

    $post_type = strip_tags( $_GET['post_type'] );

    $post_args = self::get_post_args();

    $post_args['post_type'] = $post_type;

    if( $column_name === 'publish' ) {

      $current_setting_sidebar_item_posts = MywpPostType::get_posts( $post_args );

      $count = 0;

      if( ! empty( $current_setting_sidebar_item_posts ) ) {

        $count = count( $current_setting_sidebar_item_posts );

      }

      $edit_url = add_query_arg( array( 'taxonomy' => $term->taxonomy , 'term' => $term->slug , 'post_type' => $post_type , 'post_status' => 'publish' ) , admin_url( 'edit.php' ) );

      printf( '<a href="%s">%d</a>' , esc_url( $edit_url ) , $count );

    } elseif( $column_name === 'draft' ) {

      $post_args['post_status'] = 'draft';

      $current_setting_sidebar_item_posts = MywpPostType::get_posts( $post_args );

      $count = 0;

      if( ! empty( $current_setting_sidebar_item_posts ) ) {

        $count = count( $current_setting_sidebar_item_posts );

      }

      $edit_url = add_query_arg( array( 'taxonomy' => $term->taxonomy , 'term' => $term->slug , 'post_type' => $post_type , 'post_status' => 'draft' ) , admin_url( 'edit.php' ) );

      printf( '<a href="%s">%d</a>' , esc_url( $edit_url ) , $count );

    }

  }

}

MywpTaxonomyModuleMywpTerm::init();

endif;
