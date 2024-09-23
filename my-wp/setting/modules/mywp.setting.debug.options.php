<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugOptions' ) ) :

final class MywpSettingScreenDebugOptions extends MywpAbstractSettingModule {

  static protected $id = 'debug_options';

  static protected $priority = 90;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Options' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    global $wpdb;

    $options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name NOT LIKE '%transient_%' ORDER BY option_name ASC" );

    if( empty( $options ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $options ); ?></p>
    <table class="form-table">
      <thead>
        <tr>
          <th>option_name</th>
          <td>option_value(<?php echo _e( 'Raw' , 'my-wp' ); ?>)</td>
          <td>option_value(<?php echo _e( 'Decode' , 'my-wp' ); ?>)</td>
          <td style="width: 10%;">autoload</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $options as $key => $option ) : ?>
          <?php $option_value = maybe_unserialize( $option->option_value ); ?>
          <?php $option_value_json = ''; ?>
          <?php if( ! empty( $option->option_value ) && ! is_array( $option->option_value ) ) : ?>
            <?php $option_value_json = json_decode( $option->option_value ); ?>
          <?php endif; ?>
          <tr>
            <th>
              [<?php echo $option->option_id; ?>] <?php echo $option->option_name; ?>
              <?php if( is_array( $option_value ) or is_object( $option_value ) ) : ?>
                <p><code style="background-color: #F4EFCC;">Serialize</code></p>
              <?php elseif( ! empty( $option_value_json ) && is_object( $option_value_json ) ) : ?>
                <p><code style="background-color: #CDF3D1;">Json</code></p>
              <?php endif; ?>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $option->option_value ); ?></textarea>
            </td>
            <td>
              <?php if( is_array( $option_value ) or is_object( $option_value ) ) : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $option_value ); ?></textarea>
              <?php elseif( ! empty( $option_value_json ) && is_object( $option_value_json ) ) : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $option_value_json ); ?></textarea>
              <?php else : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php echo esc_html( $option_value ); ?></textarea>
              <?php endif; ?>
            </td>
            <td>
              <?php echo $option->autoload; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugOptions::init();

endif;
