<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugSiteOptions' ) ) :

final class MywpSettingScreenDebugSiteOptions extends MywpAbstractSettingModule {

  static protected $id = 'network_debug_site_options';

  static protected $priority = 20;

  static private $menu = 'network_debug';

  public static function mywp_setting_screens( $setting_screens ) {

    if( is_multisite() ) {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'All Site Options' , 'my-wp' ),
        'menu' => self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    if( is_multisite() ) {

      if( ! is_network_admin() ) {

        return false;

      }

    } else {

      return false;

    }

    global $wpdb;

    $site_options = $wpdb->get_results( "SELECT * FROM {$wpdb->sitemeta} WHERE meta_key NOT LIKE '%transient_%' ORDER BY meta_key ASC" );

    if( empty( $site_options ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $site_options ); ?></p>
    <table class="form-table">
      <thead>
        <tr>
          <th><?php _e( 'Site ID' , 'my-wp' ); ?></th>
          <td>meta_key</td>
          <td>meta_value(<?php echo _e( 'Raw' , 'my-wp' ); ?>)</td>
          <td>meta_value(<?php echo _e( 'Decode' , 'my-wp' ); ?>)</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $site_options as $site_option ) : ?>
          <?php $meta_value = maybe_unserialize( $site_option->meta_value ); ?>
          <?php $meta_value_json = ''; ?>
          <?php if( ! empty( $site_option->meta_value ) && ! is_array( $site_option->meta_value ) ) : ?>
            <?php $meta_value_json = json_decode( $site_option->meta_value ); ?>
          <?php endif; ?>
          <tr>
            <th>
              [<?php echo $site_option->site_id; ?>]
            </th>
            <th>
              [<?php echo $site_option->meta_id; ?>] <?php echo $site_option->meta_key; ?>
              <?php if( is_array( $meta_value ) or is_object( $meta_value ) ) : ?>
                <p><code style="background-color: #F4EFCC;">Serialize</code></p>
              <?php elseif( ! empty( $meta_value_json ) && is_object( $meta_value_json ) ) : ?>
                <p><code style="background-color: #CDF3D1;">Json</code></p>
              <?php endif; ?>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $site_option->meta_value ); ?></textarea>
            </td>
            <td>
              <?php if( is_array( $meta_value ) or is_object( $meta_value ) ) : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $meta_value ); ?></textarea>
              <?php elseif( ! empty( $meta_value_json ) && is_object( $meta_value_json ) ) : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $meta_value_json ); ?></textarea>
              <?php else : ?>
                <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php echo esc_html( $meta_value ); ?></textarea>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugSiteOptions::init();

endif;
