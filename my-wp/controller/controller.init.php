<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpControllerInit' ) ) :

final class MywpControllerInit {

  public static function init() {

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_action( 'mywp_request_admin' , array( __CLASS__ , 'controller_cache' ) );
    add_action( 'mywp_request_frontend' , array( __CLASS__ , 'controller_cache' ) );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'controller/modules/';

    $includes = array(

      'admin_comments'            => $dir . 'mywp.controller.module.admin.comments.php',
      'admin_dashboard'           => $dir . 'mywp.controller.module.admin.dashboard.php',
      'admin_general'             => $dir . 'mywp.controller.module.admin.general.php',
      'admin_nav_menu'            => $dir . 'mywp.controller.module.admin.nav-menu.php',
      'admin_post_edit'           => $dir . 'mywp.controller.module.admin.post.edit.php',
      'admin_posts'               => $dir . 'mywp.controller.module.admin.posts.php',
      'admin_terms'               => $dir . 'mywp.controller.module.admin.terms.php',
      'admin_regist_metaboxes'    => $dir . 'mywp.controller.module.admin.regist.metaboxes.php',
      'admin_regist_list_columns' => $dir . 'mywp.controller.module.admin.regist.list-columns.php',
      'admin_sidebar'             => $dir . 'mywp.controller.module.admin.sidebar.php',
      'admin_toolbar'             => $dir . 'mywp.controller.module.admin.toolbar.php',
      'admin_uploads'             => $dir . 'mywp.controller.module.admin.uploads.php',
      'admin_user_edit'           => $dir . 'mywp.controller.module.admin.user-edit.php',
      'admin_users'               => $dir . 'mywp.controller.module.admin.users.php',
      'admin_site_editor'         => $dir . 'mywp.controller.module.admin.site-editor.php',

      'debug_general'             => $dir . 'mywp.controller.module.debug.general.php',

      'frontend_author_archive'   => $dir . 'mywp.controller.module.frontend.author-archive.php',
      'frontend_date_archive'     => $dir . 'mywp.controller.module.frontend.date-archive.php',
      'frontend_taxonomy_archive' => $dir . 'mywp.controller.module.frontend.taxonomy-archive.php',
      'frontend_general'          => $dir . 'mywp.controller.module.frontend.general.php',
      'frontend_toolbar'          => $dir . 'mywp.controller.module.frontend.toolbar.php',
      'frontend_regist_toolbar'   => $dir . 'mywp.controller.module.frontend.regist.toolbar.php',

      'login_general'             => $dir . 'mywp.controller.module.login.general.php',
      'login_user'                => $dir . 'mywp.controller.module.login.user.php',

      'main_general'              => $dir . 'mywp.controller.module.main.general.php',

      'site_general'              => $dir . 'mywp.controller.module.site.general.php',
      'site_post_type'            => $dir . 'mywp.controller.module.site.post-type.php',
      'site_sitemap'              => $dir . 'mywp.controller.module.site.sitemap.php',

    );

    $includes = apply_filters( 'mywp_controller_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_controller_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function controller_cache() {

    MywpController::set_controllers();

  }

}

endif;
