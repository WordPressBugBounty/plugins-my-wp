<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpPostType' ) ) :

final class MywpPostType {

  public static function get_post_types() {

    $pre_post_types = apply_filters( 'mywp_post_types' , array() );

    if( empty( $pre_post_types ) ) {

      return false;

    }

    $cap = MywpApi::get_manager_capability();

    $default = array(
      'public' => false,
      'hierarchical' => false,
      'exclude_from_search' => true,
      'show_ui' => false,
      'query_var' => false,
      'delete_with_user' => false,
      'rewrite' => false,
      'supports' => false,
      'capabilities' => array(
        'edit_post'              => $cap,
        'read_post'              => $cap,
        'delete_post'            => $cap,
        'edit_posts'             => $cap,
        'edit_others_posts'      => $cap,
        'publish_posts'          => $cap,
        'read_private_posts'     => $cap,
        'read'                   => $cap,
        'delete_posts'           => $cap,
        'delete_private_posts'   => $cap,
        'delete_published_posts' => $cap,
        'delete_others_posts'    => $cap,
        'edit_private_posts'     => $cap,
        'edit_published_posts'   => $cap,
        'create_posts'           => $cap,
      ),
    );

    $post_types = array();

    foreach( $pre_post_types as $post_type_name => $args ) {

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

      $default_args = $default;

      if( ! empty( $args['capability_type'] ) ) {

        unset( $default_args['capabilities'] );

      }

      $post_types[ $post_type_name ] = wp_parse_args( $args , $default_args );

    }

    return $post_types;

  }

  public static function get_posts( $args = array() ) {

    $posts = get_posts( $args );

    if( empty( $posts ) ) {

      return false;

    }

    foreach( $posts as $key => $post ) {

      $post = apply_filters( 'mywp_post_type_get_post' , $post , $post->post_type );

      $posts[ $key ] = apply_filters( "mywp_post_type_get_post_{$post->post_type}" , $post );

    }

    return $posts;

  }

  public static function get_post( $post_id = false ) {

    $mywp_cache = new MywpCache( "MywpPostType_get_post_{$post_id}" );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    wp_cache_delete( $post_id , 'post_meta' );

    $post = get_post( $post_id );

    if( empty( $post ) ) {

      return false;

    }

    $post = apply_filters( 'mywp_post_type_get_post' , $post , $post->post_type , $post_id );
    $post = apply_filters( "mywp_post_type_get_post_{$post->post_type}" , $post , $post_id );

    $mywp_cache->update_cache( $post );

    return $post;

  }

  public static function get_post_custom( $post_id = false ) {

    if( empty( $post_id ) ) {

      return false;

    }

    $post_id = (int) $post_id;

    $mywp_cache = new MywpCache( "MywpPostType_get_post_custom_{$post_id}" );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    $all_custom_fields = get_post_custom( $post_id );

    $all_custom_fields = apply_filters( 'mywp_post_type_get_post_custom' , $all_custom_fields , $post_id );
    $all_custom_fields = apply_filters( "mywp_post_type_get_post_custom_{$post_id}" , $all_custom_fields );

    $mywp_cache->update_cache( $all_custom_fields );

    return $all_custom_fields;

  }

  public static function get_post_meta( $post_id = false , $find_meta_key = false ) {

    if( empty( $post_id ) or empty( $find_meta_key ) ) {

      return false;

    }

    $post_id = (int) $post_id;

    $find_meta_key = strip_tags( $find_meta_key );

    $mywp_cache = new MywpCache( "MywpPostType_get_post_meta_{$post_id}_{$find_meta_key}" );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    $all_custom_fields = self::get_post_custom( $post_id );

    if( empty( $all_custom_fields ) or ! is_array( $all_custom_fields ) ) {

      return false;

    }

    $post_meta = false;

    foreach( $all_custom_fields as $custom_meta_key => $custom_meta_value ) {

      if( $find_meta_key !== $custom_meta_key ) {

        continue;

      }

      $post_meta = maybe_unserialize( $custom_meta_value[0] );

      break;

    }

    $post_meta = apply_filters( 'mywp_post_type_get_post_meta' , $post_meta , $post_id , $find_meta_key );
    $post_meta = apply_filters( "mywp_post_type_get_post_meta_{$find_meta_key}" , $post_meta );

    $mywp_cache->update_cache( $post_meta );

    return $post_meta;

  }

  public static function get_post_type_posts_all_custom_fields( $post_type = false ) {

    global $wpdb;

    if( empty( $post_type ) ) {

      return false;

    }

    $post_type = strip_tags( $post_type );

    $mywp_cache = new MywpCache( "MywpPostType_get_post_type_posts_all_custom_fields_{$post_type}" );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    $args = array(
      'fields' => 'ids',
      'posts_per_page' => -1,
      'orderby' => 'ID',
      'order' => 'ASC',
      'post_status' => 'any',
      'post_type' => $post_type
    );

    $post_ids = get_posts( $args );

    if( empty( $post_ids ) ) {

      return false;

    }

    $posts_all_custom_fields = array();

    $exclude_keys = array( 'allorany' , 'hide_on_screen' , '_edit_lock' , '_edit_last' , 'classic-editor-remember' );

    $exclude_keys = apply_filters( 'mywp_post_type_get_post_type_posts_all_custom_fields_exclude_keys' , $exclude_keys );

    $meta_keys = (array) $wpdb->get_col( "SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE post_id IN(" . implode( ',' , $post_ids ) . ")" );

    foreach( $meta_keys as $meta_key ) {

      if( isset( $posts_all_custom_fields[ $meta_key ] ) ) {

        continue;

      }

      if( ! empty( $exclude_keys ) && is_array( $exclude_keys ) ) {

        if( in_array( $meta_key , $exclude_keys ) ) {

          continue;

        }

      }

      $posts_all_custom_fields[ $meta_key ] = 1;

    }

    $posts_all_custom_fields = apply_filters( 'mywp_post_type_get_post_type_posts_all_custom_fields' , $posts_all_custom_fields , $post_type );
    $posts_all_custom_fields = apply_filters( "mywp_post_type_get_post_type_posts_all_custom_fields_{$post_type}" , $posts_all_custom_fields );

    $mywp_cache->update_cache( $posts_all_custom_fields );

    return $posts_all_custom_fields;

  }

}

endif;
