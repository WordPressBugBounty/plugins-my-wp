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
      'use_advance' => true,
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
          <th><?php echo esc_html( sprintf( __ ( 'Show debugging %s' , 'my-wp' ) , __( 'Users' ) ) ); ?></th>
          <td>
            <input type="text" name="mywp[data][user_ids_text]" class="regular-text" value="<?php echo esc_attr( $user_id_text ); ?>" placeholder="1,2,3..." />
            <p class="mywp-description">
              <span class="dashicons dashicons-lightbulb"></span>
              <?php echo esc_html( __( 'Debugging for multiple users that enter the User ID with comma.' , 'my-wp' ) ); ?>
            </p>
            <?php if( ! empty( $setting_data['users'] ) ) : ?>
              <ul>
                <?php foreach( $setting_data['users'] as $user_id ) : ?>
                  <?php $user = get_userdata( $user_id ); ?>
                  <?php if( empty( $user ) ) : ?>
                    <li>[<?php echo $user_id; ?>] <strong style="color: red;"><?php echo esc_html( sprintf( __( '%s is not found.' ) ) , __( 'User' ) ); ?></strong></li>
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

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php echo esc_html( __( 'Heavy process debug items' , 'my-wp' ) ); ?></h3>

    <p><?php echo __( 'Debugging these items may require significant time and memory, depending on the plugins and themes you are using. Please proceed with caution.' , 'my-wp' ); ?></p>

    <table class="form-table">
      <tbody>
        <tr>
          <th><?php echo esc_html( __( 'My WP Cache' , 'my-wp' ) ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][mywp_cache]" class="mywp_cache" value="1" <?php checked( $setting_data['mywp_cache'] , true ); ?> />
              <?php echo esc_html( __( 'Activate' ) ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo esc_html( __( 'Request' , 'my-wp' ) ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][debug_request]" class="debug_request" value="1" <?php checked( $setting_data['debug_request'] , true ); ?> />
              <?php echo esc_html( __( 'Activate' ) ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo esc_html( __( 'Process times' , 'my-wp' ) ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][debug_time]" class="debug_time" value="1" <?php checked( $setting_data['debug_time'] , true ); ?> />
              <?php echo esc_html( __( 'Activate' ) ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo esc_html( __( 'Action hooks' , 'my-wp' ) ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][debug_action]" class="debug_action" value="1" <?php checked( $setting_data['debug_action'] , true ); ?> />
              <?php echo esc_html( __( 'Activate' ) ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo esc_html( __( 'debug_backtrace()' , 'my-wp' ) ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][debug_debugtrace]" class="debug_debugtrace" value="1" <?php checked( $setting_data['debug_debugtrace'] , true ); ?> />
              <?php echo esc_html( __( 'Activate' ) ); ?>
            </label>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
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

    if( ! empty( $formatted_data['mywp_cache'] ) ) {

      $formatted_data['mywp_cache'] = true;

    }

    if( ! empty( $formatted_data['debug_request'] ) ) {

      $formatted_data['debug_request'] = true;

    }

    if( ! empty( $formatted_data['debug_time'] ) ) {

      $formatted_data['debug_time'] = true;

    }

    if( ! empty( $formatted_data['debug_action'] ) ) {

      $formatted_data['debug_action'] = true;

    }

    if( ! empty( $formatted_data['debug_debugtrace'] ) ) {

      $formatted_data['debug_debugtrace'] = true;

    }

    return $formatted_data;

  }

}

MywpSettingScreenDebugGeneral::init();

endif;
