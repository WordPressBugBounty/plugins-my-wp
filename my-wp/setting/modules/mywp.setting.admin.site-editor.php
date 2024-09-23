<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminSiteEditor' ) ) :

final class MywpSettingScreenAdminSiteEditor extends MywpAbstractSettingModule {

  static protected $id = 'admin_site_editor';

  static protected $priority = 130;

  static private $menu = 'admin';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Site Editor' ),
      'menu' => self::$menu,
      'controller' => 'admin_site_editor',
      'document_url' => self::get_document_url( 'document/admin-site-editor/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'General' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Top left button' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][top_left_icon]" class="top_left_icon" value="1" <?php checked( $setting_data['top_left_icon'] , true ); ?> />
              <?php _e( 'Change to back icon' , 'my-wp' ); ?>
            </label>
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

    if( ! empty( $formatted_data['top_left_icon'] ) ) {

      $new_formatted_data['top_left_icon'] = true;

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenAdminSiteEditor::init();

endif;
