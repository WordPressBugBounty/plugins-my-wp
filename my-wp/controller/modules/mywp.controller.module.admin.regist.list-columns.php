<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminRegistListColumns' ) ) :

final class MywpControllerModuleAdminRegistListColumns extends MywpControllerAbstractModule {

  static protected $id = 'admin_regist_list_columns';

  static protected $is_do_controller = true;

  static private $column_type = '';

  static private $sortable_type = '';

  static private $post_type = '';

  static private $taxonomy = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['regist_columns'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['regist_columns'] = array();

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! MywpApi::is_manager() ) {

      return false;

    }

    add_action( 'load-edit.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-edit-tags.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-edit-comments.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-upload.php' , array( __CLASS__ , 'load_screen' ) , 999 );
    add_action( 'load-users.php' , array( __CLASS__ , 'load_screen' ) , 999 );

  }

  public static function load_screen() {

    global $pagenow;
    global $typenow;
    global $taxnow;

    if( empty( $pagenow ) ) {

      return false;

    }

    if( $pagenow === 'edit.php' ) {

      self::$column_type = 'edit-' . $typenow;

      self::$sortable_type = 'edit-' . $typenow;

      self::$post_type = $typenow;

    } elseif( $pagenow === 'edit-tags.php' ) {

      self::$column_type = 'edit-' . $taxnow;

      self::$sortable_type = 'edit-' . $taxnow;

      self::$taxonomy = $taxnow;

    } elseif( $pagenow === 'edit-comments.php' ) {

      self::$column_type = 'edit-comments';

      self::$sortable_type = 'edit-comments';

    } elseif( $pagenow === 'upload.php' ) {

      self::$column_type = 'media';

      self::$sortable_type = 'upload';

    } elseif( $pagenow === 'users.php' ) {

      self::$column_type = 'users';

      self::$sortable_type = 'users';

    }

    if( empty( self::$column_type ) ) {

      return false;

    }

    add_filter( 'manage_' . self::$column_type . '_columns' , array( __CLASS__ , 'registed_columns' ) , 10000 );

    add_filter( 'manage_' . self::$column_type . '_sortable_columns' , array( __CLASS__ , 'registed_sortable_columns' ) , 10000 );

  }

  public static function registed_columns( $columns ) {

    if( empty( self::$column_type ) ) {

      return $columns;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $columns;

    }

    $option = $mywp_model->get_option();

    if( empty( $option ) ) {

      $option = array();

    }

    $column_id = self::$column_type;

    if( ! empty( self::$post_type ) ) {

      $column_id = self::$post_type;

    } elseif( ! empty( self::$taxonomy ) ) {

      $column_id = self::$taxonomy;

    }

    if( empty( $option['regist_columns'][ $column_id ] ) ) {

      $option['regist_columns'][ $column_id ] = array();

    }

    $option['regist_columns'][ $column_id ]['columns'] = $columns;

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function registed_sortable_columns( $columns ) {

    if( empty( self::$column_type ) ) {

      return $columns;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $columns;

    }

    $option = $mywp_model->get_option();

    if( empty( $option ) ) {

      $option = array();

    }

    $column_id = self::$column_type;

    if( ! empty( self::$post_type ) ) {

      $column_id = self::$post_type;

    } elseif( ! empty( self::$taxonomy ) ) {

      $column_id = self::$taxonomy;

    }

    if( empty( $option['regist_columns'][ $column_id ] ) ) {

      $option['regist_columns'][ $column_id ] = array();

    }

    $option['regist_columns'][ $column_id ]['sortables'] = $columns;

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }
}

MywpControllerModuleAdminRegistListColumns::init();

endif;
