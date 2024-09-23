<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugGeneral' ) ) :

final class MywpSettingScreenDebugGeneral extends MywpAbstractSettingModule {

  static protected $id = 'debug_general';

  static protected $priority = 1;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Debug' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'debug_general',
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $user_id_text = false;

    if( ! empty( $setting_data['users'] ) && is_array( $setting_data['users'] ) ) {

      $user_id_text = implode( ',' , $setting_data['users'] );

    }

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php printf( __ ( 'Show debugging %s' , 'my-wp' ) , __( 'Users' ) ); ?></th>
          <td>
            <input type="text" name="mywp[data][user_ids_text]" class="regular-text" value="<?php echo esc_attr( $user_id_text ); ?>" placeholder="1,2,3..." />
            <p class="mywp-description">
              <span class="dashicons dashicons-lightbulb"></span>
              <?php _e( 'Debugging for multiple users that enter the User ID with comma.' , 'my-wp' ); ?>
            </p>
            <?php if( ! empty( $setting_data['users'] ) ) : ?>
              <ul>
                <?php foreach( $setting_data['users'] as $user_id ) : ?>
                  <?php $user = get_userdata( $user_id ); ?>
                  <?php if( empty( $user ) ) : ?>
                    <li>[<?php echo $user_id; ?>] <strong style="color: red;"><?php printf( __( '%s is not found.' ) , __ ( 'User' ) ); ?></strong></li>
                  <?php else : ?>
                    <li>[<?php echo $user_id; ?>] <?php echo $user->display_name; ?> <span class="description">( <?php echo $user->user_login; ?> )</span></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $formatted_data['users'] = array();

    if( ! empty( $formatted_data['user_ids_text'] ) ) {

      if( strpos( $formatted_data['user_ids_text'] , ',' ) === false ) {

        $users[] = (int) $formatted_data['user_ids_text'];

      } else {

        $users = array_map( 'intval' , explode( ',' , $formatted_data['user_ids_text'] ) );

        foreach( $users as $key => $user_id ) {

          if( empty( $user_id ) ) {

            unset( $users[ $key ] );

          }

        }

        $users = array_unique( $users );

        asort( $users );

      }

      $formatted_data['users'] = $users;

      unset( $formatted_data['user_ids_text'] );

    }

    return $formatted_data;

  }

}

MywpSettingScreenDebugGeneral::init();

endif;
