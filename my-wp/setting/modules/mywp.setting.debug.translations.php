<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugTranslations' ) ) :

final class MywpSettingScreenDebugTranslations extends MywpAbstractSettingModule {

  static protected $id = 'debug_translations';

  static protected $priority = 60;

  static private $menu = 'debug';

  protected static function after_init() {

    $id = 'network_' . self::$id;

    add_action( "mywp_setting_screen_content_{$id}" , array( __CLASS__ , 'mywp_current_setting_screen_content' ) );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Translations' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    if( is_multisite() ) {

      $setting_screens[ 'network_' . self::$id ] = array(
        'title' => __( 'All Translations' , 'my-wp' ),
        'menu' => 'network_' . self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  private static function get_translations() {

    global $l10n;

    $translations = array();

    if( ! empty( $l10n ) ) {

      $translations = $l10n;

    }

    return $translations;

  }

  public static function mywp_current_setting_screen_content() {

    $all_translations = self::get_translations();

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_translations ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_translations as $translation_domain => $translation_object ) : ?>
          <tr>
            <th>
              <?php echo $translation_domain; ?><br />
            </th>
            <td>
              <?php if( $translation_object instanceof MO ) : ?>

                <p><code><?php echo esc_html( $translation_object->get_filename() ); ?></code></p>

              <?php else : ?>

                <p><?php echo esc_html( __( 'Unknown translation file.' , 'my-wp' ) ); ?></p>

              <?php endif; ?>

              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $translation_object->entries ); ?></textarea>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugTranslations::init();

endif;
