<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenSiteGeneral' ) ) :

final class MywpSettingScreenSiteGeneral extends MywpAbstractSettingModule {

  static protected $id = 'site_general';

  static protected $priority = 90;

  static private $menu = 'site';

  public static function mywp_setting_screens( $setting_screens ) {

    if( is_multisite() ) {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Site General' , 'my-wp' ),
        'menu' => 'network',
        'controller' => 'site_general',
      );

    } else {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Site General' , 'my-wp' ),
        'menu' => self::$menu,
        'controller' => 'site_general',
      );

    }

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Disable File Edit' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_file_edit]" class="disable_file_edit" value="1" <?php checked( $setting_data['disable_file_edit'] , true ); ?> />
              <?php _e( 'Disable' , 'my-wp' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide PHP X-Mailer version' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_php_mailer_version]" class="disable_php_mailer_version" value="1" <?php checked( $setting_data['disable_php_mailer_version'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p>
              <code><?php echo self::get_xmailer_header(); ?></code>
            </p>
          </td>
        </tr>
        <?php if( is_multisite() ) : ?>
          <tr>
            <th><?php _e( 'Disable User Admin' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][disable_user_admin]" class="disable_user_admin" value="1" <?php checked( $setting_data['disable_user_admin'] , true ); ?> />
                <?php _e( 'Disable' , 'my-wp' ); ?>
              </label>
              <a href="<?php echo esc_url( user_admin_url() ); ?>" target="_blank"><?php echo user_admin_url(); ?></a>
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

    if( ! empty( $formatted_data['disable_file_edit'] ) ) {

      $new_formatted_data['disable_file_edit'] = true;

    }

    if( ! empty( $formatted_data['disable_php_mailer_version'] ) ) {

      $new_formatted_data['disable_php_mailer_version'] = true;

    }

    if( ! empty( $formatted_data['disable_user_admin'] ) ) {

      $new_formatted_data['disable_user_admin'] = true;

    }

    return $new_formatted_data;

  }

  private static function get_xmailer_header() {

    $xmailer_header = 'X-Mailer: PHPMailer ';

    $phpmailer_version = self::get_phpmailer_version();

    if( ! empty( $phpmailer_version ) ) {

      $xmailer_header .= $phpmailer_version . ' ';

    }

    $xmailer_header .= '(https://github.com/PHPMailer/PHPMailer)';

    return $xmailer_header;

  }

  private static function get_phpmailer_version() {

    global $phpmailer;

    if( ! empty( $phpmailer ) && is_object( $phpmailer ) ) {

      $phpmailer_tmp = $phpmailer;

      if ( ( $phpmailer_tmp instanceof WPMailSMTP\MailCatcherV6 ) ) {

        $class_name = get_class( $phpmailer_tmp );

        if( defined( "$class_name::VERSION" ) ) {

          return $phpmailer_tmp::VERSION;

        } else {

          return false;

        }

      }

    } else {

      $is_new_ver = false;

      $php_mailer_file_path = ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';

      $php_mailer_file_path_old = ABSPATH . WPINC . '/class-phpmailer.php';

      if( file_exists( $php_mailer_file_path ) ) {

        $is_new_ver = true;

      }

      if( $is_new_ver ) {

        if ( ! ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) ) {

          require_once $php_mailer_file_path;

          $phpmailer_tmp = new PHPMailer\PHPMailer\PHPMailer( true );

        }

        if( ! empty( $phpmailer_tmp::VERSION ) ) {

          return $phpmailer_tmp::VERSION;

        }

      } else {

        if( file_exists( $php_mailer_file_path_old ) ) {

          require_once $php_mailer_file_path_old;

          $phpmailer_tmp = new PHPMailer( true );

        }

        if( ! empty( $phpmailer_tmp->Version ) ) {

          return $phpmailer_tmp->Version;

        }

      }

    }

    return false;

  }

}

MywpSettingScreenSiteGeneral::init();

endif;
