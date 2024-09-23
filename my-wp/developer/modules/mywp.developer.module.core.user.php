<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleCoreUser' ) ) :

final class MywpDeveloperModuleCoreUser extends MywpDeveloperAbstractModule {

  static protected $id = 'core_user';

  static protected $priority = 30;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'core',
      'title' => __( 'Current User' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    $user_id = get_current_user_id();

    $mywp_user = new MywpUser( $user_id );

    $debug_lists = array(
      'is_user_logged_in()' => is_user_logged_in(),
      'get_current_user_id()' => $user_id,
      'user_role' => $mywp_user->get_user_role(),
      'user_roles' => $mywp_user->get_user_roles(),
      'user_capabilities' => $mywp_user->get_user_capabilities(),
      'user_data' => $mywp_user->get_user_data(),
      'is_super_admin()' => is_super_admin(),
      'get_user_locale()' => get_user_locale(),
    );

    return $debug_lists;

  }

}

MywpDeveloperModuleCoreUser::init();

endif;
