<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugPostTypes' ) ) :

final class MywpSettingScreenDebugPostTypes extends MywpAbstractSettingModule {

  static protected $id = 'debug_post_types';

  static protected $priority = 10;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Post Types' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $all_post_types = MywpApi::get_all_post_types();

    if( empty( $all_post_types ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_post_types ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_post_types as $key => $post_type ) : ?>
          <tr>
            <th>
              [<?php echo $post_type->name; ?>] <?php echo $post_type->label; ?><br />
              <a href="<?php echo esc_url( add_query_arg( array( 'post_type' => $post_type->name ) , admin_url( 'edit.php' ) ) ); ?>"><?php echo $post_type->labels->all_items; ?></a>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $post_type ); ?></textarea>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugPostTypes::init();

endif;
