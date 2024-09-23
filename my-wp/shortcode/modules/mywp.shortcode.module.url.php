<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleUrl' ) ) :

final class MywpShortcodeModuleUrl extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_url';

  public static function do_shortcode( $atts = false , $content = false , $tag = false ) {

    if( empty( $atts['site_id'] ) && empty( $atts['blog_id']) ) {

      $site_id = get_current_blog_id();

    } else {

      if( ! empty( $atts['site_id'] ) ) {

        $site_id = (int) $atts['site_id'];

      } elseif( ! empty( $atts['blog_id'] ) ) {

        $site_id = (int) $atts['blog_id'];

      }

    }

    if( (int) get_current_blog_id() === (int) $site_id ) {

      $site_id = false;

    }

    if( ! empty( $atts['admin'] ) ) {

      $url = admin_url( $site_id );

      if( ! empty( $site_id ) ) {

        $url = get_admin_url( $site_id );

      }

    } elseif( ! empty( $atts['network_admin'] ) ) {

      if( is_multisite() ) {

        $url = network_admin_url();

      } else {

        $url = admin_url( $site_id );

        if( ! empty( $site_id ) ) {

          $url = get_admin_url( $site_id );

        }

      }

    } elseif( ! empty( $atts['network_site'] ) ) {

      if( is_multisite() ) {

        $url = network_site_url();

      } else {

        $url = get_site_url( $site_id );

      }

    } elseif( ! empty( $atts['site'] ) ) {

      $url = get_site_url( $site_id );

    } elseif( ! empty( $atts['post_id'] ) ) {

      $url = get_permalink( (int) $atts['post_id'] );

    } elseif( ! empty( $atts['login'] ) ) {

      $url = wp_login_url();

    } elseif( ! empty( $atts['logout'] ) ) {

      $url = wp_logout_url();

    } elseif( ! empty( $atts['lost_password'] ) ) {

      $url = wp_lostpassword_url();

    } elseif( ! empty( $atts['current'] ) ) {

      if( is_ssl() ) {

        $scheme = 'https';

      } else {

        $scheme = 'http';

      }

      $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    } else {

      $url = home_url();

      if( ! empty( $site_id ) ) {

        $url = get_home_url( $site_id );

      }

    }

    if( ! empty( $atts['esc_url'] ) ) {

      $url = esc_url( $url );

    }

    $content = apply_filters( 'mywp_shortcode_url' , $url , $atts );

    return $content;

  }

}

MywpShortcodeModuleUrl::init();

endif;
