<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminPosts' ) ) :

final class MywpControllerModuleAdminPosts extends MywpAbstractControllerListModule {

  static protected $id = 'admin_posts';

  static private $post_type = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['bulk_post_updated_messages'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['hide_bulk_actions'] = '';
    $initial_data['auto_output_column_body'] = '';
    $initial_data['custom_search_filter'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['bulk_post_updated_messages'] = array();

    $default_data['per_page_num'] = '';
    $default_data['hide_add_new'] = false;
    $default_data['hide_search_box'] = false;
    $default_data['hide_bulk_actions'] = false;
    $default_data['auto_output_column_body'] = false;
    $default_data['custom_search_filter'] = false;

    return $default_data;

  }

  public static function get_bulk_update_messages_default() {

    $bulk_update_messages_default = array(
      'updated' => _n( '%s post updated.', '%s posts updated.', 0 ),
      'locked' => _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', 0 ),
      'deleted' => _n( '%s post permanently deleted.', '%s posts permanently deleted.', 0 ),
      'trashed' => _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', 0 ),
      'untrashed' => _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', 0 ),
    );

    return $bulk_update_messages_default;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 1000 );

    add_action( 'load-edit.php' , array( __CLASS__ , 'load_edit' ) , 1000 );

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$post_type ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$post_type;

    return $option_key;

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) or $_POST['action'] !== 'inline-save' ) {

      return false;

    }

    if( empty( $_POST['screen'] ) ) {

      return false;

    }

    if( empty( $_POST['post_type'] ) ) {

      return false;

    }

    self::$post_type = strip_tags( $_POST['post_type'] );

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'manage_edit-' . self::$post_type . '_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_action( 'manage_' . self::$post_type . '_posts_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-' . self::$post_type . '_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function load_edit() {

    global $typenow;

    if( empty( $typenow ) ) {

      return false;

    }

    self::$post_type = $typenow;

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'bulk_post_updated_messages' , array( __CLASS__ , 'change_bulk_post_updated_messages' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_bulk_actions' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'request' , array( __CLASS__ , 'sortable_request' ) );

    add_filter( 'posts_orderby',  array( __CLASS__ , 'sortable_posts_orderby' ) );

    add_filter( "edit_{$typenow}_per_page" , array( __CLASS__ , 'edit_per_page' ) );

    add_filter( "manage_edit-{$typenow}_columns" , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_action( "manage_{$typenow}_posts_custom_column" , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( "manage_edit-{$typenow}_sortable_columns", array( __CLASS__ , 'manage_columns_sortable' ) );

    self::custom_search_filter();

  }

  public static function change_bulk_post_updated_messages( $bulk_post_updated_messages ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $bulk_post_updated_messages;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['bulk_post_updated_messages'] ) ) {

      return $bulk_post_updated_messages;

    }

    $bulk_post_updated_messages_default = self::get_bulk_update_messages_default();

    foreach( $bulk_post_updated_messages_default as $key => $v ) {

      if( ! empty( $setting_data['bulk_post_updated_messages'][ $key ] ) ) {

        $bulk_post_updated_messages[ self::$post_type ][ $key ] = $setting_data['bulk_post_updated_messages'][ $key ];

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $bulk_post_updated_messages;

  }

  public static function hide_add_new() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_add_new'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .wrap h1 a { display: none; }
    body.wp-admin .wrap .page-title-action { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_search_box() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_search_box'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin #posts-filter .search-box { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_bulk_actions() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_bulk_actions'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin #posts-filter .tablenav .bulkactions { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function change_column_width() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      if( empty( $column_setting['width'] ) ) {

        continue;

      }

      $columns[ $column_id ] = $column_setting['width'];

    }

    if( empty( $columns ) ) {

      return false;

    }

    echo '<style>';

    foreach( $columns as $column_id => $width ) {

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . '.hidden { display: none; }';

    }

    echo '@media screen and (max-width: 782px) {';

    foreach( $columns as $column_id => $width ) {

      if( in_array( $column_id , array( 'cb' , 'title' ) , true ) ) {

        continue;

      }

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { display: none; }';

    }

    echo '}';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function sortable_request( $request ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $request;

    }

    if( empty( $request['orderby'] ) ) {

      return $request;

    }

    if( is_array( $request['orderby'] ) ) {

      return $request;

    }

    if( $request['orderby'] === 'post-thumbnails') {

      $request['meta_key'] = '_thumbnail_id';
      $request['orderby'] = 'meta_value';

    } elseif( ! empty( $request['post_type'] ) ) {

      $posts_all_custom_fields = MywpPostType::get_post_type_posts_all_custom_fields( $request['post_type'] );

      if( isset( $posts_all_custom_fields[ $request['orderby'] ] ) ) {

        $request['meta_key'] = $request['orderby'];
        $request['orderby'] = 'meta_value';

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $request;

  }

  public static function sortable_posts_orderby( $orderby_statement ) {

    global $wpdb;
    global $wp_query;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $orderby_statement;

    }

    if( strpos( $orderby_statement , 'post_date' ) !== false ) {

      $orderby = $wp_query->get( 'orderby' );

      if( empty( $orderby ) ) {

        return $orderby_statement;

      }

      if( $orderby === 'post_excerpt' ) {

        $order = $wp_query->get( 'order' );

        $orderby_statement = sprintf( '%1$s.%2$s %3$s' , esc_sql( $wpdb->posts ) , esc_sql( $orderby ) , esc_sql( $order ) );

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $orderby_statement;

  }

  public static function edit_per_page( $per_page ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $per_page;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['per_page_num'] ) ) {

      return $per_page;

    }

    $per_page = $setting_data['per_page_num'];

    self::after_do_function( __FUNCTION__ );

    return $per_page;

  }

  public static function manage_columns( $columns ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $columns;

    }

    $wp_kses_allowed_html = wp_kses_allowed_html( 'post' );

    $wp_kses_allowed_html['input'] = array(
      'type' => 1,
      'class' => 1,
      'id' => 1,
    );

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      $columns[ $column_id ] = wp_kses( do_shortcode( $column_setting['title'] ) , $wp_kses_allowed_html );

    }

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function manage_column_body( $column_id , $post_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $post = get_post( $post_id );

    if( $column_id === 'mywp_column_id' ) {

      echo esc_html( $post_id );

    } elseif( $column_id === 'mywp_column_parent' ) {

      echo esc_html( $post->post_parent );

    } elseif( $column_id === 'mywp_column_menu_order' ) {

      echo esc_html( $post->menu_order );

    } elseif( $column_id === 'mywp_column_slug' ) {

      echo esc_html( $post->post_name );

    } elseif( $column_id === 'mywp_column_excerpt' ) {

      $post_excerpt = strip_tags( $post->post_excerpt );

      if( function_exists( 'mb_substr' ) ) {

        echo esc_html( mb_substr( $post_excerpt , 0 , 20 ) );

      } else {

        echo esc_html( substr( $post_excerpt , 0 , 20 ) );

      }

      if( ! empty( $post_excerpt ) ) {

        echo '.';

      }

    } elseif( $column_id === 'mywp_column_post_thumbnails' ) {

      if( has_post_thumbnail( $post_id ) ) {

        $thumbnail_id = get_post_thumbnail_id( $post_id );

        $thumbnail = wp_get_attachment_image_src( $thumbnail_id , 'post-thumbnail' , true );

        if( ! empty( $thumbnail[0] ) ) {

          if( current_user_can( 'upload_files' ) ) {

            $thumbnail_edit_link = add_query_arg( array( 'post' => $thumbnail_id , 'action' => 'edit' ) , admin_url( 'post.php' ) );

            printf( '<a href="%s"><img src="%s" style="%s" /></a>' , esc_url( $thumbnail_edit_link ) , esc_url( $thumbnail[0] ) , esc_attr( 'max-width:100%;' ) );

          } else {

            printf( '<img src="%s" style="%s" />' , esc_url( $thumbnail[0] ) , esc_attr( 'max-width:100%;' ) );

          }

        }

      }

    } elseif( strpos( $column_id , 'mywp_taxonomy_column_' ) !== false ) {

      $post_type_taxonomies = MywpTaxonomy::get_taxonomies( array( 'object_type' => array( self::$post_type ) ) );

      $taxonomy = str_replace( 'mywp_taxonomy_column_' , '' , $column_id );

      if( empty( $post_type_taxonomies[ $taxonomy ] ) ) {

        return false;

      }

      $post_terms = wp_get_post_terms( $post_id , $taxonomy , array( 'fields' => 'all' ) );

      if( ! empty( $post_terms ) ) {

        foreach( $post_terms as $post_term ) {

          printf( '<span class="post-term post-term-%d">[%s]</span> ' , esc_attr( $post_term->term_id ) , esc_html( $post_term->name ) );

        }

      }

    } elseif( strpos( $column_id , 'mywp_custom_field_column_' ) !== false ) {

      $custom_field_key = str_replace( 'mywp_custom_field_column_' , '' , $column_id );

      $post_meta = MywpPostType::get_post_meta( $post_id , $custom_field_key );

      if( empty( $post_meta ) ) {

        return false;

      }

      echo '<div>';

      $post_meta_maybe_serialize = maybe_unserialize( $post_meta );

      if( is_array( $post_meta_maybe_serialize ) or is_object( $post_meta_maybe_serialize ) ) {

        printf( '<textarea class="large-text" readonly="readonly">%s</textarea>' , print_r( $post_meta_maybe_serialize , true ) );

      } else {

        $post_meta_maybe_json = json_decode( $post_meta );

        if( ! empty( $post_meta_maybe_json ) && is_object( $post_meta_maybe_json ) ) {

          printf( '<textarea class="large-text" readonly="readonly">%s</textarea>' , print_r( $post_meta_maybe_json , true ) );

        } else {

          echo $post_meta;

        }

      }

      echo '</div>';

    }

    $called_text = sprintf( '%s::%s( $column_id , $post_id )' , __CLASS__ , __FUNCTION__ );

    $default_list_columns = self::get_default_list_colums();

    if( $column_id === 'id' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "id"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        echo $post_id;

      }

    } elseif( $column_id === 'slug' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "slug"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        echo sanitize_title( $post->post_name );

      }

    } elseif( $column_id === 'parent' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "parent"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        echo $post->post_parent;

      }

    } elseif( $column_id === 'post-formats' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "post-formats"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        echo get_post_format_string( get_post_format( $post_id ) );

      }

    } elseif( $column_id === 'excerpt' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "excerpt"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        if( ! empty( $post->post_excerpt ) ) {

          if( function_exists( 'mb_substr' ) ) {

            echo mb_substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

          } else {

            echo substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

          }

        }

      }

    } elseif( $column_id === 'menu_order' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "menu_order"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        echo $post->menu_order;

      }

    } elseif( $column_id === 'post-thumbnails' ) {

      if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

        $deprecated_message = '$column_id "post-thumbnails"';

        MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

        if( ! $setting_data['auto_output_column_body'] ) {

          return false;

        }

        if( has_post_thumbnail( $post_id ) ) {

          $thumbnail_id = get_post_thumbnail_id( $post_id );

          $thumbnail = wp_get_attachment_image_src( $thumbnail_id , 'post-thumbnail' , true );

          printf( '<img src="%s" style="%s" /></a>' , esc_attr( $thumbnail[0] ) , esc_attr( 'max-width:100%;' ) );

        }

      }

    } else {

      if( ! $setting_data['auto_output_column_body'] ) {

        return false;

      }

      $post_type_taxonomies = MywpTaxonomy::get_taxonomies( array( 'object_type' => array( self::$post_type ) ) );

      if( ! empty( $post_type_taxonomies[ $column_id ] ) ) {

        if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

          $deprecated_message = '$column_id "post terms( ' . $column_id . ' )"';

          MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

          $post_terms = wp_get_post_terms( $post_id , $column_id , array( 'fields' => 'all' ) );

          if( ! empty( $post_terms ) ) {

            foreach( $post_terms as $post_term ) {

              printf( '<span class="post-term post-term-%d">[%s]</span> ' , esc_attr( $post_term->term_id ) , $post_term->name );

            }

          }

        }

      } else {

        $post_meta = MywpPostType::get_post_meta( $post_id , $column_id );

        if( ! empty( $post_meta ) ) {

          if( empty( $default_list_columns['columns'][ $column_id ] ) ) {

            $deprecated_message = '$column_id "post meta( ' . $column_id . ' )"';

            MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

            echo '<div>';

            $post_meta_maybe_serialize = maybe_unserialize( $post_meta );

            if( is_array( $post_meta_maybe_serialize ) or is_object( $post_meta_maybe_serialize ) ) {

              print_r( $post_meta_maybe_serialize );

            } else {

              $post_meta_maybe_json = json_decode( $post_meta );

              if( ! empty( $post_meta_maybe_json ) && is_object( $post_meta_maybe_json ) ) {

                print_r( $post_meta_maybe_json );

              } else {

                echo $post_meta;

              }

            }

            echo '</div>';

          }

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function manage_columns_sortable( $sortables ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $sortables;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $sortables;

    }

    $sortables = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      if( ! empty( $column_setting['sort'] ) ) {

        $sortables[ $column_id ] = $column_setting['orderby'];

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $sortables;

  }

  private static function get_default_list_colums() {

    $mywp_cache = new MywpCache( 'MywpControllerModuleAdminPosts_get_default_list_columns_' . self::$post_type );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    $default_list_columns = array();

    $mywp_controller = MywpController::get_controller( 'admin_regist_list_columns' );

    if( empty( $mywp_controller['model'] ) ) {

      return false;

    }

    $option = $mywp_controller['model']->get_option();

    if( empty( $option['regist_columns'][ self::$post_type ] ) ) {

      return false;

    }

    $default_list_columns = $option['regist_columns'][ self::$post_type ];

    $mywp_cache->update_cache( $default_list_columns );

    return $default_list_columns;

  }

  protected static function custom_search_filter_do() {

    add_action( 'pre_get_posts' , array( __CLASS__ , 'custom_search_filter_query' ) , 9 );

    add_action( 'mywp_controller_admin_posts_custom_search_filter_form' , array( __CLASS__ , 'form_in_post_type' ) );

  }

  private static function get_post_statuses( $post_type = false ) {

    global $wp_post_statuses;

    $post_statuses = array();

    $post_type = MywpHelper::sanitize_text( $post_type );

    if( empty( $post_type ) ) {

      return $post_statuses;

    }

    foreach( $wp_post_statuses as $post_status => $wp_post_status ) {

      if( in_array( $post_status , array( 'auto-draft' ) ) ) {

        continue;

      }

      $post_statuses[ $post_status ] = $wp_post_status->label;

    }

    $post_statuses = apply_filters( 'mywp_controller_admin_posts_get_post_statuses' , $post_statuses , $post_type );

    $post_statuses = apply_filters( 'mywp_controller_admin_posts_get_post_statuses-' . $post_type , $post_statuses );

    return $post_statuses;

  }

  protected static function custom_search_filter_query_do( $query , $custom_search_filter_requests ) {

    if( ! $query->is_main_query() ) {

      return false;

    }

    if( empty( $_REQUEST['post_type'] ) ) {

      return false;

    }

    $post_type = MywpHelper::sanitize_text( $_REQUEST['post_type'] );

    if( $post_type !== self::$post_type ) {

      return false;

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_id'] ) ) {

      $post_id = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_id'] );

      if( ! empty( $post_id ) ) {

        $query->set( 'p' , $post_id );

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_post_status'] ) ) {

      $post_status = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_post_status'] );

      $post_statuses = self::get_post_statuses( self::$post_type );

      if( ! empty( $post_statuses[ $post_status ] ) ) {

        $query->set( 'post_status' , $post_status );

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_title'] ) ) {

      $title = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_title'] );

      if( ! empty( $title ) ) {

        $query->set( 's' , $title );

      }

    }

    $date_query = array();

    if( isset( $custom_search_filter_requests['mywp_custom_search_post_date'] ) ) {

      foreach( array( 'from' , 'to' ) as $date_key ) {

        if( empty( $custom_search_filter_requests['mywp_custom_search_post_date'][ $date_key ] ) ) {

          continue;

        }

        $date = MywpHelper::sanitize_date( $custom_search_filter_requests['mywp_custom_search_post_date'][ $date_key ] );

        if( empty( $date ) ) {

          continue;

        }

        $date_q = array(
          'column' => 'post_date',
          'inclusive' => true,
        );

        if( $date_key === 'from' ) {

          $date_q['after'] = $date;

        } elseif( $date_key === 'to' ) {

          $date_q['before'] = $date;

        }

        $date_query[] = $date_q;

      }

    }

    if( ! empty( $date_query ) ) {

      $query->set( 'date_query' , $date_query );

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_post_parent'] ) ) {

      $post_parent = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_post_parent'] );

      if( $post_parent !== false ) {

        $query->set( 'post_parent' , $post_parent );

      }

    }

    $tax_query = array();

    foreach( $custom_search_filter_requests as $custom_search_filter_request_key => $custom_search_filter_request_value ) {

      if( strpos( $custom_search_filter_request_key , 'mywp_custom_search_taxonomy_' ) === false ) {

        continue;

      }

      $taxonomy_name = str_replace( 'mywp_custom_search_taxonomy_' , '' , $custom_search_filter_request_key );

      $term_ids = MywpHelper::sanitize_term_ids( $taxonomy_name , $custom_search_filter_request_value );

      if( ! empty( $term_ids ) ) {

        $tax_query[] = array(
          'taxonomy' => $taxonomy_name,
          'field' => 'term_id',
          'terms' => $term_ids,
          'operator' => 'IN',
        );

      }

    }

    if( ! empty( $tax_query ) ) {

      $query->set( 'tax_query' , wp_parse_args( $tax_query , array( 'relation' => 'AND' ) ) );

    }

    do_action( 'mywp_controller_admin_posts_custom_search_filter' , $query , $custom_search_filter_requests , self::$post_type );

    do_action( 'mywp_controller_admin_posts_custom_search_filter-' . self::$post_type , $query , $custom_search_filter_requests );

  }

  protected static function get_custom_search_filter_fields() {

    $custom_search_filter_fields = array(
      'mywp_custom_search_id' => array(
        'id' => 'mywp_custom_search_id',
        'title' => 'ID',
        'type' => 'number',
      ),
      'mywp_custom_search_post_status' => array(
        'id' => 'mywp_custom_search_post_status',
        'title' => __( 'Post Status' , 'my-wp' ),
        'type' => 'select',
        'multiple' => false,
      ),
      'mywp_custom_search_title' => array(
        'id' => 'mywp_custom_search_title',
        'title' => __( 'Title' ),
        'type' => 'text',
      ),
      'mywp_custom_search_post_date' => array(
        'id' => 'mywp_custom_search_post_date',
        'title' => __( 'Date' ),
        'type' => 'date',
      ),
      'mywp_custom_search_post_parent' => array(
        'id' => 'mywp_custom_search_post_parent',
        'title' => __( 'Post Parent' , 'my-wp' ),
        'type' => 'number',
      ),
    );

    $post_statuses = self::get_post_statuses( self::$post_type );

    $custom_search_filter_fields['mywp_custom_search_post_status']['choices'] = $post_statuses;

    $taxonomies = get_taxonomies( array( 'object_type' => array( self::$post_type ) ) , 'objects' );

    if( ! empty( $taxonomies ) ) {

      foreach( $taxonomies as $taxonomy ) {

        $terms = get_terms( $taxonomy->name , array( 'hide_empty' => false ) );

        if( empty( $terms ) ) {

          continue;

        }

        if( is_wp_error( $terms ) ) {

          continue;

        }

        $custom_search_filter_fields['mywp_custom_search_taxonomy_' . $taxonomy->name ] = array(
          'id' => 'mywp_custom_search_taxonomy_' . $taxonomy->name,
          'title' => $taxonomy->label,
          'type' => 'checkbox',
          'multiple' => true,
          'choices' => array(),
        );

        foreach( $terms as $term ) {

          $custom_search_filter_fields['mywp_custom_search_taxonomy_' . $taxonomy->name ]['choices'][ $term->term_id ] = $term->name;

        }

      }

    }

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_posts_custom_search_filter_fields-' . self::$post_type , $custom_search_filter_fields );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    return $custom_search_filter_fields;

  }

  protected static function get_custom_search_filter_fields_after( $custom_search_filter_fields , $custom_search_filter_requests ) {

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_posts_custom_search_filter_fields_after' , $custom_search_filter_fields , $custom_search_filter_requests , self::$post_type );

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_posts_custom_search_filter_fields_after-' . self::$post_type , $custom_search_filter_fields , $custom_search_filter_requests );

    return $custom_search_filter_fields;

  }

  public static function form_in_post_type() {

    ?>

    <input type="hidden" name="post_type" value="<?php echo esc_attr( self::$post_type ); ?>">

    <?php

  }

}

MywpControllerModuleAdminPosts::init();

endif;
