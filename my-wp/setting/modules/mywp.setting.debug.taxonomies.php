<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugTaxonomies' ) ) :

final class MywpSettingScreenDebugTaxonomies extends MywpAbstractSettingModule {

  static protected $id = 'debug_taxonomies';

  static protected $priority = 30;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Taxonomies' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $all_taxonomies = MywpApi::get_all_taxonomies();

    if( empty( $all_taxonomies ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_taxonomies ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_taxonomies as $key => $taxonomy ) : ?>
          <tr>
            <th>
              [<?php echo $taxonomy->name; ?>] <?php echo $taxonomy->label; ?><br />
              <a href="<?php echo esc_url( add_query_arg( array( 'taxonomy' => $taxonomy->name ) , admin_url( 'edit-tags.php' ) ) ); ?>"><?php echo $taxonomy->labels->all_items; ?></a>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $taxonomy ); ?></textarea>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugTaxonomies::init();

endif;
