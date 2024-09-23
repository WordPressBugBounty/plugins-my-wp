<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugCapabilities' ) ) :

final class MywpSettingScreenDebugCapabilities extends MywpAbstractSettingModule {

  static protected $id = 'capabilities';

  static protected $priority = 110;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Capabilities' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $user_roles = MywpApi::get_all_user_roles();

    if( empty( $user_roles ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $user_roles ); ?></p>
    <table class="form-table">
      <thead>
        <tr>
          <th><?php _e( 'User Roles' ); ?></th>
          <td><?php _e( 'Capabilities' ); ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $user_roles as $user_role_name => $user_role ) : ?>

          <tr>
            <th>
              [<?php echo $user_role_name; ?>]<br />
              <?php echo $user_role['label']; ?>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 200px;"><?php print_r( $user_role['capabilities'] ); ?></textarea>
            </td>
          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugCapabilities::init();

endif;
