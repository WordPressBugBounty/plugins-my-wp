<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminUsers' ) ) :

final class MywpControllerModuleAdminUsers extends MywpAbstractControllerListModule {

  static protected $id = 'admin_users';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['custom_search_filter'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

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

    add_action( 'load-users.php' , array( __CLASS__ , 'load_users' ) , 1000 );

  }

  public static function load_users() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'users_per_page' , array( __CLASS__ , 'users_per_page' ) );

    add_filter( 'manage_users_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_filter( 'manage_users_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 3 );

    add_filter( 'manage_users_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

    self::custom_search_filter();

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
    body.wp-admin .search-box { display: none; }
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

  public static function users_per_page( $per_page ) {

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

  public static function manage_column_body( $content , $column_id , $user_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $content;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $content;

    }

    $user_data = get_userdata( $user_id );

    if( $column_id === 'mywp_column_id' ) {

      $content = $user_id;

    } elseif( $column_id === 'mywp_column_user_nicename' ) {

      $content = $user_data->user_nicename;

    } elseif( $column_id === 'mywp_column_display_name' ) {

      $content = $user_data->display_name;

    } elseif( $column_id === 'mywp_column_user_registered' ) {

      $content = $user_data->user_registered;

    }

    $called_text = sprintf( '%s::%s( $content , $column_id , $user_id )' , __CLASS__ , __FUNCTION__ );

    if( $column_id === 'id' ) {

      $deprecated_message = '$column_id "id"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      $content = $user_id;

    } else {

      if( empty( $content ) ) {

        $user_meta = get_user_meta( $user_id , $column_id );

        if( ! empty( $user_meta[0] ) ) {

          $deprecated_message = '$column_id "user meta"';

          MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

          if( is_object( $user_meta[0] ) or is_array( $user_meta[0] ) ) {

            $content = print_r( $user_meta[0] , true );

          } else {

            $content = $user_meta[0];

          }

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $content;

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

    add_filter( 'users_list_table_query_args' , array( __CLASS__ , 'users_list_table_query_args' ) );

    add_filter( 'pre_user_query' , array( __CLASS__ , 'pre_user_query' ) );

    add_action( 'pre_get_users' , array( __CLASS__ , 'custom_search_filter_query' ) , 9 );

  }


  public static function users_list_table_query_args( $args ) {

    if( ! isset( $args['mywp_controller_admin_users_custom_search_filter_args'] ) ) {

      $args['mywp_controller_admin_users_custom_search_filter_args'] = true;

    }

    return $args;

  }

  public static function pre_user_query( $query ) {

    global $wpdb;

    if( empty( $query->query_vars['mywp_controller_admin_users_custom_search_filter_args'] ) ) {

      return $query;

    }

    if( ! empty( $query->query_vars['user_login_like'] ) ) {

      $like_string = '%' . $wpdb->esc_like( $query->query_vars['user_login_like'] ) . '%';

      $query->query_where .= $wpdb->prepare( " AND $wpdb->users.user_login LIKE %s" , $like_string );

    }

    if( ! empty( $query->query_vars['user_email_like'] ) ) {

      $like_string = '%' . $wpdb->esc_like( $query->query_vars['user_email_like'] ) . '%';

      $query->query_where .= $wpdb->prepare( " AND $wpdb->users.user_email LIKE %s" , $like_string );

    }

    return $query;

  }

  private static function get_user_roles() {

    $all_user_roles = MywpApi::get_all_user_roles();

    $user_roles = array();

    foreach( $all_user_roles as $user_role_key => $user_role ) {

      $user_roles[ $user_role_key ] = $user_role['name'];

    }

    return $user_roles;

  }

  protected static function custom_search_filter_query_do( $query , $custom_search_filter_requests ) {

    if( empty( $query->query_vars['mywp_controller_admin_users_custom_search_filter_args'] ) ) {

      return false;

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_user_id'] ) ) {

      $user_id = MywpHelper::sanitize_number( $custom_search_filter_requests['mywp_custom_search_user_id'] );

      if( ! empty( $user_id ) ) {

        $query->query_vars['include'] = array( $user_id );

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_user_login'] ) ) {

      $user_login = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_user_login'] );

      if( ! empty( $user_login ) ) {

        $query->query_vars['user_login_like'] = $user_login;

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_user_email'] ) ) {

      $user_email = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_user_email'] );

      if( ! empty( $user_email ) ) {

        $query->query_vars['user_email_like'] = $user_email;

      }

    }

    if( isset( $custom_search_filter_requests['mywp_custom_search_user_role'] ) ) {

      $user_role = MywpHelper::sanitize_text( $custom_search_filter_requests['mywp_custom_search_user_role'] );

      $user_roles = self::get_user_roles();

      if( ! empty( $user_roles[ $user_role ] ) ) {

        $query->query_vars['role'] = $user_role;

      }

    }

    $date_query = array();

    if( isset( $custom_search_filter_requests['mywp_custom_search_user_registered'] ) ) {

      foreach( array( 'from' , 'to' ) as $date_key ) {

        if( empty( $custom_search_filter_requests['mywp_custom_search_user_registered'][ $date_key ] ) ) {

          continue;

        }

        $date = MywpHelper::sanitize_date( $custom_search_filter_requests['mywp_custom_search_user_registered'][ $date_key ] );

        if( empty( $date ) ) {

          continue;

        }

        $date_q = array(
          'column' => 'user_registered',
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

    do_action( 'mywp_controller_admin_users_custom_search_filter' , $query , $custom_search_filter_requests );

  }

  protected static function get_custom_search_filter_fields() {

    $custom_search_filter_fields = array(
      'mywp_custom_search_user_id' => array(
        'id' => 'mywp_custom_search_user_id',
        'title' => __( 'User ID' , 'my-wp' ),
        'type' => 'number',
      ),
      'mywp_custom_search_user_login' => array(
        'id' => 'mywp_custom_search_user_login',
        'title' => __( 'Username' ),
        'type' => 'text',
      ),
      'mywp_custom_search_user_email' => array(
        'id' => 'mywp_custom_search_user_email',
        'title' => __( 'User Email' ),
        'type' => 'text',
      ),
      'mywp_custom_search_user_role' => array(
        'id' => 'mywp_custom_search_user_role',
        'title' => __( 'User Roles' ),
        'type' => 'select',
        'multiple' => false,
      ),
      'mywp_custom_search_user_registered' => array(
        'id' => 'mywp_custom_search_user_registered',
        'title' => __( 'Registered Date' , 'my-wp' ),
        'type' => 'date',
      ),
    );

    $user_roles = self::get_user_roles();

    $custom_search_filter_fields['mywp_custom_search_user_role']['choices'] = $user_roles;

    $custom_search_filter_fields = apply_filters( 'mywp_controller_admin_users_custom_search_filter_fields' , $custom_search_filter_fields );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    return $custom_search_filter_fields;

  }

}

MywpControllerModuleAdminUsers::init();

endif;
