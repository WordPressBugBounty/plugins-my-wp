<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugPostStructure' ) ) :

final class MywpSettingScreenDebugPostStructure extends MywpAbstractSettingModule {

  static protected $id = 'debug_post_structure';

  static protected $priority = 130;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Post Structure' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_header() {

    printf( '<p>%s</p>' , __( 'The structure in the post.' , 'my-wp' ) );

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-input-post.php' );

  }

  public static function mywp_current_setting_screen_content() {

    $current_setting_post_id = MywpSettingPost::get_current_post_id();
    $current_setting_post = MywpSettingPost::get_current_post();

    if( empty( $current_setting_post ) ) {

      printf( __( '%1$s: %3$s %2$s is not found.' , 'my-wp' ) , __( 'Invalid Post' , 'my-wp' ) , $current_setting_post_id , __( 'Post' ) );

      return false;

    }

    $post_custom = get_post_custom( $current_setting_post_id );

    $post_structure = array(
      'post_id' => $current_setting_post_id,
      'post' => $current_setting_post,
      'custom_fields' => array(),
    );

    if( ! empty( $post_custom ) ) {

      foreach( $post_custom as $custom_field_key => $custom_field_value ) {

        if( isset( $post_structure[ $custom_field_key ] ) ) {

          continue;

        }

        $post_structure['custom_fields'][ $custom_field_key ] = $custom_field_value;

      }

    }

    ksort( $post_structure['custom_fields'] );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Post' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php foreach( $post_structure['post'] as $field_name => $field_value ) : ?>
          <tr>
            <th><?php echo $field_name; ?></th>
            <td><textarea readonly class="large-text" style="height: 80px;"><?php echo $field_value; ?></textarea></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Custom Fields' ); ?></h3>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $post_structure['custom_fields'] ); ?></p>
    <table class="form-table">
      <tbody>
        <?php if( ! empty( $post_structure['custom_fields'] ) ) : ?>

          <?php foreach( $post_structure['custom_fields'] as $custom_field_key => $structure ) : ?>
            <tr>
              <th><?php echo $custom_field_key; ?></th>
              <td>
                <?php foreach( $structure as $custom_field_value ) : ?>
                  <?php $custom_field_value_unserializez = maybe_unserialize( $custom_field_value ); ?>
                  <?php $custom_field_value_json = json_decode( $custom_field_value ); ?>
                  <?php if( is_array( $custom_field_value_unserializez ) or is_object( $custom_field_value_unserializez ) ) : ?>
                    <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $custom_field_value_unserializez ); ?></textarea>
                  <?php elseif( ! empty( $custom_field_value_json ) && is_object( $custom_field_value_json ) ) : ?>
                    <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php print_r( $custom_field_value_json ); ?></textarea>
                  <?php else : ?>
                    <textarea readonly="readonly" class="large-text" style="height: 100px;"><?php echo $custom_field_value_unserializez; ?></textarea>
                  <?php endif; ?>
                <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>

        <?php endif; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugPostStructure::init();

endif;
