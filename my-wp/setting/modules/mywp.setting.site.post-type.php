<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenSitePostType' ) ) :

final class MywpSettingScreenSitePostType extends MywpAbstractSettingModule {

  static protected $id = 'site_post_type';

  static protected $priority = 100;

  static private $menu = 'site';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Post Type' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'site_post_type',
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Change the capability for create_posts' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][change_cap_create_posts]" class="change_cap_create_posts" value="1" <?php checked( $setting_data['change_cap_create_posts'] , true ); ?> />
              <?php _e( 'Change' ); ?>
            </label>
            <p class="mywp-description">
              <span class="dashicons dashicons-lightbulb"></span>
              <?php _e( 'Change the "create_posts" capability example to create_posts from edit_posts.' , 'my-wp' ); ?>
            </p>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['change_cap_create_posts'] ) ) {

      $new_formatted_data['change_cap_create_posts'] = true;

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenSitePostType::init();

endif;
