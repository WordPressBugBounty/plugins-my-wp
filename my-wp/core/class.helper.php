<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpHelper' ) ) :

final class MywpHelper {

  public static function error_after_called_message( $action = false , $called = false ) {

    if( $action === false or $called === false ) {

      return false;

    }

    $action = strip_tags( $action );

    $called = strip_tags( $called );

    self::error_message( sprintf( __( 'This function can be called after "%s" action.' , 'my-wp' ) , $action ) , $called );

  }

  public static function error_require_message( $required_val = false , $called = false ) {

    if( $required_val === false or $called === false ) {

      return false;

    }

    $required_val = strip_tags( $required_val );

    $called = strip_tags( $called );

    self::error_message( sprintf( __( 'The %s is required.' , 'my-wp' ) , $required_val ) , $called );

  }

  public static function error_not_found_message( $not_found = false , $called = false ) {

    if( $not_found === false or $called === false ) {

      return false;

    }

    $not_found = strip_tags( $not_found );

    $called = strip_tags( $called );

    self::error_message( sprintf( __( '%s is not found.' , 'my-wp' ) , $not_found ) , $called );

  }

  public static function error_returned_message( $returned = false , $message = false , $called = false ) {

    if( $returned === false or $called === false ) {

      return false;

    }

    $returned = strip_tags( $returned );

    $message = strip_tags( $message );

    $called = strip_tags( $called );

    self::error_message( sprintf( __( '%s is error %s.' , 'my-wp' ) , $returned , $message ) , $called );

  }

  public static function error_deprecated( $deprecated_message = false , $called = false ) {

    if( $deprecated_message === false or $called === false ) {

      return false;

    }

    $deprecated_message  = strip_tags( $deprecated_message );

    $called = strip_tags( $called );

    self::error_message( $deprecated_message , $called );

    if( WP_DEBUG ) {

      if( function_exists( 'wp_trigger_error' ) ) {

        $message = sprintf( 'Plugin %s(%s), %s, %s' , MYWP_NAME , MYWP_PLUGIN_BASENAME , $called , $deprecated_message );

        wp_trigger_error( '' , $message );

      }

    }

  }

  public static function error_deprecated_value( $deprecated_message = false , $called = false , $version = false ) {

    if( $deprecated_message === false or $called === false or $version === false ) {

      return false;

    }

    $deprecated_message  = strip_tags( $deprecated_message );

    $called = strip_tags( $called );

    $version = strip_tags( $version );

    $message = sprintf( '%s is deprecated since version %s.' , $deprecated_message , $version );

    self::error_deprecated( $message , $called );

  }

  public static function error_message( $message = false , $called = false ) {

    if( $message === false or $called === false ) {

      return false;

    }

    $called  = strip_tags( $called );

    $error_text = sprintf( __( '%1$s: %2$s' , 'my-wp' ) , $called , $message );

    MywpApi::add_error( $error_text );

  }

  public static function get_define( $define_name = false ) {

    if( empty( $define_name ) ) {

      $called_text = sprintf( '%1$s::%2$s( %3$s )' , __CLASS__ , __FUNCTION__ , '$define_name' );

      MywpHelper::error_not_found_message( '$define_name' , $called_text );

      return false;

    }

    $define_name = strip_tags( $define_name );

    if( ! defined( $define_name ) ) {

      return false;

    }

    return constant( $define_name );

  }

  public static function is_doing( $doing_name = false ) {

    if( empty( $doing_name ) ) {

      $called_text = sprintf( '%1$s::%2$s( %3$s )' , __CLASS__ , __FUNCTION__ , '$doing_name' );

      MywpHelper::error_not_found_message( '$doing_name' , $called_text );

      return false;

    }

    $define_name = '';

    if( 'cron' === $doing_name ) {

      $define_name = 'DOING_CRON';

    } elseif( 'xmlrpc' === $doing_name ) {

      $define_name = 'XMLRPC_REQUEST';

    } elseif( 'rest' === $doing_name ) {

      $define_name = 'REST_REQUEST';

    } elseif( 'ajax' === $doing_name ) {

      $define_name = 'DOING_AJAX';

    }

    $doing = self::get_define( $define_name );

    return $doing;

  }

