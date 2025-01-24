<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevTimes' ) ) :

final class MywpDeveloperModuleDevTimes extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_times';

  static protected $priority = 100;

  static private $init_process;

  protected static function after_init() {

    global $timestart;

    if( ! MywpDeveloper::is_debug_item( 'debug_time' ) ) {

      return false;

    }

    $init_process = MywpDeveloper::get_process();

    $define_microtime = MywpHelper::get_define( 'MYWP_DEVELOPER_MICROTIME' );

    if( ! empty( $define_microtime ) ) {

      $init_process['microtime'] = $define_microtime;

    } elseif ( ! empty( $timestart ) ) {

      $init_process['microtime'] = $timestart;

    }

    $define_memory_get_usage = MywpHelper::get_define( 'MYWP_DEVELOPER_MEMORY_GET_USAGE' );

    if( ! empty( $define_memory_get_usage ) ) {

      $init_process['memory_get_usage'] = $define_memory_get_usage;

    }

    $define_load_avg = MywpHelper::get_define( 'MYWP_DEVELOPER_LOAD_AVG' );

    if( ! empty( $define_load_avg ) ) {

      $init_process['load_avg'] = $define_load_avg;

    }

    self::$init_process = $init_process;

    self::add_actions( 'plugins_loaded' );
    self::add_actions( 'setup_theme' );
    self::add_actions( 'after_setup_theme' );
    self::add_actions( 'init' );
    self::add_actions( 'wp_loaded' );

    if( is_admin() ) {

      self::add_actions( 'admin_menu' );
      self::add_actions( 'admin_init' );
      self::add_actions( 'admin_head' );
      self::add_actions( 'admin_footer' );

    } else {

      self::add_actions( 'parse_request' );
      self::add_actions( 'wp' );
      self::add_actions( 'template_redirect' );
      self::add_actions( 'wp_head' );
      self::add_actions( 'wp_footer' );

    }

  }

  private static function add_actions( $action_hook_name = false ) {

    if( empty( $action_hook_name ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , '$action_hook_name' );

      MywpHelper::error_not_found_message( '$action_hook_name' , $called_text );

      return false;

    }

    if( $action_hook_name !== 'plugins_loaded' ) {

      add_action( $action_hook_name , array( __CLASS__ , 'process_set_1' ) , -10 );
      add_action( $action_hook_name , array( __CLASS__ , 'process_set_10' ) , 10 );

    }

    add_action( $action_hook_name , array( __CLASS__ , 'process_set_100' ) , 100 );
    add_action( $action_hook_name , array( __CLASS__ , 'process_set_10000' ) , 10000 );

  }

  private static function set_process( $priority ) {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    if( ! MywpDeveloper::is_debug_item( 'debug_time' ) ) {

      return false;

    }

    $current_filter = MywpDeveloper::get_current_filter();

    if( empty( $current_filter ) ) {

      return false;

    }

    $processes = self::get_processes();

    $processes[ $current_filter ][ $priority ] = MywpDeveloper::get_process();

    $mywp_cache = new MywpCache( 'mywp_times' );

    $mywp_cache->update_cache( $processes );

  }

  private static function get_processes() {

    $mywp_cache = new MywpCache( 'mywp_times' );

    $procecces = $mywp_cache->get_cache();

    if( empty( $procecces ) ) {

      $procecces = array();

    }

    return $procecces;

  }

  private static function get_process( $filter_name = false ) {

    if( empty( $filter_name ) ) {

      return false;

    }

    $processes = self::get_processes();

    if( empty( $processes[ $filter_name ] ) ) {

      return false;

    }

    return $processes[ $filter_name ];

  }

  public static function process_set_1() {

    self::set_process( -1 );

  }

  public static function process_set_10() {

    self::set_process( 10 );

  }

  public static function process_set_100() {

    self::set_process( 100 );

  }

  public static function process_set_10000() {

    self::set_process( 10000 );

  }

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Action Times' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function mywp_developer_debug() {

    if( ! MywpDeveloper::is_debug_item( 'debug_time' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $processes = self::get_processes();

    if( empty( $processes ) ) {

      return false;

    }

    $first_process = self::get_first_process();

    if( empty( $first_process ) ) {

      return false;

    }

    printf( '%s = ' , __( 'All' ) );

    echo self::print_format( $first_process );

    printf( 'Memory peak: %s' , MywpHelper::get_byte( memory_get_peak_usage() ) );

    $before_screen_process = self::get_before_screen_process();

    if( empty( $before_screen_process ) ) {

      return false;

    }

    printf( '%s = ' , __( 'Before Screen' , 'my-wp' ) );

    echo self::print_format( $before_screen_process );

    $screen_process = self::get_screen_process();

    if( empty( $screen_process ) ) {

      return false;

    }

    printf( '%s = ' , __( 'Screen' , 'my-wp' ) );

    echo self::print_format( $screen_process );

    foreach( $processes as $action_hook_name => $priority_processes ) {

      echo $action_hook_name . "\n";

      $last_priority_process = end( $priority_processes );
      $first_priority_process = reset( $priority_processes );

      $total_screen_process = self::format_process( $first_priority_process , $last_priority_process );

      printf( '%s = ' , __( 'Total' , 'my-wp' ) );

      echo self::print_format( $total_screen_process );

      $priority_processes_count = count( $priority_processes );
      $priority_processes_keys = array_keys( $priority_processes );

      for( $i = 0; $i < ( $priority_processes_count -1 ); ++$i ) {

        $current_priority = $priority_processes_keys[ $i ];

        $next_priority = $priority_processes_keys[ ( $i + 1 ) ];

        $current_process = $priority_processes[ $current_priority ];

        $next_process = $priority_processes[ $next_priority ];

        $diff_process = self::format_process( $current_process , $next_process );

        printf( '[%s] - [%s] = ' , $current_priority , $next_priority );

        echo self::print_format( $diff_process );

        echo self::print_filters( $action_hook_name , $current_priority , $next_priority );

      }

    }

  }

  protected static function mywp_debug_render() {

    if( ! MywpDeveloper::is_debug_item( 'debug_time' ) ) {

      echo esc_html( __( 'Not activated.' , 'my-wp' ) );

      return false;

    }

    $processes = self::get_processes();

    if( empty( $processes ) ) {

      return false;

    }

    echo '<table class="debug-table">';

    $first_process = self::get_first_process();

    if( empty( $first_process ) ) {

      return false;

    }

    echo '<tr>';

    printf( '<th>%s</th>' , __( 'All' ) );

    printf( '<td>%s' , self::print_format( $first_process ) );

    printf( ' - Memory peak: %s</td>' , MywpHelper::get_byte( memory_get_peak_usage() ) );

    echo '</tr>';

    echo '</table>';

    echo '<table class="debug-table">';

    $before_screen_process = self::get_before_screen_process();

    if( empty( $before_screen_process ) ) {

      return false;

    }

    echo '<tr>';

    printf( '<th>%s</th>' , __( 'Before Screen' , 'my-wp' ) );

    printf( '<td>%s</td>' , self::print_format( $before_screen_process ) );

    echo '</tr>';

    $screen_process = self::get_screen_process();

    if( empty( $screen_process ) ) {

      return false;

    }

    echo '<tr>';

    printf( '<th>%s</th>' , __( 'Screen' , 'my-wp' ) );

    printf( '<td>%s</td>' , self::print_format( $screen_process ) );

    echo '</tr>';

    echo '</table>';

    foreach( $processes as $action_hook_name => $priority_processes ) {

      printf( '<p>%s</p>' , $action_hook_name );

      echo '<table class="debug-table">';

      $last_priority_process = end( $priority_processes );
      $first_priority_process = reset( $priority_processes );

      $total_screen_process = self::format_process( $first_priority_process , $last_priority_process );

      echo '<tr>';

      printf( '<th>%s</th>' , __( 'Total' , 'my-wp' ) );

      printf( '<td>%s</td>' , self::print_format( $total_screen_process ) );

      echo '</tr>';

      $priority_processes_count = count( $priority_processes );
      $priority_processes_keys = array_keys( $priority_processes );

      for( $i = 0; $i < ( $priority_processes_count -1 ); ++$i ) {

        $current_priority = $priority_processes_keys[ $i ];

        $next_priority = $priority_processes_keys[ ( $i + 1 ) ];

        $current_process = $priority_processes[ $current_priority ];

        $next_process = $priority_processes[ $next_priority ];

        $diff_process = self::format_process( $current_process , $next_process );

        echo '<tr>';

        printf( '<th>%s</th>' , sprintf( '[%s] - [%s]' , $current_priority , $next_priority )  );

        printf( '<td>%s' , self::print_format( $diff_process ) );

        echo self::print_filters( $action_hook_name , $current_priority , $next_priority , true );

        echo '</td>';

        echo '</tr>';

      }

      echo '</table>';

    }

    //printf( '<pre>%s</pre>' , print_r( $processes , true ) );

  }

  private static function get_first_process() {

    $processes = self::get_processes();

    if( empty( $processes ) ) {

      return false;

    }

    $init_process = self::$init_process;

    $last_process_priorities = end( $processes );

    $last_process = end( $last_process_priorities );

    return self::format_process( $init_process , $last_process );

  }

  private static function get_before_screen_process() {

    $processes = self::get_processes();

    if( empty( $processes ) ) {

      return false;

    }

    $init_process = self::$init_process;

    $before_screen_process = false;

    if( ! empty( $processes['admin_head'][-1] ) ) {

      $before_screen_process = $processes['admin_head'][-1];

    } elseif( ! empty( $processes['wp_head'][-1] ) ) {

      $before_screen_process = $processes['wp_head'][-1];

    }

    return self::format_process( $init_process , $before_screen_process );

  }

  private static function get_screen_process() {

    $processes = self::get_processes();

    if( empty( $processes ) ) {

      return false;

    }

    $before_screen_process = false;

    if( ! empty( $processes['admin_head'][-1] ) ) {

      $before_screen_process = $processes['admin_head'][-1];

    } elseif( ! empty( $processes['wp_head'][-1] ) ) {

      $before_screen_process = $processes['wp_head'][-1];

    }

    $last_process_priorities = end( $processes );

    $last_process = end( $last_process_priorities );

    return self::format_process( $before_screen_process , $last_process );

  }

  private static function format_process( $start_process = false , $end_process = false ) {

    if( empty( $start_process ) or empty( $end_process ) ) {

      return false;

    }

    $format_process = array(
      'start' => $start_process,
      'end' => $end_process,
      'second' => ( $end_process['microtime'] - $start_process['microtime'] ),
      'memory' => ( $end_process['memory_get_usage'] - $start_process['memory_get_usage'] ),
      'load_avg' => $start_process['load_avg'],
    );

    return $format_process;

  }

  private static function print_format( $process ) {

    if( ! isset( $process['second'] ) or ! isset( $process['memory'] ) ) {

      return false;

    }

    if( function_exists( 'sys_getloadavg' ) ) {

      $print_format = sprintf( 'Time: %s - Memory: %s - LoadAvg: %s' , self::get_second( $process['second'] ) , MywpHelper::get_byte( $process['memory'] ) , strip_tags( $process['load_avg'] ) );

    } else {

      $print_format = sprintf( 'Time: %s - Memory: %s' , self::get_second( $process['second'] ) , MywpHelper::get_byte( $process['memory'] ) );

    }

    $print_format .= "\n";

    return $print_format;

  }

  private static function print_filters( $action_hook_name , $from_priority , $to_priority , $is_list = false ) {

    if( empty( $action_hook_name ) ) {

      return false;

    }

    $filter_to_func = MywpDeveloper::get_filter_to_func( $action_hook_name );

    if( empty( $filter_to_func ) ) {

      return false;

    }

    $print_filters = false;

    if( $is_list ) {

      $print_filters = '<textarea readonly="readonly" style="height: 30px;">';

    }

    foreach( $filter_to_func as $func ) {

      if( $func['priority'] >= $from_priority && $func['priority'] <= $to_priority ) {

        $print_filters .= sprintf( '(%s) %s' , $func['priority'] , $func['print_format'] );

        $print_filters .= "\n";

      }

    }

    if( $is_list ) {

      $print_filters .= '</textarea>';

    } else {

      $print_filters .= "\n";

    }

    return $print_filters;

  }

  private static function get_second( $second = false ) {

    if( $second === false ) {

      return false;

    }

    $decimals = 3;

    $number = false;

    if( function_exists( 'number_format_i18n' ) ) {

      $number = number_format_i18n( $second , $decimals );

    } else {

      $number = number_format( $second , $decimals );

    }

    return "{$number} seconds";

  }

}

MywpDeveloperModuleDevTimes::init();

endif;
