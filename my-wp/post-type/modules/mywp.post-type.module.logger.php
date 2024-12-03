<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpPostTypeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpPostTypeModuleLogger' ) ) :

final class MywpPostTypeModuleLogger extends MywpPostTypeAbstractModule {

  protected static $id = 'mywp_logger';

  protected static function get_regist_post_type_args() {

    $args = array(
      'label' => 'My WP Logger',
      'supports' => array( 'title' , 'custom-fields' ),
    );

    return $args;

  }

  protected static function current_post_type_request( $request ) {

    if( empty( $request['orderby'] ) ) {

      return $request;

    }

    if( $request['orderby'] === 'blog_id' ) {

      $request['meta_key'] = 'blog_id';
      $request['orderby'] = 'meta_value';

    } elseif( $request['orderby'] === 'user_id' ) {

      $request['meta_key'] = 'user_id';
      $request['orderby'] = 'meta_value';

    } elseif( $request['orderby'] === 'request_uri' ) {

      $request['meta_key'] = 'request_uri';
      $request['orderby'] = 'meta_value';

    }

    return $request;

  }

  protected static function current_manage_posts_sortable_column( $sortables ) {

    $sortables['blog_id'] = 'blog_id';

    $sortables['user_id'] = 'user_id';

    $sortables['request_uri'] = 'request_uri';

    $sortables['log']     = 'log';

    return $sortables;

  }

  public static function current_mywp_post_type_get_post( $post ) {

    $post_id = $post->ID;

    $post->item_parent = $post->post_parent;

    $post->blog_id = (int) MywpPostType::get_post_meta( $post_id , 'blog_id' );

    $post->user_id = (int) MywpPostType::get_post_meta( $post_id , 'user_id' );

    $post->request_uri = strip_tags( MywpPostType::get_post_meta( $post_id , 'request_uri' ) );

    $post->log = ( MywpPostType::get_post_meta( $post_id , 'log' ) );

    return $post;

  }

  public static function current_manage_posts_columns( $posts_columns ) {

    $old_columns = $posts_columns;

    $posts_columns = array();

    $posts_columns['cb']          = $old_columns['cb'];
    $posts_columns['title']       = $old_columns['title'];
    $posts_columns['id']          = 'ID';
    $posts_columns['blog_id']     = 'Blog ID';
    $posts_columns['user_id']     = 'User ID';
    $posts_columns['request_uri'] = 'Request URI';
    $posts_columns['log']         = 'Log';
    $posts_columns['date']        = __( 'Date' );

    return $posts_columns;

  }

  public static function current_manage_posts_custom_column( $column_name , $post_id ) {

    $mywp_post = MywpPostType::get_post( $post_id );

    if( empty( $mywp_post ) ) {

      return false;

    }

    if( $column_name === 'id' ) {

      if( $mywp_post->ID ) {

        echo $mywp_post->ID;

      }

    } elseif( $column_name === 'blog_id' ) {

      if( $mywp_post->blog_id ) {

        echo $mywp_post->blog_id;

      }

    } elseif( $column_name === 'user_id' ) {

      if( $mywp_post->user_id ) {

        echo $mywp_post->user_id;

      }

    } elseif( $column_name === 'request_uri' ) {

      printf( '<input type="text" class="large-text" readonly="readonly" value="%s" />' , esc_attr( $mywp_post->request_uri ) );

    } elseif( $column_name === 'log' ) {

      printf( '<textarea class="large-text" readonly="readonly">%s</textarea>' , esc_textarea( print_r( $mywp_post->log , true ) ) );

    }

  }

}

MywpPostTypeModuleLogger::init();

endif;
