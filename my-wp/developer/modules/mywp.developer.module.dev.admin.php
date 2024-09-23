<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevAdmin' ) ) :

final class MywpDeveloperModuleDevAdmin extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_admin';

  static protected $priority = 20;

  static private $post_updated_messages = array();

  static private $bulk_post_updated_messages = array();

  protected static function after_init() {

    add_filter( 'post_updated_messages' , array( __CLASS__ , 'post_updated_messages' ) , 1000 );

    add_filter( 'bulk_post_updated_messages' , array( __CLASS__ , 'bulk_post_updated_messages' ) , 1000 );

  }

  public static function post_updated_messages( $messages ) {

    global $typenow;

    if( empty( $typenow ) ) {

      return $messages;

    }

    if( isset( $messages[ $typenow ] ) ) {

      self::$post_updated_messages = $messages[ $typenow ];

    } elseif( isset( $messages['post'] ) ) {

      self::$post_updated_messages = $messages[ 'post' ];

    }

    return $messages;

  }

  public static function bulk_post_updated_messages( $bulk_messages ) {

    global $typenow;

    if( empty( $typenow ) ) {

      return $bulk_messages;

    }

    if( isset( $bulk_messages[ $typenow ] ) ) {

      self::$bulk_post_updated_messages = $bulk_messages[ $typenow ];

    } elseif( isset( $bulk_messages['post'] ) ) {

      self::$bulk_post_updated_messages = $bulk_messages[ 'post' ];

    }

    return $bulk_messages;

  }

  public static function mywp_debug_renders( $debug_renders ) {

    if( ! is_admin() ) {

      return $debug_renders;

    }

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Current Admin Information' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    global $wpdb;
    global $pagenow, $hook_suffix, $plugin_page, $page_hook, $typenow, $taxnow;
    global $post;
    global $user_id;

    $current_screen = false;

    $current_screen_id = false;

    if( function_exists( 'get_current_screen' ) ) {

      $current_screen = get_current_screen();

      if( ! empty( $current_screen->id ) ) {

        $current_screen_id = $current_screen->id;

      }

    }

    $debug_lists = array(
      '$pagenow' => $pagenow,
      '$hook_suffix' => $hook_suffix,
      '$plugin_page' => $plugin_page,
      '$page_hook' => $page_hook,
      '$typenow' => $typenow,
      '$taxnow' => $taxnow,
      '$current_screen' => $current_screen,
    );

    if( in_array( $pagenow , array( 'post.php' , 'post-new.php' ) ) ) {

      $post_id = (int) $post->ID;

      $debug_lists['post_id'] = $post_id;
      $debug_lists['post'] = $post;
      $debug_lists['custom_fields'] = get_post_meta( $post_id );

      $debug_lists['taxonomies'] = array();
      $debug_lists['terms'] = array();

      $post_taxonomies = get_post_taxonomies( $post_id );

      if( ! empty( $post_taxonomies ) ) {

        foreach( $post_taxonomies as $taxonomy_name ) {

          $debug_lists['taxonomies'][ $taxonomy_name ] = false;
          $debug_lists['terms'][ $taxonomy_name ] = false;

          $taxonomy = get_taxonomy( $taxonomy_name );

          $terms = wp_get_post_terms( $post_id , $taxonomy_name );

          if( empty( $taxonomy ) ) {

            continue;

          }

          $debug_lists['taxonomies'][ $taxonomy_name ] = $taxonomy;

          if( ! empty( $terms ) ) {

            $debug_lists['terms'][ $taxonomy_name ] = $terms;

          }

        }

      }

      $debug_lists['post_updated_messages'] = self::$post_updated_messages;

      $debug_lists['get_post( ' . $post_id . ' )'] = get_post( $post_id );
      $debug_lists['MywpPostType::get_post( ' . $post_id . ' )'] = MywpPostType::get_post( $post_id );
      $debug_lists['SELECT * FROM $wpdb->posts WHERE ID = ' . $post_id] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id ) );

    } elseif( in_array( $pagenow , array( 'edit.php' ) ) ) {

      $debug_lists['post_type'] = $typenow;
      $debug_lists['counts'] = wp_count_posts( $typenow );

      $debug_lists['taxonomies'] = array();

      $post_type_taxonomies = get_object_taxonomies( $typenow );

      if( ! empty( $post_type_taxonomies ) ) {

        foreach( $post_type_taxonomies as $taxonomy_name ) {

          $debug_lists['taxonomies'][ $taxonomy_name ] = false;

          $taxonomy = get_taxonomy( $taxonomy_name );

          if( empty( $taxonomy ) ) {

            continue;

          }

          $debug_lists['taxonomies'][ $taxonomy_name ] = $taxonomy;

        }

      }

      if( $typenow === 'page' ) {

        $debug_lists['column_filters'][] = 'manage_pages_columns';

      } else {

        $debug_lists['column_filters'][] = 'manage_posts_columns';

      }

      $debug_lists['column_filters'][] = sprintf( 'manage_%s_posts_columns (manage_{$typenow}_posts_columns)' , $typenow );

      $debug_lists['sortable_column_filter'] = sprintf( 'manage_%s_sortable_columns (manage_{$this->screen->id}_sortable_columns)' , $current_screen_id );

      if( $typenow === 'page' ) {

        $debug_lists['column_content_actions'][] = 'manage_pages_custom_column';

      } else {

        $debug_lists['column_content_actions'][] = 'manage_posts_custom_column';

      }

      $debug_lists['column_content_actions'][] = sprintf( 'manage_%s_posts_custom_column (manage_{$post->post_type}_posts_custom_column)' , $typenow );

      $debug_lists['bulk_action_filter'] = sprintf( 'bulk_actions-%s (bulk_actions-{$this->screen->id})' , $current_screen_id );

      $debug_lists['bulk_post_updated_messages'] = self::$bulk_post_updated_messages;

    } elseif( in_array( $pagenow , array( 'edit-tags.php' ) ) ) {

      $debug_lists['taxonomy'] = $taxnow;
      $debug_lists['count'] = wp_count_terms( $taxnow );

      $debug_lists['column_filters'] = sprintf( 'manage_edit-%s_columns (manage_edit-{$taxnow}_columns)' , $taxnow );

      $debug_lists['sortable_column_filter'] = sprintf( 'manage_%s_sortable_columns (manage_{$this->screen->id}_sortable_columns)' , $current_screen_id );

      $debug_lists['column_content_actions'] = sprintf( 'manage_%s_custom_column' , $taxnow );

      $debug_lists['bulk_action_filter'] = sprintf( 'bulk_actions-%s (bulk_actions-{$this->screen->id})' , $current_screen_id );


    } elseif( in_array( $pagenow , array( 'term.php' ) ) ) {

      $debug_lists['taxonomy'] = $taxnow;
      $debug_lists['post_type'] = $typenow;

      $term_id = absint( $_REQUEST['tag_ID'] );
      $deaub_lists['term_id'] = $term_id;
      $debug_lists['term'] = get_term( $term_id );
      $debug_lists['custom_meta'] = get_term_meta( $term_id );

    } elseif( in_array( $pagenow , array( 'users.php' ) ) ) {

      $debug_lists['count'] = count_users();

    } elseif( in_array( $pagenow , array( 'user-edit.php' , 'profile.php' ) ) ) {

      $debug_lists['user_id'] = $user_id;
      $debug_lists['get_userdata()'] = get_userdata( $user_id );
      $debug_lists['get_user_meta()'] = get_user_meta( $user_id );

    }

    return $debug_lists;

  }

  protected static function mywp_developer_debug() {

    if( ! is_admin() ) {

      return false;

    }

    parent::mywp_developer_debug();

  }

  protected static function mywp_debug_render() {

    if( ! is_admin() ) {

      return false;

    }

    parent::mywp_debug_render();

  }

}

MywpDeveloperModuleDevAdmin::init();

endif;