  public static function get_byte( $memory = false ) {

    if( $memory === false ) {

      return false;

    }

    $memory = (int) $memory;

    if( $memory > TB_IN_BYTES ) {

      $memory = number_format( $memory / TB_IN_BYTES , 2 );

      $unit = __( 'TB' );

    } elseif( $memory > GB_IN_BYTES ) {

      $memory = number_format( $memory / GB_IN_BYTES , 2 );

      $unit = __( 'GB' );

    } elseif( $memory > MB_IN_BYTES ) {

      $memory = number_format( $memory / MB_IN_BYTES , 2 );

      $unit = __( 'MB' );

    } elseif( $memory > KB_IN_BYTES ) {

      $memory = number_format( $memory / KB_IN_BYTES , 2 );

      $unit = __( 'KB' );

    } else {

      $memory = number_format( $memory / KB_IN_BYTES , 2 );

      $unit = __( 'Bytes' );

    }

    return sprintf( '%s %s' , $memory , $unit );

  }

  public static function get_all_sites() {

    $args = array( 'number' => '' );

    return get_sites( $args );

  }

  public static function get_max_allowed_packet_size() {

    $max_allowed_packet_size = apply_filters( 'mywp_get_max_allowed_packet_size' , 1000000 );

    return $max_allowed_packet_size;

  }

  public static function set_time_limit( $seconds = 0 ) {

    $seconds = (int) $seconds;

    if( function_exists( 'set_time_limit' ) ) {

      set_time_limit( $seconds );

    }

  }

  public static function get_gmt_offset_seconds() {

    $gmt_offset = (float) get_option( 'gmt_offset' );

    $gmt_offset_seconds = (int) ( $gmt_offset * HOUR_IN_SECONDS );

    return $gmt_offset_seconds;

  }

  public static function get_wp_version() {

    global $wp_version;

    if( function_exists( 'wp_get_wp_version' ) ) {

      $version = wp_get_wp_version();

    } else {

      $version = $wp_version;

    }

    return $wp_version;

  }

  public static function sanitize_text( $value ) {

    $value = sanitize_text_field( $value );

    return $value;

  }

  public static function sanitize_number( $value ) {

    $value = self::sanitize_text( $value );

    if( $value === '0' ) {

      return 0;

    }

    if( empty( $value ) ) {

      return false;

    }

    $value = (int) $value;

    if( empty( $value ) ) {

      return false;

    }

    return $value;

  }

  public static function sanitize_date( $value ) {

    $value = self::sanitize_text( $value );

    if( empty( $value ) ) {

      return false;

    }

    $date_array = explode( '-' , $value );

    if( empty( $date_array[0] ) ) {

      return false;

    }

    $year = (int) $date_array[0];

    if( empty( $date_array[1] ) ) {

      return false;

    }

    $month = (int) $date_array[1];

    if( empty( $date_array[2] ) ) {

      return false;

    }

    $day = (int) $date_array[2];

    $date_string = sprintf( '%s-%s-%s' , $year , $month , $day );

    if( ! wp_checkdate( $month , $day , $year , $date_string ) ) {

      return false;

    }

    return $date_string;

  }

  public static function sanitize_term_ids( $taxonomy , $values ) {

    $taxonomy = sanitize_text_field( $taxonomy );

    if( empty( $taxonomy ) ) {

      return false;

    }

    if( ! taxonomy_exists( $taxonomy ) ) {

      return false;

    }

    if( empty( $values ) ) {

      return false;

    }

    if( ! is_array( $values ) ) {

      return false;

    }

    $term_ids = array();

    foreach( $values as $term_id ) {

      $term_id = MywpHelper::sanitize_number( $term_id );

      if( empty( $term_id ) ) {

        continue;

      }

      $term_exists = term_exists( $term_id , $taxonomy );

      if( empty( $term_exists['term_id'] ) ) {

        continue;

      }

      $term_ids[] = (int) $term_exists['term_id'];

    }

    return $term_ids;

  }

}

endif;
