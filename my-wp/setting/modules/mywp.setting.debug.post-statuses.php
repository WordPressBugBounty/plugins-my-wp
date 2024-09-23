<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugPostStatuses' ) ) :

final class MywpSettingScreenDebugPostStatuses extends MywpAbstractSettingModule {

  static protected $id = 'debug_post_statuces';

  static protected $priority = 20;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Post Statuses' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $all_post_statuses = MywpApi::get_all_post_statuses();

    if( empty( $all_post_statuses ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_post_statuses ); ?></p>
    <table class="form-table">
      <tbody>

        <?php foreach( $all_post_statuses as $key => $post_status ) : ?>

          <tr>
            <th>
              [<?php echo $post_status->name; ?>] <?php echo $post_status->label; ?><br />
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $post_status ); ?></textarea>
            </td>
          </tr>

        <?php endforeach; ?>

      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugPostStatuses::init();

endif;
