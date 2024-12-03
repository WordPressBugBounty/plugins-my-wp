<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminUploads' ) ) :

final class MywpControllerModuleAdminUploads extends MywpAbstractControllerListModule {

  static protected $id = 'admin_uploads';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['force_show_list'] = '';
    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['custom_search_filter'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['force_show_list'] = false;
    $default_data['per_page_num'] = '';
    $default_data['hide_add_new'] = false;
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

    add_action( 'load-upload.php' , array( __CLASS__ , 'load_uploads' ) , 1000 );

  }

  public static function load_uploads() {

    add_filter( 'get_user_option_media_library_mode' , array( __CLASS__ , 'force_show_list_user_option' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'force_show_list_buttons' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'request' , array( __CLASS__ , 'sortable_request' ) );

    add_filter( 'posts_orderby',  array( __CLASS__ , 'sortable_posts_orderby' ) );

    add_filter( 'upload_per_page' , array( __CLASS__ , 'upload_per_page' ) );

    add_filter( 'manage_media_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_filter( 'manage_media_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_upload_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

    self::custom_search_filter();

  }

  public static function force_show_list_user_option( $user_option ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $user_option;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['force_show_list'] ) ) {

      return $user_option;

    }

    $user_option = $setting_data['force_show_list'];

    self::after_do_function( __FUNCTION__ );

    return $user_option;

  }

