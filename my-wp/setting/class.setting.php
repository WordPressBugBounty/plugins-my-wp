<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSetting' ) ) :

final class MywpSetting {

  public static function setting_view() {

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/setting-screen.php' );

  }

  public static function get_model( $setting_screen_id = false ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , $setting_screen_id );

    if( empty( $setting_screen_id ) ) {

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screen = MywpSettingScreen::get_setting_screen( $setting_screen_id );

    if( empty( $setting_screen['controller'] ) ) {

      return false;

    }

    $controller = MywpController::get_controller( $setting_screen['controller'] );

    if( empty( $controller['id'] ) ) {

      MywpHelper::error_require_message( '$controller["id"]' , $called_text );

      return false;

    }

    $controller_id = $controller['id'];

    do_action( 'mywp_setting_before_get_model' , $setting_screen_id , $controller_id );

    $pre_model = apply_filters( "mywp_setting_pre_get_model_{$controller_id}" , false , $setting_screen_id , $controller );
    $pre_model = apply_filters( 'mywp_setting_pre_get_model' , $pre_model , $setting_screen_id , $controller_id , $controller );

    if( $pre_model !== false ) {

      return $pre_model;

    }

    $mywp_model = new MywpModel( $controller_id , 'setting' , $controller['network'] );

    $mywp_model->set_initial_data( $controller['initial_data'] );
    $mywp_model->set_default_data( $controller['default_data'] );

    do_action( 'mywp_setting_after_get_model' , $setting_screen_id , $controller_id );

    return $mywp_model;

  }

  public static function get_setting_data( $setting_screen_id = false ) {

    $model = self::get_model( $setting_screen_id );

    if( empty( $model ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , $setting_screen_id );

      MywpHelper::error_require_message( '$model' , $called_text );

      return false;

    }

    $setting_data = apply_filters( 'mywp_setting_get_setting_data' , $model->get_setting_data() , $setting_screen_id , $model );

    return $setting_data;

  }

  public static function get_posts( $args = array() , $setting_screen_id = false ) {

    do_action( 'mywp_setting_before_get_posts' , $args , $setting_screen_id );

    $args = apply_filters( "mywp_setting_pre_get_posts_args_{$setting_screen_id}" , $args );
    $args = apply_filters( 'mywp_setting_pre_get_posts_args' , $args , $setting_screen_id );

    $posts = apply_filters( "mywp_setting_pre_get_posts_{$setting_screen_id}" , false , $args , $setting_screen_id );
    $posts = apply_filters( 'mywp_setting_pre_get_posts' , $posts , $args , $setting_screen_id );

    if( $posts !== false ) {

      return $posts;

    }

    $posts = MywpPostType::get_posts( $args );

    $posts = apply_filters( "mywp_setting_change_get_posts_{$setting_screen_id}" , $posts , $args );
    $posts = apply_filters( 'mywp_setting_change_get_posts' , $posts , $args , $setting_screen_id );

    do_action( 'mywp_setting_after_get_posts' , $posts , $args , $setting_screen_id );

    return $posts;

  }

  public static function print_form_item_must( $setting_screen_id = false , $action = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , ' $action' );

    if( empty( $setting_screen_id ) ) {

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      MywpHelper::error_require_message( '$action' , $called_text );

      return false;

    }

    $action = strip_tags( $action );

    printf( '<input type="hidden" name="mywp[setting_screen]" value="%s" />' , esc_attr( $setting_screen_id ) );
    printf( '<input type="hidden" name="mywp[action]" value="%s" />' , esc_attr( $action ) );

    $nonce_key = self::get_nonce_key( $setting_screen_id , $action );

    wp_nonce_field( $nonce_key , $nonce_key );

