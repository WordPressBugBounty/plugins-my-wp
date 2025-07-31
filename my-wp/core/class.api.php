<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpApi' ) ) :

final class MywpApi {

  public static function get_manager_capability() {

    $capability = 'manage_options';

    return apply_filters( 'mywp_manager_capability' , $capability );

  }

  public static function is_manager() {

    $capability = self::get_manager_capability();

    if( current_user_can( $capability ) ) {

      return true;

    }

    return false;

  }

  public static function get_network_manager_capability() {

    $capability = 'manage_network';

    return apply_filters( 'mywp_network_manager_capability' , $capability );

  }

  public static function is_network_manager() {

    $capability = self::get_network_manager_capability();

    if( current_user_can( $capability ) ) {

      return true;

    }

    return false;

  }

  public static function plugin_info() {

    $plugin_info = array(
      'forum_url' => 'https://wordpress.org/support/plugin/my-wp/',
      'review_url' => 'https://wordpress.org/support/plugin/my-wp/reviews/',
      'admin_url' => admin_url( 'admin.php?page=mywp' ),
      'website_url' => 'https://mywpcustomize.com/',
      'document_url' => 'https://mywpcustomize.com/documents/',
      'document_category_url' => 'https://mywpcustomize.com/document_category/',
    );

    $plugin_info = apply_filters( 'mywp_plugin_info' , $plugin_info );

    return $plugin_info;

  }

  public static function get_plugin_url( $path = 'assets' ) {

    $path = strip_tags( $path );

    if( $path === 'assets' ) {

      return MYWP_PLUGIN_URL . 'assets/';

    } elseif( $path === 'css' ) {

        return MYWP_PLUGIN_URL . 'assets/css/';

    } elseif( $path === 'js' ) {

        return MYWP_PLUGIN_URL . 'assets/js/';

    } elseif( $path === 'img' ) {

        return MYWP_PLUGIN_URL . 'assets/img/';

      } elseif( $path === 'font' ) {

          return MYWP_PLUGIN_URL . 'assets/font/';

    } else {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( $path , $called_text );

    }

  }

  public static function include_files( $files = array() ) {

    if( empty( $files ) ) {

      return false;

    }

    foreach( $files as $file ) {

      self::include_file( $file );

    }

  }

  public static function include_file( $file = false ) {

    if( $file === false ) {

      return false;

    }

    if ( file_exists( $file ) ) {

      $mywp_cache = new MywpCache( 'include_file' );

      $mywp_cache->add_cache( $file );

      include_once( $file );

    } else {

      $error_msg = sprintf( __( "There doesn't seem to be a %s file." , 'my-wp' ) , $file );

      return self::add_error( $error_msg );

    }

  }

  public static function require_files( $files = array() ) {

    if( empty( $files ) ) {

      return false;

    }

    foreach( $files as $file ) {

      self::require_file( $file );

    }

  }

  public static function require_file( $file = false ) {

    if( $file === false ) {

      return false;

    }

    if ( file_exists( $file ) ) {

      $mywp_cache = new MywpCache( 'require_file' );

      $mywp_cache->add_cache( $file );

      require_once( $file );

    } else {

      $error_msg = sprintf( __( "There doesn't seem to be a %s file." , 'my-wp' ) , $file );

      return self::add_error( $error_msg );

    }

  }

  public static function add_error( $message = false ) {

    $mywp_cache = new MywpCache( 'error' );

    return $mywp_cache->add_cache( $message );

  }

  public static function get_error() {

    $errors = self::get_errors();

    if( empty( $errors ) ) {

      return false;

    }

    return array_shift( $errors );

  }

  public static function get_errors() {

    $mywp_cache = new MywpCache( 'error' );

    return $mywp_cache->get_cache();

  }

