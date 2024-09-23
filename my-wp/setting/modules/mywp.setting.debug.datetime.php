<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugDatetime' ) ) :

final class MywpSettingScreenDebugDatetime extends MywpAbstractSettingModule {

  static protected $id = 'debug_datetime';

  static protected $priority = 100;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Date Time' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th>get_option( "date_format" )</th>
          <td><?php echo get_option( "date_format" ); ?></td>
        </tr>
        <tr>
          <th>get_option( "time_format" )</th>
          <td><?php echo get_option( "time_format" ); ?></td>
        </tr>
        <tr>
          <th>get_option( "timezone_string" )</th>
          <td><?php echo get_option( "timezone_string" ); ?></td>
        </tr>
        <tr>
          <th>get_option( "gmt_offset" )</th>
          <td><?php echo get_option( "gmt_offset" ); ?></td>
        </tr>
        <tr>
          <th>date_default_timezone_get()</th>
          <td><?php echo date_default_timezone_get(); ?></td>
        </tr>
        <tr>
          <th>ini_get( "date.timezone" )</th>
          <td><?php echo ini_get( "date.timezone" ); ?></td>
        </tr>
        <tr>
          <th>time()</th>
          <td><?php echo time(); ?></td>
        </tr>
        <tr>
          <th>date( "Y-m-d H:i:s" )</th>
          <td><?php echo date( "Y-m-d H:i:s" ); ?></td>
        </tr>
        <tr>
          <th>date( "Y-m-d H:i:s" , time() )</th>
          <td><?php echo date( "Y-m-d H:i:s" , time() ); ?></td>
        </tr>
        <tr>
          <th>date( "Y-m-d H:i:s" , time() + ( get_option( "gmt_offset" ) * HOUR_IN_SECONDS ) )</th>
          <td><?php echo date( "Y-m-d H:i:s" , time() + ( get_option( "gmt_offset" ) * HOUR_IN_SECONDS ) ); ?></td>
        </tr>
        <tr>
          <th>gmdate( "Y-m-d H:i:s" )</th>
          <td><?php echo gmdate( "Y-m-d H:i:s" ); ?></td>
        </tr>
        <tr>
          <th>strtotime( "now" )</th>
          <td><?php echo strtotime( "now" ); ?></td>
        </tr>
        <tr>
          <th>date( "Y-m-d H:i:s" , strtotime( "now" ) )</th>
          <td><?php echo date( "Y-m-d H:i:s" , strtotime( "now" ) ); ?></td>
        </tr>
        <tr>
          <th>current_time( "timestamp" )</th>
          <td><?php echo current_time( "timestamp" ); ?></td>
        </tr>
        <tr>
          <th>current_time( "timestamp" , true )</th>
          <td><?php echo current_time( "timestamp" , true ); ?></td>
        </tr>
        <tr>
          <th>date( "Y-m-d H:i:s" , current_time( "timestamp" ) )</th>
          <td><?php echo date( "Y-m-d H:i:s" , current_time( "timestamp" ) ); ?></td>
        </tr>
        <tr>
          <th>current_time( "mysql" )</th>
          <td><?php echo current_time( "mysql" ); ?></td>
        </tr>
        <tr>
          <th>current_time( "mysql" , true )</th>
          <td><?php echo current_time( "mysql" , true ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( "G" , current_time( "mysql" ) )</th>
          <td><?php echo mysql2date( "G" , current_time( "mysql" ) ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( "U" , current_time( "mysql" ) )</th>
          <td><?php echo mysql2date( "U" , current_time( "mysql" ) ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( get_option( "date_format" ) , current_time( "mysql" ) )</th>
          <td><?php echo mysql2date( get_option( "date_format" ) , current_time( "mysql" ) ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( get_option( "time_format" ) , current_time( "mysql" ) )</th>
          <td><?php echo mysql2date( get_option( "time_format" ) , current_time( "mysql" ) ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( "l, F j, Y" , current_time( "mysql" ) )</th>
          <td><?php echo mysql2date( "l, F j, Y" , current_time( "mysql" ) ); ?></td>
        </tr>
        <tr>
          <th>mysql2date( "l, F j, Y" , current_time( "mysql" ) , false )</th>
          <td><?php echo mysql2date( "l, F j, Y" , current_time( "mysql" ) , false ); ?></td>
        </tr>
        <tr>
          <th>date_i18n( "Y-m-d H:i:s" )</th>
          <td><?php echo date_i18n( "Y-m-d H:i:s" ); ?></td>
        </tr>
        <tr>
          <th>date_i18n( "Y-m-d H:i:s" , false )</th>
          <td><?php echo date_i18n( "Y-m-d H:i:s" , false ); ?></td>
        </tr>
        <tr>
          <th>date_i18n( "Y-m-d H:i:s" , false , true )</th>
          <td><?php echo date_i18n( "Y-m-d H:i:s" , false , true ); ?></td>
        </tr>
        <tr>
          <th>date_i18n( "Y-m-d H:i:s" , current_time( "timestamp" ) )</th>
          <td><?php echo date_i18n( "Y-m-d H:i:s" , current_time( "timestamp" ) ); ?></td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugDatetime::init();

endif;
