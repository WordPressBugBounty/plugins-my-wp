<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpDeveloper' ) ) :

final class MywpDeveloper {

  public static function is_debug() {

    $is_debug = false;

    return apply_filters( 'mywp_is_debug' , $is_debug );

  }

  public static function is_debug_item( $debug_item = false ) {

    if( empty( $debug_item ) ) {

      return false;

    }

    if( ! is_string( $debug_item ) ) {

      return false;

    }

    if( ! class_exists( 'MywpControllerModuleDebugGeneral' ) ) {

      return true;

    }

    $setting_data = MywpControllerModuleDebugGeneral::get_setting_data();

    $debug_items = array(
      'mywp_cache',
      'debug_request',
      'debug_time',
      'debug_action',
      'debug_debugtrace',
    );

    if( ! in_array( $debug_item , $debug_items , true ) ) {

      return false;

    }

    $is_debug_item = false;

    if( isset( $setting_data[ $debug_item ] ) ) {

      $is_debug_item = $setting_data[ $debug_item ];

    }

    $is_debug_item = apply_filters( "mywp_is_debug_item_{$debug_item}" , $is_debug_item );
    $is_debug_item = apply_filters( 'mywp_is_debug_item' , $is_debug_item , $debug_item );

    if( ! $is_debug_item ) {

      return false;

    }

    return true;

  }

  public static function get_debug_types() {

    $debug_types = array();

    return apply_filters( 'mywp_debug_types' , $debug_types );

  }

  public static function get_debug_renders() {

    $pre_debug_renders = apply_filters( 'mywp_debug_renders' , array() );

    if( empty( $pre_debug_renders ) ) {

      return false;

    }

    $default = array(
      'debug_type' => 'custom',
      'title' => '',
    );

    $debug_renders = array();

    foreach( $pre_debug_renders as $render_id => $render ) {

      $debug_renders[ $render_id ] = wp_parse_args( $render , $default );

    }

    return $debug_renders;

  }

  public static function debug( $include_debug_modules = array() ) {

    do_action( 'mywp_developer_debug' , $include_debug_modules );

    echo "\n\n ----- mywp_developer_debug ----- \n\n";

  }

  public static function debug_die( $include_debug_modules = array() ) {

    self::debug( $include_debug_modules );

    die();

  }

  public static function debug_action( $action = false ) {

    if( $action === false ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$action' );

      MywpHelper::error_not_found_message( '$action' , $called_text );

      return false;

    }

    $action = strip_tags( $action );

    printf( 'debug_action = %s' , $action );
    echo "\n";

    printf( 'has_action = %s' , has_action( $action ) );
    echo "\n";

    printf( 'did_action = %s' , did_action( $action ) );
    echo "\n";

    echo 'actions = ' . "\n";

    $filter_to_func = self::get_filter_to_func( $action );

    if( ! empty( $filter_to_func ) ) {

      foreach( $filter_to_func as $func ) {

        printf( '  (%d) %s' , $func['priority'] , $func['print_format'] );
        echo "\n";

      }

    }

    echo "\n\n";

  }

  public static function debug_filter( $filter = false ) {

    if( $filter === false ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$filter' );

      MywpHelper::error_not_found_message( '$filter' , $called_text );

      return false;

    }

    $filter = strip_tags( $filter );

    printf( 'debug_filter = %s' , $filter );
    echo "\n";

    printf( 'has_filter = %s' , has_filter( $filter ) );
    echo "\n";

    echo 'filters = ' . "\n";

    $filter_to_func = self::get_filter_to_func( $filter );

    if( ! empty( $filter_to_func ) ) {

      foreach( $filter_to_func as $func ) {

        printf( '  (%d) %s' , $func['priority'] , $func['print_format'] );
        echo "\n";

      }

    }

    echo "\n\n";

  }

  public static function get_filter_to_func( $filter_name = false ) {

    global $wp_filter;

    if( empty( $filter_name ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$filter_name' );

      MywpHelper::error_not_found_message( '$filter_name' , $called_text );

      return false;

    }

    $wp_filters = $wp_filter;

    if( empty( $wp_filters[ $filter_name ] ) ) {

      return false;

    }

    $filter_to_func = array();

    $defaults = array(
      'priority' => false,
      'function' => false,
      'class' => false,
      'static' => false,
      'print_format' => false,
    );

    foreach( $wp_filters[ $filter_name ] as $priority => $filters ) {

      foreach( $filters as $filter_func_name => $filter_array ) {

        if( is_null( $filter_array['function'] ) ) {

          continue;

        }

        $filter_function = $defaults;

        $filter_function['priority'] = $priority;

        $print_format = '';

        if( is_array( $filter_array['function'] ) ) {

          $filter_function['function'] = $filter_array['function'][1];

          if( is_object( $filter_array['function'][0] ) ) {

            $filter_function['class'] = get_class( $filter_array['function'][0] );
            $print_format = sprintf( '[Class / Object] %s -> %s()' , $filter_function['class'] , $filter_function['function'] );

          } else {

            $filter_function['class'] = $filter_array['function'][0];
            $filter_function['static'] = 1;
            $print_format = sprintf( '[Class / Object] %s :: %s()' , $filter_function['class'] , $filter_function['function'] );

          }

        } elseif( is_object( $filter_array['function'] ) ) {

          $filter_function['class'] = get_class( $filter_array['function'] );
          $print_format = sprintf( '[Class / Object] %s' , print_r( $filter_array['function'] , true ) );

        } else {

          $filter_function['function'] = $filter_array['function'];
          $print_format = sprintf( '%s()' , $filter_function['function'] );

        }

        $filter_function['print_format'] = $print_format;

        $filter_to_func[] = $filter_function;

      }

    }

    return $filter_to_func;

  }

  public static function get_current_filter() {

    $filter = current_filter();

    if( empty( $filter ) ) {

      $called_text = sprintf( '%s::%s()' , __CLASS__ );

      MywpHelper::error_not_found_message( 'filter' , $called_text );

      return false;

    }

    return $filter;

  }

  public static function exists_function( $functions = false ) {

    if( empty( $functions ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$functions' );

      MywpHelper::error_not_found_message( '$functions' , $called_text );

      return false;

    }

    if( is_array( $functions ) ) {

      foreach( $functions as $function_name ) {

        self::exists_function( $function_name );

      }

    } else {

      $exists = false;

      if( function_exists( $functions ) ) {

        $exists = true;

      }

      printf( '%s: %s' , $functions , $exists );
      echo "\n";

    }

  }

  public static function exists_define( $defines = false ) {

    if( empty( $defines ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$defines' );

      MywpHelper::error_not_found_message( '$defines' , $called_text );

      return false;

    }

    if( is_array( $defines ) ) {

      foreach( $defines as $define_name ) {

        self::exists_define( $define_name );

      }

    } else {

      $exists = false;

      if( defined( $defines ) ) {

        $exists = true;

      }

      printf( '%s: %s' , $defines , $exists );
      echo "\n";

    }

  }

  public static function get_process() {

    $load_avg = false;

    if( function_exists( 'sys_getloadavg' ) ) {

      $sys_getloadavg = sys_getloadavg();

      if( isset( $sys_getloadavg[0] ) ) {

        $load_avg = $sys_getloadavg[0];

      }

    }

    return array( 'microtime' => microtime( true ) , 'memory_get_usage' => memory_get_usage() , 'load_avg' => $load_avg );

  }

}

endif;