  public static function get_all_user_roles() {

    if( ! function_exists( 'get_editable_roles' ) ) {

      require_once ABSPATH . 'wp-admin/includes/user.php';

    }

    if( ! function_exists( 'get_editable_roles' ) ) {

      $called_text = sprintf( 'get_editable_roles()' , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$editable_roles' , $called_text );

      return false;

    }

    $editable_roles = get_editable_roles();

    if( empty( $editable_roles ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

      MywpHelper::error_not_found_message( '$editable_roles' , $called_text );

      return false;

    }

    $all_user_roles = array();

    foreach( $editable_roles as $role_group_name => $role_details ) {

      $role_group = $role_details;
      $role_group['label'] = translate_user_role( $role_details['name'] );

      $all_user_roles[ $role_group_name ] = $role_group;

    }

    return apply_filters( 'mywp_all_user_roles' , $all_user_roles );
  }

  public static function get_all_post_types() {

    global $wp_post_types;

    return $wp_post_types;

  }

  public static function get_all_post_statuses() {

    global $wp_post_statuses;

    return $wp_post_statuses;

  }

  public static function get_all_taxonomies() {

    global $wp_taxonomies;

    return $wp_taxonomies;

  }

  public static function get_dashicons() {

    $categories = array(
      'admin_menu'     => array( 'id' => 'admin_menu' , 'title' =>__( 'Admin Menu' ) ),
      'welcome_screen' => array( 'id' => 'welcome_screen' , 'title' =>__( 'Welcome Screen' ) ),
      'post_formats'   => array( 'id' => 'post_formats' , 'title' =>__( 'Post Formats' ) ),
      'media'          => array( 'id' => 'media' , 'title' =>__( 'Media' ) ),
      'image_editing'  => array( 'id' => 'image_editing' , 'title' =>__( 'Image Editing' ) ),
      'tinymce'        => array( 'id' => 'tinymce' , 'title' =>__( 'TinyMCE' ) ),
      'posts_screen'   => array( 'id' => 'posts_screen' , 'title' =>__( 'Posts Screen' ) ),
      'sorting'        => array( 'id' => 'sorting' , 'title' =>__( 'Sorting' ) ),
      'social'         => array( 'id' => 'social' , 'title' =>__( 'Social' ) ),
      'wp'             => array( 'id' => 'wp' , 'title' =>__( 'WordPress.org Specific: Jobs, Profiles, WordCamps' ) ),
      'products'       => array( 'id' => 'products' , 'title' =>__( 'Products' ) ),
      'taxonomies'     => array( 'id' => 'taxonomies' , 'title' =>__( 'Taxonomies' ) ),
      'widgets'        => array( 'id' => 'widgets' , 'title' =>__( 'Widgets' ) ),
      'notifications'  => array( 'id' => 'notifications' , 'title' =>__( 'Notifications' ) ),
      'misc'           => array( 'id' => 'misc' , 'title' =>__( 'Misc' ) ),
    );

    $icons = array(

      array(
        'css_content' => '\f333',
        'class' => 'dashicons-menu',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f319',
        'class' => 'dashicons-admin-site',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f226',
        'class' => 'dashicons-dashboard',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f109',
        'class' => 'dashicons-admin-post',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f104',
        'class' => 'dashicons-admin-media',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f103',
        'class' => 'dashicons-admin-links',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f105',
        'class' => 'dashicons-admin-page',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f101',
        'class' => 'dashicons-admin-comments',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f100',
        'class' => 'dashicons-admin-appearance',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f106',
        'class' => 'dashicons-admin-plugins',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f110',
        'class' => 'dashicons-admin-users',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f107',
        'class' => 'dashicons-admin-tools',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f108',
        'class' => 'dashicons-admin-settings',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f112',
        'class' => 'dashicons-admin-network',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f102',
        'class' => 'dashicons-admin-home',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f111',
        'class' => 'dashicons-admin-generic',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f148',
        'class' => 'dashicons-admin-collapse',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f536',
        'class' => 'dashicons-filter',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f540',
        'class' => 'dashicons-admin-customizer',
        'cat' => 'admin_menu',
      ),
      array(
        'css_content' => '\f541',
        'class' => 'dashicons-admin-multisite',
        'cat' => 'admin_menu',
      ),

      array(
        'css_content' => '\f119',
        'class' => 'dashicons-welcome-write-blog',
        'cat' => 'welcome_screen',
      ),
      array(
        'css_content' => '\f133',
        'class' => 'dashicons-welcome-add-page',
        'cat' => 'welcome_screen',
      ),
      array(
        'css_content' => '\f115',
        'class' => 'dashicons-welcome-view-site',
        'cat' => 'welcome_screen',
      ),
      array(
        'css_content' => '\f116',
        'class' => 'dashicons-welcome-widgets-menus',
        'cat' => 'welcome_screen',
      ),
      array(
        'css_content' => '\f117',
        'class' => 'dashicons-welcome-comments',
        'cat' => 'welcome_screen',
      ),
      array(
        'css_content' => '\f118',
        'class' => 'dashicons-welcome-learn-more',
        'cat' => 'welcome_screen',
      ),

      array(
        'css_content' => '\f123',
        'class' => 'dashicons-format-aside',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f128',
        'class' => 'dashicons-format-image',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f161',
        'class' => 'dashicons-format-gallery',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f126',
        'class' => 'dashicons-format-video',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f130',
        'class' => 'dashicons-format-status',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f122',
        'class' => 'dashicons-format-quote',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f125',
        'class' => 'dashicons-format-chat',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f127',
        'class' => 'dashicons-format-audio',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f306',
        'class' => 'dashicons-camera',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f232',
        'class' => 'dashicons-images-alt',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f233',
        'class' => 'dashicons-images-alt2',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f234',
        'class' => 'dashicons-video-alt',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f235',
        'class' => 'dashicons-video-alt2',
        'cat' => 'post_formats',
      ),
      array(
        'css_content' => '\f236',
        'class' => 'dashicons-video-alt3',
        'cat' => 'post_formats',
      ),

      array(
        'css_content' => '\f501',
        'class' => 'dashicons-media-archive',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f500',
        'class' => 'dashicons-media-audio',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f499',
        'class' => 'dashicons-media-code',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f498',
        'class' => 'dashicons-media-default',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f497',
        'class' => 'dashicons-media-document',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f496',
        'class' => 'dashicons-media-interactive',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f495',
        'class' => 'dashicons-media-spreadsheet',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f491',
        'class' => 'dashicons-media-text',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f490',
        'class' => 'dashicons-media-video',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f492',
        'class' => 'dashicons-playlist-audio',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f493',
        'class' => 'dashicons-playlist-video',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f522',
        'class' => 'dashicons-controls-play',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f523',
        'class' => 'dashicons-controls-pause',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f519',
        'class' => 'dashicons-controls-forward',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f517',
        'class' => 'dashicons-controls-skipforward',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f518',
        'class' => 'dashicons-controls-back',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f516',
        'class' => 'dashicons-controls-skipback',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f515',
        'class' => 'dashicons-controls-repeat',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f521',
        'class' => 'dashicons-controls-volumeon',
        'cat' => 'media',
      ),
      array(
        'css_content' => '\f520',
        'class' => 'dashicons-controls-volumeoff',
        'cat' => 'media',
      ),

      array(
        'css_content' => '\f165',
        'class' => 'dashicons-image-crop',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f531',
        'class' => 'dashicons-image-rotate',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f166',
        'class' => 'dashicons-image-rotate-left',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f167',
        'class' => 'dashicons-image-rotate-right',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f168',
        'class' => 'dashicons-image-flip-vertical',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f169',
        'class' => 'dashicons-image-flip-horizontal',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f533',
        'class' => 'dashicons-image-filter',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f171',
        'class' => 'dashicons-undo',
        'cat' => 'image_editing',
      ),
      array(
        'css_content' => '\f172',
        'class' => 'dashicons-redo',
        'cat' => 'image_editing',
      ),

      array(
        'css_content' => '\f200',
        'class' => 'dashicons-editor-bold',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f201',
        'class' => 'dashicons-editor-italic',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f203',
        'class' => 'dashicons-editor-ul',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f204',
        'class' => 'dashicons-editor-ol',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f205',
        'class' => 'dashicons-editor-quote',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f206',
        'class' => 'dashicons-editor-alignleft',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f207',
        'class' => 'dashicons-editor-aligncenter',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f208',
        'class' => 'dashicons-editor-alignright',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f209',
        'class' => 'dashicons-editor-insertmore',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f210',
        'class' => 'dashicons-editor-spellcheck',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f211',
        'class' => 'dashicons-editor-expand',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f506',
        'class' => 'dashicons-editor-contract',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f212',
        'class' => 'dashicons-editor-kitchensink',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f213',
        'class' => 'dashicons-editor-underline',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f214',
        'class' => 'dashicons-editor-justify',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f215',
        'class' => 'dashicons-editor-textcolor',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f216',
        'class' => 'dashicons-editor-paste-word',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f217',
        'class' => 'dashicons-editor-paste-text',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f218',
        'class' => 'dashicons-editor-removeformatting',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f219',
        'class' => 'dashicons-editor-video',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f220',
        'class' => 'dashicons-editor-customchar',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f221',
        'class' => 'dashicons-editor-outdent',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f222',
        'class' => 'dashicons-editor-indent',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f223',
        'class' => 'dashicons-editor-help',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f224',
        'class' => 'dashicons-editor-strikethrough',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f225',
        'class' => 'dashicons-editor-unlink',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f320',
        'class' => 'dashicons-editor-rtl',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f474',
        'class' => 'dashicons-editor-break',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f475',
        'class' => 'dashicons-editor-code',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f476',
        'class' => 'dashicons-editor-paragraph',
        'cat' => 'tinymce',
      ),
      array(
        'css_content' => '\f535',
        'class' => 'dashicons-editor-table',
        'cat' => 'tinymce',
      ),

      array(
        'css_content' => '\f135',
        'class' => 'dashicons-align-left',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f136',
        'class' => 'dashicons-align-right',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f134',
        'class' => 'dashicons-align-center',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f138',
        'class' => 'dashicons-align-none',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f160',
        'class' => 'dashicons-lock',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f528',
        'class' => 'dashicons-unlock',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f145',
        'class' => 'dashicons-calendar',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f508',
        'class' => 'dashicons-calendar-alt',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f177',
        'class' => 'dashicons-visibility',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f530',
        'class' => 'dashicons-hidden',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f173',
        'class' => 'dashicons-post-status',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f464',
        'class' => 'dashicons-edit',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f182',
        'class' => 'dashicons-trash',
        'cat' => 'posts_screen',
      ),
      array(
        'css_content' => '\f537',
        'class' => 'dashicons-sticky',
        'cat' => 'posts_screen',
      ),

      array(
        'css_content' => '\f504',
        'class' => 'dashicons-external',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f142',
        'class' => 'dashicons-arrow-up',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f140',
        'class' => 'dashicons-arrow-down',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f139',
        'class' => 'dashicons-arrow-right',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f141',
        'class' => 'dashicons-arrow-left',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f342"',
        'class' => 'dashicons-arrow-up-alt',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f346',
        'class' => 'dashicons-arrow-down-alt',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f344',
        'class' => 'dashicons-arrow-right-alt',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f340',
        'class' => 'dashicons-arrow-left-alt',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f343',
        'class' => 'dashicons-arrow-up-alt2',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f347',
        'class' => 'dashicons-arrow-down-alt2',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f345',
        'class' => 'dashicons-arrow-right-alt2',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f341',
        'class' => 'dashicons-arrow-left-alt2',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f156',
        'class' => 'dashicons-sort',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f229',
        'class' => 'dashicons-leftright',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f503',
        'class' => 'dashicons-randomize',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f163',
        'class' => 'dashicons-list-view',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f164',
        'class' => 'dashicons-exerpt-view',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f509',
        'class' => 'dashicons-grid-view',
        'cat' => 'sorting',
      ),
      array(
        'css_content' => '\f545',
        'class' => 'dashicons-move',
        'cat' => 'sorting',
      ),

      array(
        'css_content' => '\f237',
        'class' => 'dashicons-share',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f240',
        'class' => 'dashicons-share-alt',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f242',
        'class' => 'dashicons-share-alt2',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f301',
        'class' => 'dashicons-twitter',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f303',
        'class' => 'dashicons-rss',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f465',
        'class' => 'dashicons-email',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f466',
        'class' => 'dashicons-email-alt',
        'cat' => 'social',
      ),
      array(
        'css_content' => 'f304',
        'class' => 'dashicons-facebook',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f305',
        'class' => 'dashicons-facebook-alt',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f462',
        'class' => 'dashicons-googleplus',
        'cat' => 'social',
      ),
      array(
        'css_content' => '\f325',
        'class' => 'dashicons-networking',
        'cat' => 'social',
      ),

      array(
        'css_content' => '\f308',
        'class' => 'dashicons-hammer',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f309',
        'class' => 'dashicons-art',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f310',
        'class' => 'dashicons-migrate',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f311',
        'class' => 'dashicons-performance',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f483',
        'class' => 'dashicons-universal-access',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f507',
        'class' => 'dashicons-universal-access-alt',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f486',
        'class' => 'dashicons-tickets',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f484',
        'class' => 'dashicons-nametag',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f481',
        'class' => 'dashicons-clipboard',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f487',
        'class' => 'dashicons-heart',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f488',
        'class' => 'dashicons-megaphone',
        'cat' => 'wp',
      ),
      array(
        'css_content' => '\f489',
        'class' => 'dashicons-schedule',
        'cat' => 'wp',
      ),

      array(
        'css_content' => '\f120',
        'class' => 'dashicons-wordpress',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f324',
        'class' => 'dashicons-wordpress-alt',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f157',
        'class' => 'dashicons-pressthis',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f463',
        'class' => 'dashicons-update',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f180',
        'class' => 'dashicons-screenoptions',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f348"',
        'class' => 'dashicons-info',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f174',
        'class' => 'dashicons-cart',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f175',
        'class' => 'dashicons-feedback',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f176',
        'class' => 'dashicons-cloud',
        'cat' => 'products',
      ),
      array(
        'css_content' => '\f326',
        'class' => 'dashicons-translation',
        'cat' => 'products',
      ),

      array(
        'css_content' => '\f323',
        'class' => 'dashicons-tag',
        'cat' => 'taxonomies',
      ),
      array(
        'css_content' => '\f318',
        'class' => 'dashicons-category',
        'cat' => 'taxonomies',
      ),

      array(
        'css_content' => '\f480',
        'class' => 'dashicons-archive',
        'cat' => 'widgets',
      ),
      array(
        'css_content' => '\f479',
        'class' => 'dashicons-tagcloud',
        'cat' => 'widgets',
      ),
      array(
        'css_content' => '\f478',
        'class' => 'dashicons-text',
        'cat' => 'widgets',
      ),

      array(
        'css_content' => '\f147',
        'class' => 'dashicons-yes',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f158',
        'class' => 'dashicons-no',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f335',
        'class' => 'dashicons-no-alt',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f132',
        'class' => 'dashicons-plus',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f502',
        'class' => 'dashicons-plus-alt',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f460',
        'class' => 'dashicons-minus',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f153',
        'class' => 'dashicons-dismiss',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f159',
        'class' => 'dashicons-marker',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f155',
        'class' => 'dashicons-star-filled',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f459',
        'class' => 'dashicons-star-half',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f154',
        'class' => 'dashicons-star-empty',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f227',
        'class' => 'dashicons-flag',
        'cat' => 'notifications',
      ),
      array(
        'css_content' => '\f534',
        'class' => 'dashicons-warning',
        'cat' => 'notifications',
      ),

      array(
        'css_content' => '\f230',
        'class' => 'dashicons-location',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f231',
        'class' => 'dashicons-location-alt',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f178',
        'class' => 'dashicons-vault',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f332',
        'class' => 'dashicons-shield',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f334',
        'class' => 'dashicons-shield-alt',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f468',
        'class' => 'dashicons-sos',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f179',
        'class' => 'dashicons-search',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f181',
        'class' => 'dashicons-slides',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f183',
        'class' => 'dashicons-analytics',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f184',
        'class' => 'dashicons-chart-pie',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f185',
        'class' => 'dashicons-chart-bar',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f238',
        'class' => 'dashicons-chart-line',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f239',
        'class' => 'dashicons-chart-area',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f307',
        'class' => 'dashicons-groups',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f338',
        'class' => 'dashicons-businessman',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f336',
        'class' => 'dashicons-id',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f337',
        'class' => 'dashicons-id-alt',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f312',
        'class' => 'dashicons-products',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f313',
        'class' => 'dashicons-awards',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f314',
        'class' => 'dashicons-forms',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f473',
        'class' => 'dashicons-testimonial',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f322',
        'class' => 'dashicons-portfolio',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f330',
        'class' => 'dashicons-book',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f331',
        'class' => 'dashicons-book-alt',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f316',
        'class' => 'dashicons-download',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f317',
        'class' => 'dashicons-upload',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f321',
        'class' => 'dashicons-backup',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f469',
        'class' => 'dashicons-clock',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f339',
        'class' => 'dashicons-lightbulb',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f482',
        'class' => 'dashicons-microphone',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f472',
        'class' => 'dashicons-desktop',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f547',
        'class' => 'dashicons-laptop',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f471',
        'class' => 'dashicons-tablet',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f470',
        'class' => 'dashicons-smartphone',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f525',
        'class' => 'dashicons-phone',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f510',
        'class' => 'dashicons-index-card',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f511',
        'class' => 'dashicons-carrot',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f512',
        'class' => 'dashicons-building',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f513',
        'class' => 'dashicons-store',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f514',
        'class' => 'dashicons-album',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f527',
        'class' => 'dashicons-palmtree',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f524',
        'class' => 'dashicons-tickets-alt',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f526',
        'class' => 'dashicons-money',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f328',
        'class' => 'dashicons-smiley',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f529',
        'class' => 'dashicons-thumbs-up',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f542',
        'class' => 'dashicons-thumbs-down',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f538',
        'class' => 'dashicons-layout',
        'cat' => 'misc',
      ),
      array(
        'css_content' => '\f546',
        'class' => 'dashicons-paperclip',
        'cat' => 'misc',
      ),

    );

    $dashicons = array( 'all' => $icons , 'categories' => $categories );

    return $dashicons;

  }

  public static function get_theme_path( $parent = false ) {

    if( $parent ) {

      $theme_path = get_template_directory();

    } else {

      $theme_path = get_stylesheet_directory();

    }

    return $theme_path;

  }

  public static function get_theme_url( $parent = false ) {

    if( $parent ) {

      $theme_url = get_template_directory_uri();

    } else {

      $theme_url = get_stylesheet_directory_uri();

    }

    return $theme_url;

  }

  public static function duplicate_post_from_site_in_network( $from_site_id = false , $from_post_id = false , $args = array() ) {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $from_site_id = (int) $from_site_id;

    if( empty( $from_site_id ) ) {

      MywpHelper::error_require_message( '$from_site_id' , $called_text );

      return false;

    }

    if( is_multisite() ) {

      $blog_details = get_blog_details( array( 'blog_id' => $from_site_id ) );

      if( empty( $blog_details ) ) {

        MywpHelper::error_not_found_message( '$blog_details' , $called_text );

        return false;

      }

    }

    $from_post_id = (int) $from_post_id;

    if( empty( $from_post_id ) ) {

      MywpHelper::error_require_message( '$from_post_id' , $called_text );

      return false;

    }

    $from_args = array(
      'site_id' => $from_site_id,
      'post_id' => $from_post_id,
      'args' => $args,
    );

    $is_switch_blog = false;

    if( is_multisite() ) {

      if( $from_site_id !== (int) get_current_blog_id() ) {

        $is_switch_blog = true;

        switch_to_blog( $from_site_id );

      }

    }

    $from_post = get_post( $from_post_id );

    $from_post_metas = false;

    $from_post_thumbnail_id = false;

    $from_post_attached_file = false;

    $from_post_taxonomy_terms = false;

    if( ! empty( $from_post ) ) {

      if( ! empty( $args['is_duplicate_custom_field'] ) ) {

        $from_post_metas = get_post_meta( $from_post_id );

      }

      if( ! empty( $args['is_duplicate_featured_image'] ) ) {

        $from_post_thumbnail_id = get_post_thumbnail_id( $from_post_id );

        $from_post_attached_file = get_attached_file( $from_post_thumbnail_id );

      }

      if( ! empty( $args['is_duplicate_terms'] ) ) {

        $taxonomies_args = array(
          'object_type' => array( $from_post->post_type ),
        );

        $taxonomies = get_taxonomies( $taxonomies_args , 'objects' );

        $from_post_taxonomy_terms = array();

        if( ! empty( $taxonomies ) ) {

          foreach( $taxonomies as $taxonomy ) {

            $terms = wp_get_post_terms( $from_post_id , $taxonomy->name );

            if( empty( $terms ) ) {

              continue;

            }

            if( is_wp_error( $terms ) ) {

              continue;

            }

            $from_post_taxonomy_terms[ $taxonomy->name ] = $terms;

          }

        }

      }

    }

    if( $is_switch_blog ) {

      restore_current_blog();

    }

    if( empty( $from_post ) ) {

      MywpHelper::error_not_found_message( '$from_post' , $called_text );

      return false;

    }

    $post_new_args = (array) $from_post;

    foreach( array( 'ID' , 'guid' , 'post_date' , 'post_date_gmt' , 'post_modified' , 'post_modified_gmt' , 'post_content_filtered' , 'comment_count' , 'filter' ) as $key ) {

      if( isset( $post_new_args[ $key ] ) ) {

        unset( $post_new_args[ $key ] );

      }

    }

    $post_new_args = apply_filters( 'mywp_api_duplicate_post_from_site_in_network_post_new_args' , $post_new_args , $from_post , $from_args );

    if( empty( $post_new_args ) ) {

      MywpHelper::error_not_found_message( '$post_new_args' , $called_text );

      return false;

    }

    add_filter( 'wp_insert_post_empty_content' , '__return_false' , 1000 );

    $new_post_id = wp_insert_post( $post_new_args , true );

    remove_filter( 'wp_insert_post_empty_content' , '__return_false' , 1000 );

    if( empty( $new_post_id ) ) {

      MywpHelper::error_not_found_message( '$new_post_id' , $called_text );

      return false;

    }

    if( is_wp_error( $new_post_id ) ) {

      MywpHelper::error_returned_message( '$new_post_id' , sprintf( '[%s] %s' , $new_post_id->get_error_code() , $new_post_id->get_error_message() ) , $called_text );

      return false;

    }

    $validate_new_post = apply_filters( 'mywp_api_duplicate_post_from_site_in_network_post_validate_new_post' , true , $new_post_id , $from_args );

    if( ! $validate_new_post ) {

      MywpHelper::error_returned_message( '$validate_new_post' , '' , $called_text );

      wp_delete_post( $new_post_id , true );

      return false;

    }

    if( ! empty( $args['is_duplicate_custom_field'] ) ) {

      if( ! empty( $from_post_metas ) ) {

        foreach( $from_post_metas as $meta_key => $from_post_meta_values ) {

          foreach( $from_post_meta_values as $from_post_meta_value ) {

            add_post_meta( $new_post_id , $meta_key , maybe_unserialize( $from_post_meta_value ) );

          }

        }

      }

    }

    if( ! empty( $args['is_duplicate_featured_image'] ) ) {

      if( ! empty( $from_post_attached_file ) ) {

        $pathinfo = pathinfo( $from_post_attached_file );

        $from_post_attached_file_data = file_get_contents( $from_post_attached_file );

        $new_attachment_id = self::create_attachment_from_filedata( $from_post_attached_file_data , $pathinfo['basename'] );

        if( empty( self::get_error() ) ) {

          if( empty( $new_attachment_id ) ) {

            MywpHelper::error_not_found_message( '$new_attachment_id' , $called_text );

          } else {

            wp_update_post( array( 'ID' => $new_attachment_id , 'post_parent' => $new_post_id ) );

            set_post_thumbnail( $new_post_id , $new_attachment_id );

          }

        }

      }

    }

    if( ! empty( $args['is_duplicate_terms'] ) ) {

      if( ! empty( $from_post_taxonomy_terms ) ) {

        foreach( $from_post_taxonomy_terms as $taxonomy_name => $from_post_terms ) {

          $term_names = array();

          foreach( $from_post_terms as $from_post_term ) {

            $term_names[] = $from_post_term->name;

          }

          if( empty( $term_names ) ) {

            continue;

          }

          $wp_set_object_terms = wp_set_object_terms( $new_post_id , $term_names , $taxonomy_name , false );

          if( empty( $wp_set_object_terms ) ) {

            MywpHelper::error_not_found_message( '$wp_set_object_terms' , $called_text );

            continue;

          }

          if( is_wp_error( $wp_set_object_terms ) ) {

            MywpHelper::error_returned_message( '$wp_set_object_terms' , sprintf( '[%s] %s' , $wp_set_object_terms->get_error_code() , $wp_set_object_terms->get_error_message() ) , $called_text );

            continue;

          }

        }

      }

    }

    do_action( 'mywp_api_duplicate_post_from_site_in_network' , $new_post_id , $from_args );

    return $new_post_id;

  }

  public static function create_attachment_from_filedata( $filedata = false , $filename = false ) {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    if( empty( $filedata ) ) {

      MywpHelper::error_require_message( '$filedata' , $called_text );

      return false;

    }

    $filename = strip_tags( $filename );

    if( empty( $filename ) ) {

      MywpHelper::error_require_message( '$filename' , $called_text );

      return false;

    }

    $wp_upload_bits = wp_upload_bits( $filename , null , $filedata );

    if( ! empty( $wp_upload_bits['error'] ) ) {

      MywpHelper::error_returned_message( '$wp_upload_bits' , $wp_upload_bits['error'] , $called_text );

      return false;

    }

    $wp_filetype = wp_check_filetype( $wp_upload_bits['file'] , null );

    $attachment_args = array(
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => sanitize_file_name( basename( $filename ) ),
      'post_content' => '',
      'post_status' => 'inherit',
    );

    $attachment_id = wp_insert_attachment( $attachment_args ,  $wp_upload_bits['file'] );

    if( empty( $attachment_id ) ) {

      MywpHelper::error_not_found_message( '$attachment_id' , $called_text );

      return false;

    }

    if( is_wp_error( $attachment_id ) ) {

      MywpHelper::error_returned_message( '$attachment_id' , sprintf( '[%s] %s' , $attachment_id->get_error_code() , $attachment_id->get_error_message() ) , $called_text );

      return false;

    }

    if( ! function_exists( 'wp_generate_attachment_metadata' ) ) {

      require_once( ABSPATH . 'wp-admin/includes/image.php' );

    }

    $attachment_metadata = wp_generate_attachment_metadata( $attachment_id , $wp_upload_bits['file'] );

    wp_update_attachment_metadata( $attachment_id , $attachment_metadata);

    return $attachment_id;

  }

}

endif;