  public static function force_show_list_buttons() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['force_show_list'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .wp-filter .media-toolbar-secondary { margin: 10px 0; }
    body.wp-admin .wp-filter .media-grid-view-switch { display: none; }
    body.wp-admin .wp-filter .filter-items { margin: 10px 0; }
    body.wp-admin .wp-filter .filter-items .view-switch { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

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
    body.wp-admin .search-form,
    body.wp-admin .media-toolbar-primary.search-form { display: none; }
    body.wp-admin #media-search-input { display: none; }
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

  public static function sortable_request( $request ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $request;

    }

    if( empty( $request['orderby'] ) ) {

      return $request;

    }

    if( $request['orderby'] === 'image_alt') {

      $request['meta_key'] = '_wp_attachment_image_alt';
      $request['orderby'] = 'meta_value';

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

  public static function upload_per_page( $per_page ) {

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

    } elseif( $column_id === 'mywp_column_media_title' ) {

      echo _draft_or_post_title( $post_id );

    } elseif( $column_id === 'mywp_column_image_alt' ) {

      $image_alt = get_post_meta( $post_id , '_wp_attachment_image_alt' , true );

      echo wp_strip_all_tags( stripslashes( $image_alt ) );

    } elseif( $column_id === 'mywp_column_post_excerpt' ) {

      $post_excerpt = strip_tags( $post->post_excerpt );

      if( function_exists( 'mb_substr' ) ) {

        echo esc_html( mb_substr( $post_excerpt , 0 , 20 ) );

      } else {

        echo esc_html( substr( $post_excerpt , 0 , 20 ) );

      }

      if( ! empty( $post_excerpt ) ) {

        echo '.';

      }

    } elseif( $column_id === 'mywp_column_post_content' ) {

      $post_content = strip_tags( $post->post_content );

      if( function_exists( 'mb_substr' ) ) {

        echo esc_html( mb_substr( $post_content , 0 , 20 ) );

      } else {

        echo esc_html( substr( $post_content , 0 , 20 ) );

      }

      if( ! empty( $post_content ) ) {

        echo '.';

      }

    } elseif( $column_id === 'mywp_column_file_url' ) {

      printf( '<input type="text" readonly="readonly" value="%s" class="large-text" />' , esc_attr( wp_get_attachment_url( $post_id ) ) );

    }

    $called_text = sprintf( '%s::%s( $content , $column_id , $user_id )' , __CLASS__ , __FUNCTION__ );

    if( $column_id === 'id' ) {

      $deprecated_message = '$column_id "id"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo $post_id;

    } elseif( $column_id === 'media_title' ) {

      $deprecated_message = '$column_id "media_title"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      echo _draft_or_post_title( $post_id );

    } elseif( $column_id === 'image_alt' ) {

      $deprecated_message = '$column_id "image_alt"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      $image_alt = get_post_meta( $post_id , '_wp_attachment_image_alt' , true );

      echo wp_strip_all_tags( stripslashes( $image_alt ) );

    } elseif( $column_id === 'post_excerpt' ) {

      $deprecated_message = '$column_id "post_excerpt"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      if( ! empty( $post->post_excerpt ) ) {

        if( function_exists( 'mb_substr' ) ) {

          echo mb_substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        } else {

          echo substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        }

      }

    } elseif( $column_id === 'post_content' ) {

      $deprecated_message = '$column_id "post_content"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      if( ! empty( $post->post_content ) ) {

        if( function_exists( 'mb_substr' ) ) {

          echo mb_substr( strip_tags( $post->post_content ) , 0 , 20 ) . '.';

        } else {

          echo substr( strip_tags( $post->post_content ) , 0 , 20 ) . '.';

        }

      }

    } elseif( $column_id === 'file_url' ) {

      $deprecated_message = '$column_id "file_url"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      printf( '<input type="text" readonly="readonly" value="%s" class="large-text" />' , esc_url( wp_get_attachment_url( $post_id ) ) );

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

    if( ! empty( $_GET['mode'] ) ) {

      $media_library_mode = MywpHelper::sanitize_text( $_GET['mode'] );

    } else {

      $media_library_mode = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';

    }

    if( $media_library_mode !== 'list' ) {

      remove_action( 'admin_footer' , array( __CLASS__ , 'print_custom_search_filter' ) );

      return false;

    }

    add_action( 'pre_get_posts' , array( __CLASS__ , 'custom_search_filter_query' ) , 9 );

  }

  private static function get_post_statuses() {

    global $wp_post_statuses;

    $post_statuses = array();

    foreach( $wp_post_statuses as $post_status => $wp_post_status ) {

      /*
      if( ! in_array( $post_status , array( 'draft' , 'publish' , 'trash' , 'private' ) ) ) {

        continue;

      }
      */

      $post_statuses[ $post_status ] = $wp_post_status->label;

    }

    return $post_statuses;

  }

  private static function get_post_mime_types() {

    global $wpdb;

    $mywp_cache = new MywpCache( 'MywpControllerModuleAdminUploads_get_post_mime_types' );

    $cache = $mywp_cache->get_cache();

    if( ! empty( $cache ) ) {

      return $cache;

    }

    $post_mime_types = array();

    $results = $wpdb->get_col( "SELECT DISTINCT post_mime_type FROM $wpdb->posts WHERE post_mime_type != ''" );

    if( empty( $results ) ) {

      return $post_mime_types;

    }

    foreach( $results as $mime_type ) {

      $post_mime_types[ $mime_type ] = $mime_type;

    }

    return $post_mime_types;

  }

  protected static function custom_search_filter_query_do( $query , $custom_search_filter_requests ) {

    if( ! $query->is_main_query() ) {

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

      $post_statuses = self::get_post_statuses();

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

    if( isset( $custom_search_filter_requests['mywp_custom_search_post_mime_type'] ) ) {

      $post_mime_type = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_post_mime_type'] );

      $post_mime_types = self::get_post_mime_types();

      if( ! empty( $post_mime_types[ $post_mime_type ] ) ) {

        $query->set( 'post_mime_type' , $post_mime_type );

      }

    }

    do_action( 'mywp_controller_admin_uploads_custom_search_filter' , $query , $custom_search_filter_requests );

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
        'title' => __( 'Post Date' , 'my-wp' ),
        'type' => 'date',
      ),
      'mywp_custom_search_post_parent' => array(
        'id' => 'mywp_custom_search_post_parent',
        'title' => __( 'Post Parent' , 'my-wp' ),
        'type' => 'number',
      ),
      'mywp_custom_search_post_mime_type' => array(
        'id' => 'mywp_custom_search_post_mime_type',
        'title' => __( 'Mime Type' , 'my-wp' ),
        'type' => 'select',
      ),
    );

    $post_statuses = self::get_post_statuses();

    $custom_search_filter_fields['mywp_custom_search_post_status']['choices'] = $post_statuses;

    $post_mime_types = self::get_post_mime_types();

    $custom_search_filter_fields['mywp_custom_search_post_mime_type']['choices'] = $post_mime_types;

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_uploads_custom_search_filter_fields' , $custom_search_filter_fields );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    return $custom_search_filter_fields;

  }

}

MywpControllerModuleAdminUploads::init();

endif;
