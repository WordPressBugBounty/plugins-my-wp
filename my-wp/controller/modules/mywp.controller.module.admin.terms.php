<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminTerms' ) ) :

final class MywpControllerModuleAdminTerms extends MywpControllerAbstractModule {

  static protected $id = 'admin_terms';

  static private $taxonomy = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['per_page_num'] = '';
    $default_data['hide_add_new'] = false;
    $default_data['hide_search_box'] = false;

    return $default_data;

  }

  public static function get_list_column_default() {

    $list_column_default = array(
      'id' => '',
      'sort' => '',
      'orderby' => '',
      'title' => '',
      'width' => '',
    );

    return $list_column_default;

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

    add_action( 'load-edit-tags.php' , array( __CLASS__ , 'load_edit' ) , 1000 );

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$taxonomy ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$taxonomy;

    return $option_key;

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) or $_POST['action'] !== 'inline-save-tax' ) {

      return false;

    }

    if( empty( $_POST['taxonomy'] ) ) {

      return false;

    }

    self::$taxonomy = strip_tags( $_POST['taxonomy'] );

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'manage_edit-' . self::$taxonomy . '_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_action( 'manage_' . self::$taxonomy . '_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 3 );

    add_filter( 'manage_edit-' . self::$taxonomy . '_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function load_edit() {

    global $taxnow;

    if( empty( $taxnow ) ) {

      return false;

    }

    self::$taxonomy = $taxnow;

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( "edit_{$taxnow}_per_page" , array( __CLASS__ , 'edit_per_page' ) );

    add_filter( "manage_edit-{$taxnow}_columns" , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_action( "manage_{$taxnow}_custom_column" , array( __CLASS__ , 'manage_column_body' ) , 10 , 3 );

    add_filter( "manage_edit-{$taxnow}_sortable_columns", array( __CLASS__ , 'manage_columns_sortable' ) );

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
    body.wp-admin .wrap #col-container #col-left { display: none; }
    body.wp-admin .wrap #col-container #col-right { float: none; width: auto; }
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
    body.wp-admin .search-form .search-box { display: none; }
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

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      $columns[ $column_id ] = do_shortcode( $column_setting['title'] );

    }

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function manage_column_body( $content , $column_id , $term_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $content;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $content;

    }

    $term = get_term( $term_id , self::$taxonomy );

    if( $column_id === 'mywp_column_id' ) {

      $content = $term_id;

    } elseif( $column_id === 'mywp_column_parent' ) {

      $content = $term->parent;

    }

    $called_text = sprintf( '%s::%s( $content , $column_id , $term_id )' , __CLASS__ , __FUNCTION__ );

    if( $column_id === 'id' ) {

      $deprecated_message = '$column_id "id"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      $content = $term_id;

    } elseif( $column_id === 'parent' ) {

      $deprecated_message = '$column_id "parent"';

      MywpHelper::error_deprecated_value( $deprecated_message , $called_text , '1.24' );

      $content = $term->parent;

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

}

MywpControllerModuleAdminTerms::init();

endif;
