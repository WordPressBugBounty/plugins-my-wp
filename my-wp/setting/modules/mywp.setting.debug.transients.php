<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugTransients' ) ) :

final class MywpSettingScreenDebugTransients extends MywpAbstractSettingModule {

  static protected $id = 'debug_transients';

  static protected $priority = 70;

  static private $menu = 'debug';

  protected static function after_init() {

    $id = 'network_' . self::$id;

    add_action( "mywp_setting_screen_content_{$id}" , array( __CLASS__ , 'mywp_current_setting_screen_content' ) );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Transients' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    if( is_multisite() ) {

      $setting_screens[ 'network_' . self::$id ] = array(
        'title' => __( 'All Transients' , 'my-wp' ),
        'menu' => 'network_' . self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  private static function get_transients_network() {

    global $wpdb;

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->sitemeta} WHERE meta_key LIKE '%_site_transient_%' ORDER BY meta_id DESC" );

    if( empty( $results ) ) {

      return false;

    }

    $maybe_transients = array();

    foreach( $results as $result ) {

      $maybe_transients[ $result->meta_key ] = $result;

    }

    $transients = array();

    foreach( $maybe_transients as $meta_key => $maybe_transient ) {

      if( strpos( $meta_key , '_site_transient_timeout_' ) !== false ) {

        continue;

      }

      $timeout_meta_key = preg_replace( '/_site_transient_/' , '_site_transient_timeout_' , $meta_key );

      $timeout_transient = array();

      if( isset( $maybe_transients[ $timeout_meta_key ] ) ) {

        $timeout_transient = $maybe_transients[ $timeout_meta_key ];

      }

      $transient = array(
        'id' => $maybe_transient->meta_id,
        'name' => $maybe_transient->meta_key,
        'value' => $maybe_transient->meta_value,
      );

      if( ! empty( $timeout_transient ) ) {

        $transient['timeout']['id'] = $timeout_transient->meta_id;
        $transient['timeout']['name'] = $timeout_transient->meta_key;
        $transient['timeout']['value'] = $timeout_transient->meta_value;

      }

      $transients[] = $transient;

    }

    return $transients;

  }

  private static function get_transients_single() {

    global $wpdb;

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%' ORDER BY option_id DESC" );

    if( empty( $results ) ) {

      return false;

    }

    $maybe_transients = array();

    foreach( $results as $result ) {

      $maybe_transients[ $result->option_name ] = $result;

    }

    $transients = array();

    foreach( $maybe_transients as $option_name => $maybe_transient ) {

      if( strpos( $option_name , '_transient_timeout_' ) !== false ) {

        continue;

      }

      $timeout_option_name = preg_replace( '/_transient_/' , '_transient_timeout_' , $option_name );

      $timeout_transient = array();

      if( isset( $maybe_transients[ $timeout_option_name ] ) ) {

        $timeout_transient = $maybe_transients[ $timeout_option_name ];

      }

      $transient = array(
        'id' => $maybe_transient->option_id,
        'name' => $maybe_transient->option_name,
        'value' => $maybe_transient->option_value,
      );

      if( ! empty( $timeout_transient ) ) {

        $transient['timeout']['id'] = $timeout_transient->option_id;
        $transient['timeout']['name'] = $timeout_transient->option_name;
        $transient['timeout']['value'] = $timeout_transient->option_value;

      }

      $transients[] = $transient;

    }

    return $transients;

  }

  private static function get_transients() {

    if( is_network_admin() ) {

      $transients = self::get_transients_network();

    } else {

      $transients = self::get_transients_single();

    }

    return $transients;

  }

  public static function mywp_current_setting_screen_content() {

    $all_transients = self::get_transients();

    if( empty( $all_transients ) ) {

      return false;

    }

    $timezone_format = _x( 'Y-m-d H:i:s' , 'timezone date format' );
    $timezone = get_option( 'timezone_string' );

    ?>

    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_transients ); ?></p>

    <table class="form-table">
      <thead>
        <th><?php _e( 'Name' ); ?></th>
        <th><?php _e( 'Expiration' ); ?></th>
        <th><?php _e( 'Data' ); ?></th>
      </thead>
      <tbody>

        <?php foreach( $all_transients as $transient ) : ?>

          <tr>
            <th>
              [<?php echo $transient['id']; ?>]
              <?php echo $transient['name']; ?><br />
              <p class="transient-timeout">
                <?php if( ! empty( $transient['timeout'] ) ) : ?>
                  [<?php echo $transient['timeout']['id']; ?>]
                  <?php echo $transient['timeout']['name']; ?><br />
                <?php else : ?>
                  <?php _e( 'Not found timeout date.' , 'my-wp' ); ?>
                <?php endif; ?>
              </p>
            </th>
            <td>
              <?php if( ! empty( $transient['timeout'] ) ) : ?>
                <p><?php echo date( $timezone_format , $transient['timeout']['value'] + MywpHelper::get_gmt_offset_seconds() ); ?> (<?php echo $timezone; ?>)</p>
                <input type="text" readonly="readonly" class="large-text" value="<?php echo esc_attr( $transient['timeout']['value'] ); ?>" /><br />
              <?php else : ?>
                <?php _e( 'Not found timeout date.' , 'my-wp' ); ?>
              <?php endif; ?>

              <p class="transient-timeout">
                <?php if( ! empty( $transient['timeout'] ) ) : ?>
                  <?php _e( 'RAW' ); ?>: <?php echo date( $timezone_format , $transient['timeout']['value'] ); ?> (<?php _e( 'UTC' ); ?>)<br />
                <?php endif; ?>
              </p>
            </td>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 160px;"><?php echo esc_textarea( print_r( maybe_unserialize( $transient['value'] ) , true ) ); ?></textarea>
            </td>
          </tr>

        <?php endforeach; ?>

      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_admin_print_styles() {

    ?>
    <style>
    .transient-timeout {
      color: #999;
      font-size: 0.9em;
    }
    </style>
    <?php

  }

}

MywpSettingScreenDebugTransients::init();

endif;
