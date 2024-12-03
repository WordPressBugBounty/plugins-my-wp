<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugCrons' ) ) :

final class MywpSettingScreenDebugCrons extends MywpAbstractSettingModule {

  static protected $id = 'debug_crons';

  static protected $priority = 80;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Crons' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  private static function get_crons() {

    $crons = _get_cron_array();

    return $crons;

  }

  public static function mywp_current_setting_screen_content() {

    $all_crons = self::get_crons();

    if( empty( $all_crons ) ) {

      return false;

    }

    $timezone_format = _x( 'Y-m-d H:i:s' , 'timezone date format' );
    $timezone = get_option( 'timezone_string' );

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_crons ); ?></p>
    <table class="form-table">
      <thead>
        <th><?php _e( 'Name' ); ?></th>
        <th><?php printf( __( 'Run %s' ) , 'Cron' ); ?></th>
        <th><?php _e( 'Data' ); ?></th>
      </thead>
      <tbody>

        <?php foreach( $all_crons as $timestamp => $cron ) : ?>

          <tr>
            <th>
              <?php echo esc_html( key( $cron ) ); ?>
            </th>
            <td>
              <p><?php echo date( $timezone_format , $timestamp + MywpHelper::get_gmt_offset_seconds() ); ?> (<?php echo $timezone; ?>)</p>
              <input type="text" readonly="readonly" class="large-text" value="<?php echo esc_attr( $timestamp ); ?>" /><br />
              <?php _e( 'RAW' ); ?>: <?php echo date( $timezone_format , $timestamp ); ?> (<?php _e( 'UTC' ); ?>)
            </td>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 160px;"><?php echo esc_textarea( print_r( $cron , true ) ); ?></textarea>
            </td>
          </tr>

        <?php endforeach; ?>

      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugCrons::init();

endif;
