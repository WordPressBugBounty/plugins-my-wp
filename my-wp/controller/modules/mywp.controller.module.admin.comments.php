<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminComments' ) ) :

final class MywpControllerModuleAdminComments extends MywpAbstractControllerListModule {

  static protected $id = 'admin_comments';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['custom_search_filter'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['per_page_num'] = 20;
    $default_data['hide_search_box'] = false;
    $default_data['custom_search_filter'] = false;

    return $default_data;

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

    add_action( 'load-edit-comments.php' , array( __CLASS__ , 'load_comments' ) , 1000 );

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) ) {

      return false;

    }

    $action = strip_tags( $_POST['action'] );

    if( ! in_array( $action , array( 'edit-comment' , 'replyto-comment' ) ) ) {

      return false;

    }

    add_filter( 'manage_edit-comments_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_filter( 'manage_comments_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-comments_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function load_comments() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'comments_per_page' , array( __CLASS__ , 'comments_per_page' ) );

    add_filter( 'manage_edit-comments_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_filter( 'manage_comments_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-comments_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

    self::custom_search_filter();

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
    body.wp-admin #comments-form .search-box { display: none; }
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
      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function comments_per_page( $per_page ) {

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

      $columns[ $column_id ] = wp_kses( $column_setting['title'] , $wp_kses_allowed_html );

    }

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function manage_column_body( $column_id , $comment_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $comment = get_comment( $comment_id );

    if( $column_id === 'mywp_column_id' ) {

      echo esc_html( $comment_id );

    } elseif( $column_id === 'mywp_column_comment_author' ) {

      echo esc_html( $comment->comment_author );

    } elseif( $column_id === 'mywp_column_comment_author_email' ) {

      echo esc_html( $comment->comment_author_email );

    } elseif( $column_id === 'mywp_column_comment_author_url' ) {

      echo esc_html( $comment->comment_author_url );

    }

    $called_text = sprintf( '%s::%s( $content , $column_id , $user_id )' , __CLASS__ , __FUNCTION__ );

    if( $column_id === 'id' ) {

      $deprecated_message = '$column_id "id"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo $comment_id;

    } elseif( $column_id === 'comment_author' ) {

      $deprecated_message = '$column_id "comment_author"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo $comment->comment_author;

    } elseif( $column_id === 'comment_author_email' ) {

      $deprecated_message = '$column_id "comment_author_email"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo $comment->comment_author_email;

    } elseif( $column_id === 'comment_author_url' ) {

      $deprecated_message = '$column_id "comment_author_url"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo $comment->comment_author_url;

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

  protected static function custom_search_filter_do() {

    add_filter( 'comments_list_table_query_args' , array( __CLASS__ , 'comments_list_table_query_args' ) );

    add_filter( 'comments_clauses' , array( __CLASS__ , 'comments_clauses' ) , 10 , 2 );

    add_action( 'pre_get_comments' , array( __CLASS__ , 'custom_search_filter_query' ) , 9 );

  }


  public static function comments_list_table_query_args( $args ) {

    if( ! isset( $args['mywp_controller_admin_comments_custom_search_filter_args'] ) ) {

      $args['mywp_controller_admin_comments_custom_search_filter_args'] = true;

    }

    return $args;

  }

  public static function comments_clauses( $clauses , $query ) {

    global $wpdb;

    if( empty( $query->query_vars['mywp_controller_admin_comments_custom_search_filter_args'] ) ) {

      return $clauses;

    }

    if( ! empty( $query->query_vars['author_email_like'] ) ) {

      $like_string = '%' . $wpdb->esc_like( $query->query_vars['author_email_like'] ) . '%';

      $clauses['where'] .= $wpdb->prepare( " AND $wpdb->comments.comment_author_email LIKE %s" , $like_string );

    }

    return $clauses;

  }

  private static function get_comment_statuses() {

    $comment_statuses = get_comment_statuses();

    return $comment_statuses;

  }

  protected static function custom_search_filter_query_do( $query , $custom_search_filter_requests ) {

    if( empty( $query->query_vars['mywp_controller_admin_comments_custom_search_filter_args'] ) ) {

      return false;

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_id'] ) ) {

      $comment_id = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_comment_id'] );

      if( ! empty( $comment_id ) ) {

        $query->query_vars['comment__in'] = $comment_id;

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_post_id'] ) ) {

      $post_id = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_comment_post_id'] );

      if( ! empty( $post_id ) ) {

        $query->query_vars['post_id'] = $post_id;

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_status'] ) ) {

      $comment_status = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_comment_status'] );

      $comment_statuses = self::get_comment_statuses();

      if( ! empty( $comment_statuses[ $comment_status ] ) ) {

        $query->query_vars['status'] = $comment_status;

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_author_email'] ) ) {

      $author_email = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_comment_author_email'] );

      if( ! empty( $author_email ) ) {

        $query->query_vars['author_email_like'] = $author_email;

      }

    }

    $date_query = array();

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_date'] ) ) {

      foreach( array( 'from' , 'to' ) as $date_key ) {

        if( empty( $custom_search_filter_requests['mywp_custom_search_comment_date'][ $date_key ] ) ) {

          continue;

        }

        $date = MywpHelper::sanitize_date( $custom_search_filter_requests['mywp_custom_search_comment_date'][ $date_key ] );

        if( empty( $date ) ) {

          continue;

        }

        $date_q = array(
          'column' => 'comment_date',
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

      $query->query_vars['date_query'] = $date_query;

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_comment_parent'] ) ) {

      $parent_id = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_comment_parent'] );

      if( $parent_id !== false ) {

        $query->query_vars['parent'] = $parent_id;

      }

    }

    do_action( 'mywp_controller_admin_comments_custom_search_filter' , $query , $custom_search_filter_requests );

  }

  protected static function get_custom_search_filter_fields() {

    $custom_search_filter_fields = array(
      'mywp_custom_search_comment_id' => array(
        'id' => 'mywp_custom_search_comment_id',
        'title' => 'ID',
        'type' => 'number',
      ),
      'mywp_custom_search_comment_post_id' => array(
        'id' => 'mywp_custom_search_comment_post_id',
        'title' => __( 'Comment Post ID' , 'my-wp' ),
        'type' => 'number',
      ),
      'mywp_custom_search_comment_status' => array(
        'id' => 'mywp_custom_search_comment_status',
        'title' => __( 'Comment Status' , 'my-wp' ),
        'type' => 'select',
        'multiple' => false,
      ),
      'mywp_custom_search_comment_author_email' => array(
        'id' => 'mywp_custom_search_comment_author_email',
        'title' => __( 'Comment Author Email' , 'my-wp' ),
        'type' => 'text',
      ),
      'mywp_custom_search_comment_date' => array(
        'id' => 'mywp_custom_search_comment_date',
        'title' => __( 'Comment Date' , 'my-wp' ),
        'type' => 'date',
      ),
      'mywp_custom_search_comment_parent' => array(
        'id' => 'mywp_custom_search_comment_parent',
        'title' => __( 'Comment Parent' , 'my-wp' ),
        'type' => 'number',
      ),
    );

    $comment_statuses = self::get_comment_statuses();

    $custom_search_filter_fields['mywp_custom_search_comment_status']['choices'] = $comment_statuses;

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_comments_custom_search_filter_fields' , $custom_search_filter_fields );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    return $custom_search_filter_fields;

  }

}

MywpControllerModuleAdminComments::init();

endif;
