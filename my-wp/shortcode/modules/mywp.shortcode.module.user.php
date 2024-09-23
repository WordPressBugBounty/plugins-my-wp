<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleUser' ) ) :

final class MywpShortcodeModuleUser extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_user';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    if( empty( $atts['field'] ) ) {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $field = strip_tags( $atts['field'] );

    if( empty( $atts['user_id'] ) ) {

      $user_id = get_current_user_id();

    } else {

      $user_id = (int) $atts['user_id'];

    }

    $mywp_user = new MywpUser( $user_id );

    if( empty( $mywp_user ) ) {

      return $content;

    }

    if( $field === 'id' ) {

      $content = $user_id;

    } elseif( $field === 'name' ) {

      $content = $mywp_user->get_name();

    } elseif( $field === 'fname' ) {

      $content = $mywp_user->get_fname();

    } elseif( $field === 'lname' ) {

      $content = $mywp_user->get_lname();

    } elseif( $field === 'nickname' ) {

      $content = $mywp_user->get_nickname();

    } elseif( $field === 'displayname' ) {

      $content = $mywp_user->get_displayname();

    } elseif( $field === 'user_login' ) {

      $content = $mywp_user->get_user_login();

    } elseif( $field === 'user_role' ) {

      $content = $mywp_user->get_user_role();

    } elseif( $field === 'avatar' ) {

      $size = false;

      if( ! empty( $atts['size'] ) ) {

        $size = $atts['size'];

      }

      $content = $mywp_user->get_avatar_tag( $size );

    } else {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $content = apply_filters( 'mywp_shortcode_user' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModuleUser::init();

endif;