    do_action( "mywp_setting_print_form_item_must_{$setting_screen_id}_{$action}" );
    do_action( 'mywp_setting_print_form_item_must' , $setting_screen_id , $action );

  }

  public static function get_nonce_key( $setting_screen_id = false , $action = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , ' $action' );

    if( empty( $setting_screen_id ) ) {

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      MywpHelper::error_require_message( '$action' , $called_text );

      return false;

    }

    $action = strip_tags( $action );

    return sprintf( 'mywp_setting_nonce_%s_%s' , $setting_screen_id , $action );

  }

  public static function get_ajax_action_name( $setting_screen_id = false , $action = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , ' $action' );

    if( empty( $setting_screen_id ) ) {

      MywpHelper::error_require_message( '$setting_screen_id' , $called_text );

      return false;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      MywpHelper::error_require_message( '$action' , $called_text );

      return false;

    }

    $action = strip_tags( $action );

    return sprintf( 'mywp_setting_%s_%s' , $setting_screen_id , $action );

  }

  public static function is_mywp_form_action( $post_data = array() ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$post_data' );

    if( empty( $post_data ) ) {

      MywpHelper::error_require_message( '$post_data' , $called_text );

      return false;

    }

    if( empty( $post_data['mywp'] ) ) {

      return false;

    }

    if( empty( $post_data['mywp']['setting_screen'] ) ) {

      MywpHelper::error_require_message( 'mywp[setting_screen]' , $called_text );

      return false;

    }

    if( empty( $post_data['mywp']['action'] ) ) {

      MywpHelper::error_require_message( 'mywp[action]' , $called_text );

      return false;

    }

    $found_nonce = false;

    foreach( $post_data as $key => $val ) {

      if( strpos( $key , 'mywp_setting_nonce_' ) !== false ) {

        $found_nonce = true;
        break;

      }

    }

    if( $found_nonce === false ) {

      MywpHelper::error_require_message( '$found_nonce' , $called_text );

      return false;

    }

    return $found_nonce;

  }

  public static function post_data_format( $setting_screen_id = false , $action = false , $form_data = false ) {

    $mywp_notice = new MywpNotice();

    $called_text = sprintf( '%s::%s( %s , %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , '$action' , '$form_data' );

    if( empty( $setting_screen_id ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$setting_screen_id' ) );

      return $form_data;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$action' ) );

      return $form_data;

    }

    if( empty( $form_data['data'] ) or ! is_array( $form_data['data'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$form_data["data"]' ) );

      return $form_data;

    }

    $action = strip_tags( $action );

    if( empty( $form_data['data']['advance'] ) ) {

      $form_data['data']['advance'] = false;

    } else {

      $form_data['data']['advance'] = true;

    }

    $formatted_data = $form_data['data'];

    $formatted_data = apply_filters( "mywp_setting_post_data_format_{$setting_screen_id}_{$action}" , $formatted_data , $form_data['data'] );
    $formatted_data = apply_filters( 'mywp_setting_post_data_format' , $formatted_data , $form_data['data'] , $setting_screen_id , $action );

    return $formatted_data;

  }

  public static function post_data_validate( $setting_screen_id = false , $action = false , $formatted_data = false ) {

    $mywp_notice = new MywpNotice();

    $called_text = sprintf( '%s::%s( %s , %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , '$action' , '$formatted_data' );

    if( empty( $setting_screen_id ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$setting_screen_id' ) );

      return $formatted_data;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$action' ) );

      return $formatted_data;

    }

    $action = strip_tags( $action );

    if( empty( $formatted_data ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$formatted_data' ) );

      return $formatted_data;

    }

    if( ! isset( $formatted_data['advance'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$formatted_data["advance"]' ) );

      return $formatted_data;

    }

    $validated_data = $formatted_data;

    $validated_data = apply_filters( "mywp_setting_post_data_validate_{$setting_screen_id}_{$action}" , $validated_data , $formatted_data );
    $validated_data = apply_filters( 'mywp_setting_post_data_validate' , $validated_data , $formatted_data , $setting_screen_id , $action );

    return $formatted_data;

  }

  public static function post_data_action( $setting_screen_id = false , $action = false , $validated_data = false ) {

    $called_text = sprintf( '%s::%s( %s , %s , %s )' , __CLASS__ , __FUNCTION__ , '$setting_screen_id' , '$action' , '$validated_data' );

    $mywp_notice = new MywpNotice();

    if( empty( $setting_screen_id ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$setting_screen_id' ) );

      return false;

    }

    $setting_screen_id = strip_tags( $setting_screen_id );

    if( empty( $action ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$action' ) );

      return false;

    }

    $action = strip_tags( $action );

    if( empty( $validated_data ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %1$s is required for %2$s.' , 'my-wp' ) , $called_text , '$validated_data' ) );

      return false;

    }

    do_action( "mywp_setting_before_post_data_action_{$setting_screen_id}_{$action}" , $validated_data );
    do_action( 'mywp_setting_before_post_data_action' , $validated_data , $setting_screen_id , $action );

    $mywp_model = self::get_model( $setting_screen_id );

    if( ! empty( $mywp_model ) ) {

      if( $action === 'update' ) {

        $mywp_model->update_data( $validated_data );

        $mywp_notice->add_notice( __( 'Settings saved.' ) );

      } elseif( $action === 'remove' ) {

        $mywp_model->remove_data();

        $mywp_notice->add_notice( __( 'Settings saved.' ) );

      } else {

        do_action( "mywp_setting_post_data_action_custom_{$setting_screen_id}_$action" , $validated_data );
        do_action( 'mywp_setting_post_data_action_custom' , $validated_data , $setting_screen_id , $action );

      }

    }

    do_action( "mywp_setting_after_post_data_action_{$setting_screen_id}_{$action}" , $validated_data );
    do_action( 'mywp_setting_after_post_data_action' , $validated_data , $setting_screen_id , $action );

    $is_redirect = true;

    $is_redirect = apply_filters( "mywp_setting_post_data_action_redirect_{$setting_screen_id}_{$action}" , $is_redirect );
    $is_redirect = apply_filters( 'mywp_setting_post_data_action_redirect' , $is_redirect , $setting_screen_id , $action );

    return $is_redirect;

  }

}

endif;
