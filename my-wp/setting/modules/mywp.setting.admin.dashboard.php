<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminDashboard' ) ) :

final class MywpSettingScreenAdminDashboard extends MywpAbstractSettingModule {

  static protected $id = 'admin_dashboard';

  static protected $priority = 20;

  static private $menu = 'admin';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Dashboard' ),
      'menu' => self::$menu,
      'controller' => 'admin_dashboard',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-dashboard/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $meta_boxes_setting = array();

    if( ! empty( $setting_data['meta_boxes'] ) ) {

      $meta_boxes_setting = $setting_data['meta_boxes'];

    }

    MywpSettingMetaBox::set_current_meta_box_screen_id( 'dashboard' );
    MywpSettingMetaBox::set_current_meta_box_screen_url( admin_url( 'index.php' ) );
    MywpSettingMetaBox::set_current_meta_box_setting_data( $meta_boxes_setting );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Management of meta boxes' , 'my-wp' ); ?></h3>

    <?php MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-management-meta-boxes.php' ); ?>

    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'General' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Welcome to WordPress!' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_welcome_panel]" class="hide_welcome_panel" value="1" <?php checked( $setting_data['hide_welcome_panel'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Re-arrange meta boxes' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][prevent_meta_box]" class="prevent_meta_box" value="1" <?php checked( $setting_data['prevent_meta_box'] , true ); ?> />
              <?php _e( 'Prevent' , 'my-wp' ); ?>
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

    $meta_box_default = MywpControllerModuleAdminDashboard::get_meta_box_default();

    if( ! empty( $formatted_data['meta_boxes'] ) ) {

      foreach( $formatted_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

        $meta_box_id = strip_tags( $meta_box_id );

        $new_meta_box_setting = $meta_box_default;

        $new_meta_box_setting['action'] = strip_tags( $meta_box_setting['action'] );

        if( ! empty( $meta_box_setting['title'] ) ) {

          $new_meta_box_setting['title'] = wp_unslash( $meta_box_setting['title'] );

        }

        $new_formatted_data['meta_boxes'][ $meta_box_id ] = $new_meta_box_setting;

      }

    }

    if( ! empty( $formatted_data['hide_welcome_panel'] ) ) {

      $new_formatted_data['hide_welcome_panel'] = true;

    }

    if( ! empty( $formatted_data['prevent_meta_box'] ) ) {

      $new_formatted_data['prevent_meta_box'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    MywpSettingMetaBox::set_current_meta_box_screen_id( 'dashboard' );

    MywpSettingMetaBox::delete_current_meta_boxes();

  }

}

MywpSettingScreenAdminDashboard::init();

endif;
