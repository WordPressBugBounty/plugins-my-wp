<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingInit' ) ) :

final class MywpSettingInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );

    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_main' ) , 5 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_network' ) , 5 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_admin' ) , 30 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_frontend' ) , 40 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_login' ) , 50 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_site' ) , 60 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_update' ) , 70 );
    add_filter( 'mywp_setting_menus' , array( __CLASS__ , 'add_setting_menu_debug' ) , 100 );

    add_action( 'mywp_request_admin_manager' , array( __CLASS__ , 'mywp_request_admin_manager' ) );
    add_action( 'mywp_request_network_admin_manager' , array( __CLASS__ , 'mywp_request_network_admin_manager' ) );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'setting/modules/';

    $includes = array(

      'admin_comments'            => $dir . 'mywp.setting.admin.comments.php',
      'admin_dashboard'           => $dir . 'mywp.setting.admin.dashboard.php',
      'admin_general'             => $dir . 'mywp.setting.admin.general.php',
      'admin_nav_menu'            => $dir . 'mywp.setting.admin.nav-menu.php',
      'admin_post_edit'           => $dir . 'mywp.setting.admin.post-edit.php',
      'admin_posts'               => $dir . 'mywp.setting.admin.posts.php',
      'admin_terms'               => $dir . 'mywp.setting.admin.terms.php',
      'admin_sidebar'             => $dir . 'mywp.setting.admin.sidebar.php',
      'admin_toolbar'             => $dir . 'mywp.setting.admin.toolbar.php',
      'admin_uploads'             => $dir . 'mywp.setting.admin.uploads.php',
      'admin_user_edit'           => $dir . 'mywp.setting.admin.user-edit.php',
      'admin_users'               => $dir . 'mywp.setting.admin.users.php',
      'admin_site_editor'         => $dir . 'mywp.setting.admin.site-editor.php',

      'debug_blogs'               => $dir . 'mywp.setting.debug.blogs.php',
      'debug_crons'               => $dir . 'mywp.setting.debug.crons.php',
      'debug_datetime'            => $dir . 'mywp.setting.debug.datetime.php',
      'debug_defines'             => $dir . 'mywp.setting.debug.defines.php',
      'debug_find_option'         => $dir . 'mywp.setting.debug.find-option.php',
      'debug_general'             => $dir . 'mywp.setting.debug.general.php',
      'debug_options'             => $dir . 'mywp.setting.debug.options.php',
      'debug_post_statuses'       => $dir . 'mywp.setting.debug.post-statuses.php',
      'debug_post_type_structure' => $dir . 'mywp.setting.debug.post-type-structure.php',
      'debug_post_types'          => $dir . 'mywp.setting.debug.post-types.php',
      'debug_post_structure'      => $dir . 'mywp.setting.debug.post-structure.php',
      'debug_rest_api'            => $dir . 'mywp.setting.debug.rest-api.php',
      'debug_site_options'        => $dir . 'mywp.setting.debug.site-options.php',
      'debug_taxonomies'          => $dir . 'mywp.setting.debug.taxonomies.php',
      'debug_terms'               => $dir . 'mywp.setting.debug.terms.php',
      'debug_transients'          => $dir . 'mywp.setting.debug.transients.php',
      'debug_translations'        => $dir . 'mywp.setting.debug.translations.php',
      'debug_capabilities'        => $dir . 'mywp.setting.debug.capabilities.php',

      'frontend_author_archive'   => $dir . 'mywp.setting.frontend.author-archive.php',
      'frontend_date_archive'     => $dir . 'mywp.setting.frontend.date-archive.php',
      'frontend_taxonomy_archive' => $dir . 'mywp.setting.frontend.taxonomy-archive.php',
      'frontend_general'          => $dir . 'mywp.setting.frontend.general.php',
      'frontend_toolbar'          => $dir . 'mywp.setting.frontend.toolbar.php',

      'login_general'             => $dir . 'mywp.setting.login.general.php',
      'login_user'                => $dir . 'mywp.setting.login.user.php',

      'main_general'              => $dir . 'mywp.setting.main.general.php',

      'site_general'              => $dir . 'mywp.setting.site.general.php',
      'site_post_type'            => $dir . 'mywp.setting.site.post-type.php',
      'site_sitemap'              => $dir . 'mywp.setting.site.sitemap.php',

      'update_bulk_post_meta'     => $dir . 'mywp.setting.update.bulk-post-meta.php',

    );

    $includes = apply_filters( 'mywp_setting_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_setting_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function add_setting_menu_main( $setting_menus ) {

    $setting_menus['main'] = array(
      'menu_title' => __( 'My WP' , 'my-wp' ),
      'page_title' => __( 'My WP Customize' , 'my-wp' ),
      'slug' => 'mywp',
      'main' => true,
      'multiple_screens' => false,
      'icon_url' => 'none',
    );

    return $setting_menus;

  }

  public static function add_setting_menu_admin( $setting_menus ) {

    $setting_menus['admin'] = array(
      'menu_title' => __( 'Admin' , 'my-wp' ),
    );

    return $setting_menus;

  }

  public static function add_setting_menu_frontend( $setting_menus ) {

    $setting_menus['frontend'] = array(
      'menu_title' => __( 'Frontend' , 'my-wp' ),
    );

    return $setting_menus;

  }

  public static function add_setting_menu_login( $setting_menus ) {

    $setting_menus['login'] = array(
      'menu_title' => __( 'Log in' ),
    );

    return $setting_menus;

  }

  public static function add_setting_menu_site( $setting_menus ) {

    $setting_menus['site'] = array(
      'menu_title' => __( 'Website' , 'my-wp' ),
    );

    return $setting_menus;

  }

  public static function add_setting_menu_network( $setting_menus ) {

    $setting_menus['network'] = array(
      'menu_title' => __( 'My WP' , 'my-wp' ),
      'network' => true,
      'main' => true,
      'icon_url' => 'none',
    );

    return $setting_menus;

  }

  public static function add_setting_menu_update( $setting_menus ) {

    $setting_menus['update'] = array(
      'menu_title' => __( 'Update' ),
    );

    return $setting_menus;

  }

  public static function add_setting_menu_debug( $setting_menus ) {

    $setting_menus['debug'] = array(
      'menu_title' => __( 'Debug' , 'my-wp' ),
    );

    $setting_menus['network_debug'] = array(
      'menu_title' => __( 'Debug' , 'my-wp' ),
      'network' => true,
    );

    return $setting_menus;

  }

  public static function mywp_request_admin_manager() {

    if( is_network_admin() ) {

      return false;

    }

    $setting_menus = MywpSettingMenu::get_setting_menus();

    if( empty( $setting_menus ) ) {

      return false;

    }

    $setting_screens = MywpSettingScreen::get_setting_screens();

    if( empty( $setting_screens ) ) {

      return false;

    }

    self::setting_data_update();

    add_action( 'admin_menu' , array( __CLASS__ , 'admin_menu' ) );

    add_action( 'admin_init' , array( __CLASS__ , 'admin_init' ) , 20 );

  }

  public static function mywp_request_network_admin_manager() {

    if( ! is_network_admin() ) {

      return false;

    }

    $setting_menus = MywpSettingMenu::get_setting_menus();

    if( empty( $setting_menus ) ) {

      return false;

    }

    $setting_screens = MywpSettingScreen::get_setting_screens();

    if( empty( $setting_screens ) ) {

      return false;

    }

    self::setting_data_update();

    add_action( 'network_admin_menu' , array( __CLASS__ , 'network_admin_menu' ) );

    add_action( 'admin_init' , array( __CLASS__ , 'admin_init' ) , 20 );

  }

  private static function setting_data_update() {

    if( empty( $_POST ) ) {

      return false;

    }

    if( ! MywpSetting::is_mywp_form_action( $_POST ) ) {

      return false;

    }

    if( is_network_admin() ) {

      if( ! MywpApi::is_network_manager() ) {

        return false;

      }

    } else {

      if( ! MywpApi::is_manager() ) {

        return false;

      }

    }

    $form = $_POST['mywp'];

    $setting_screen_id = strip_tags( $form['setting_screen'] );

    $action = strip_tags( $form['action'] );

    $nonce_key = MywpSetting::get_nonce_key( $setting_screen_id , $action );

    check_admin_referer( $nonce_key , $nonce_key );

    $mywp_notice = new MywpNotice();

    $formatted_data = MywpSetting::post_data_format( $setting_screen_id , $action , $form );

    $notice = $mywp_notice->get_notice();

    if( ! empty( $notice ) ) {

      return false;

    }

    $validated_data = MywpSetting::post_data_validate( $setting_screen_id , $action , $formatted_data );

    $notice = $mywp_notice->get_notice();

    if( ! empty( $notice ) ) {

      return false;

    }

    $is_redirect = MywpSetting::post_data_action( $setting_screen_id , $action , $validated_data );

    if( $is_redirect ) {

      wp_safe_redirect( esc_url_raw( remove_query_arg( 'updated' , add_query_arg( 'updated' , true ) ) ) );
      exit;

    }

  }

  public static function admin_menu() {

    $setting_menus = MywpSettingMenu::get_setting_menus();

    if( empty( $setting_menus ) ) {

      return false;

    }

    foreach( $setting_menus as $setting_menu_id => $setting_menu ) {

      if( ! empty( $setting_menu['network'] ) ) {

        continue;

      }

      MywpSettingMenu::add_menu( $setting_menu_id , $setting_menu );

    }

  }

  public static function network_admin_menu() {

    $setting_menus = MywpSettingMenu::get_setting_menus();

    if( empty( $setting_menus ) ) {

      return false;

    }

    foreach( $setting_menus as $setting_menu_id => $setting_menu ) {

      if( empty( $setting_menu['network'] ) ) {

        continue;

      }

      MywpSettingMenu::add_menu( $setting_menu_id , $setting_menu );

    }

  }

  public static function admin_init() {

    $menu_hook_names = MywpSettingMenu::get_menu_hook_names();

    if( empty( $menu_hook_names ) ) {

      return false;

    }

    foreach( $menu_hook_names as $setting_menu_id => $menu_hook_name ) {

      add_action( "load-{$menu_hook_name}" , array( __CLASS__ , 'load_setting_screen' ) );

    }

  }

  public static function load_setting_screen() {

    self::set_current_setting();

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();

    if( empty( $current_setting_menu_id ) ) {

      return false;

    }

    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    if( empty( $current_setting_screen_id ) ) {

      return false;

    }

    add_editor_style( array( MywpApi::get_plugin_url( 'css' ) . 'admin-editor.css' ) );

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'admin_enqueue_scripts' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'admin_print_styles' ) );

    add_action( 'admin_print_scripts' , array( __CLASS__ , 'admin_print_scripts' ) );

    add_action( 'admin_print_footer_scripts' , array( __CLASS__ , 'admin_print_footer_scripts' ) );

    add_filter( 'admin_body_class' , array( __CLASS__ , 'admin_body_class' ) );

    do_action( "mywp_setting_load_setting_screen_{$current_setting_menu_id}" );

    do_action( "mywp_setting_load_setting_screen_{$current_setting_screen_id}" );

    do_action( "mywp_setting_load_setting_screen_{$current_setting_screen_id}_{$current_setting_menu_id}" );

    do_action( 'mywp_setting_load_setting_screen' , $current_setting_screen_id , $current_setting_menu_id );

    do_action( 'mywp_setting_after_load_setting_screen' );

  }

  private static function set_current_setting() {

    global $page_hook;

    if( empty( $page_hook ) ) {

      return false;

    }

    MywpSettingMenu::set_current_menu_by_page_hook( $page_hook );

    if( ! empty( $_GET['setting_screen'] ) ) {

      MywpSettingScreen::set_current_screen_id( $_GET['setting_screen'] );

    } else {

      MywpSettingScreen::set_current_screen_by_menu_id( MywpSettingMenu::get_current_menu_id() );

    }

    do_action( 'mywp_set_current_setting' );

  }

  public static function admin_enqueue_scripts() {

    $dir_css = MywpApi::get_plugin_url( 'css' );
    $dir_js = MywpApi::get_plugin_url( 'js' );

    wp_register_style( 'mywp_admin_setting' , $dir_css . 'admin-setting.css' , array() , MYWP_VERSION );
    wp_register_script( 'mywp_admin_setting' , $dir_js . 'admin-setting.js' , array( 'jquery' ) , MYWP_VERSION );

    $mywp_admin_setting = array(
      'error_try_again' => sprintf( 'ERROR: %s' , __( 'Please try again.' ) ),
      'unknown_error_reload_page' => __( 'An error has occurred. Please reload the page and try again.' ),
      'confirm_message' => __( 'Are you sure you want to do this?' ),
      'confirm_update_message' => __( 'Are you sure you want to update?' , 'my-wp' ),
      'confirm_delete_message' => __( 'Are you sure you want to delete this?' , 'my-wp' ),
      'finish_message' => __( 'Finished' , 'my-wp' ),
      'not_found_update_url' => __( 'Not found update URL.' , 'my-wp' ),
      'column_already_added' => __( 'It column can not be added because it has already added.' , 'my-wp' ),
    );

    wp_localize_script( 'mywp_admin_setting' , 'mywp_admin_setting' , $mywp_admin_setting );

    wp_enqueue_style( 'mywp_admin_setting' );
    wp_enqueue_script( 'mywp_admin_setting' );

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();
    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    do_action( "mywp_setting_admin_enqueue_scripts_{$current_setting_menu_id}" );

    do_action( "mywp_setting_admin_enqueue_scripts_{$current_setting_screen_id}" );

    do_action( "mywp_setting_admin_enqueue_scripts_{$current_setting_screen_id}_{$current_setting_menu_id}" );

    do_action( 'mywp_setting_admin_enqueue_scripts' , $current_setting_screen_id , $current_setting_menu_id );

  }

  public static function admin_print_styles() {

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();
    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    do_action( "mywp_setting_admin_print_styles_{$current_setting_menu_id}" );

    do_action( "mywp_setting_admin_print_styles_{$current_setting_screen_id}" );

    do_action( "mywp_setting_admin_print_styles_{$current_setting_screen_id}_{$current_setting_menu_id}" );

    do_action( 'mywp_setting_admin_print_styles' , $current_setting_screen_id , $current_setting_menu_id );

  }

  public static function admin_print_scripts() {

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();
    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    do_action( "mywp_setting_admin_print_scripts_{$current_setting_menu_id}" );

    do_action( "mywp_setting_admin_print_scripts_{$current_setting_screen_id}" );

    do_action( "mywp_setting_admin_print_scripts_{$current_setting_screen_id}_{$current_setting_menu_id}" );

    do_action( 'mywp_setting_admin_print_scripts' , $current_setting_screen_id , $current_setting_menu_id );

  }

  public static function admin_print_footer_scripts() {

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();
    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    do_action( "mywp_setting_admin_print_footer_scripts_{$current_setting_menu_id}" );

    do_action( "mywp_setting_admin_print_footer_scripts_{$current_setting_screen_id}" );

    do_action( "mywp_setting_admin_print_footer_scripts_{$current_setting_screen_id}_{$current_setting_menu_id}" );

    do_action( 'mywp_setting_admin_print_footer_scripts' , $current_setting_screen_id , $current_setting_menu_id );

  }

  public static function admin_body_class( $admin_body_class ) {

    $admin_body_class .= ' mywp-setting ';

    $current_setting_menu_id = MywpSettingMenu::get_current_menu_id();
    $current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

    $admin_body_class .= " mywp-{$current_setting_menu_id} mywp-{$current_setting_screen_id} ";
    $admin_body_class .= " mywp-{$current_setting_menu_id}-{$current_setting_screen_id} ";

    return $admin_body_class;

  }

}

endif;
