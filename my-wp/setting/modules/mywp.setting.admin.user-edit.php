<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminUserEdit' ) ) :

final class MywpSettingScreenAdminUserEdit extends MywpAbstractSettingModule {

  static protected $id = 'admin_user_edit';

  static protected $priority = 120;

  static private $menu = 'admin';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Edit User' , 'my-wp' ) . '/' . __( 'Profile' ),
      'menu' => self::$menu,
      'controller' => 'admin_user_edit',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-user-edit/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $fields = array(
      'hide_rich_editing' => __( 'Visual Editor' ),
      'hide_syntax_highlighting' => __( 'Syntax Highlighting' ),
      'hide_admin_color' => __( 'Admin Color Scheme' ),
      'hide_comment_shortcuts' => __( 'Keyboard Shortcuts' ),
      'hide_toolbar' => __( 'Toolbar' ),
      'hide_language' => __( 'Language' ),
      'hide_url' => __( 'Website' ),
      'hide_description' => __( 'Biographical Info' ),
      'hide_picture' => __( 'Profile Picture' ),
      'hide_session' => __( 'Sessions' ),
      'hide_application_passwords' => __( 'Application Passwords' ),
    );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'General' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php foreach( $fields as $field_name => $field_label ) : ?>
          <tr>
            <th><?php echo $field_label; ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][<?php echo esc_attr( $field_name ); ?>]" class="<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( $setting_data[ $field_name ] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    $mywp_user = new MywpUser();

    $user = $mywp_user->get_user_data();

    $contact_fields = wp_get_user_contact_methods( $user );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Other' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php if( ! empty( $contact_fields ) ) : ?>
          <?php foreach( $contact_fields as $field_name => $field_label ) : ?>
            <?php $checked = false; ?>
            <?php if( ! empty(  $setting_data['hide_contact_fields'][ $field_name ] ) ) : ?>
              <?php $checked = true; ?>
            <?php endif; ?>
            <tr>
              <th><?php echo strip_tags( $field_label ); ?></th>
              <td>
                <label>
                  <input type="checkbox" name="mywp[data][hide_contact_fields][<?php echo esc_attr( $field_name ); ?>]" class="hide_contact_fields-<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( $checked , true ); ?> />
                  <?php _e( 'Hide' ); ?>
                </label>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td>
              <?php _e( 'Not found contact methods.' , 'my-wp' ); ?>
            </td>
          </tr>
        <?php endif; ?>
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

    if( ! empty( $formatted_data['hide_rich_editing'] ) ) {

      $new_formatted_data['hide_rich_editing'] = true;

    }

    if( ! empty( $formatted_data['hide_syntax_highlighting'] ) ) {

      $new_formatted_data['hide_syntax_highlighting'] = true;

    }

    if( ! empty( $formatted_data['hide_admin_color'] ) ) {

      $new_formatted_data['hide_admin_color'] = true;

    }

    if( ! empty( $formatted_data['hide_comment_shortcuts'] ) ) {

      $new_formatted_data['hide_comment_shortcuts'] = true;

    }

    if( ! empty( $formatted_data['hide_toolbar'] ) ) {

      $new_formatted_data['hide_toolbar'] = true;

    }

    if( ! empty( $formatted_data['hide_language'] ) ) {

      $new_formatted_data['hide_language'] = true;

    }

    if( ! empty( $formatted_data['hide_url'] ) ) {

      $new_formatted_data['hide_url'] = true;

    }

    if( ! empty( $formatted_data['hide_description'] ) ) {

      $new_formatted_data['hide_description'] = true;

    }

    if( ! empty( $formatted_data['hide_picture'] ) ) {

      $new_formatted_data['hide_picture'] = true;

    }

    if( ! empty( $formatted_data['hide_session'] ) ) {

      $new_formatted_data['hide_session'] = true;

    }

    if( ! empty( $formatted_data['hide_application_passwords'] ) ) {

      $new_formatted_data['hide_application_passwords'] = true;

    }

    if( ! empty( $formatted_data['hide_contact_fields'] ) ) {

      foreach( $formatted_data['hide_contact_fields'] as $field_name => $field_val ) {

        $field_name = strip_tags( $field_name );

        $new_formatted_data['hide_contact_fields'][ $field_name ] = true;

      }

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenAdminUserEdit::init();

endif;
