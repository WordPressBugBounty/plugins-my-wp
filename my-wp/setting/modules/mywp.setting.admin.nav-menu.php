<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminNavMenu' ) ) :

final class MywpSettingScreenAdminNavMenu extends MywpAbstractSettingModule {

  static protected $id = 'admin_nav_menu';

  static protected $priority = 100;

  static private $menu = 'admin';

  protected static function after_init() {

    $screen_id = self::$id;

    add_action( "mywp_setting_screen_advance_content_{$screen_id}" , array( __CLASS__ , 'mywp_setting_screen_advance_content_20' ) , 20 );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Nav Menus' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'admin_nav_menu',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-nav-menus/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $meta_boxes = self::get_meta_boxes();

    if( empty( $meta_boxes ) ) {

      return false;

    }

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Management of meta boxes' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php foreach( $meta_boxes as $key => $meta_box ) : ?>
          <?php $metabox_id = $meta_box['id']; ?>
          <?php $checked = false; ?>
          <?php if( ! empty( $setting_data['remove_meta_boxes_items'][ $metabox_id ] ) ) : ?>
            <?php $checked = true; ?>
          <?php endif; ?>
          <tr>
            <th><?php echo $meta_box['title']; ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][remove_meta_boxes_items][<?php echo esc_attr( $metabox_id ); ?>]" class="remove_meta_boxes_item remove_meta_box_item-<?php echo esc_attr( $metabox_id ); ?>" value="1" <?php checked( $checked , true ); ?> />
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

    $fields = array(
      'hide_link_target' => __( 'Link Target' ),
      'hide_title_attribute' => __( 'Title Attribute' ),
      'hide_css_classes' => __( 'CSS Classes' ),
      'hide_xfn' => __( 'Link Relationship (XFN)' ),
      'hide_description' => __( 'Description' ),
    );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Advanced menu properties' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php foreach( $fields as $field_name => $field_label ) : ?>
          <tr>
            <th><?php echo $field_label; ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][<?php echo esc_attr( $field_name ); ?>]" class="<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( $setting_data[$field_name] , true ); ?> />
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

  public static function mywp_setting_screen_advance_content_20() {

    $setting_data = self::get_setting_data();

    $fields = array(
      'hide_add_new_menu' => __( 'Create a new menu' , 'my-wp' ),
      'hide_delete_menu' => __( 'Delete Menu' ),
      'hide_manage_locations' => __( 'Manage Locations' ),
      'hide_menu_settings' => __( 'Menu Settings' ),
      'hide_live_preview_button' => __( 'Manage with Live Preview' ),
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
                <input type="checkbox" name="mywp[data][<?php echo esc_attr( $field_name ); ?>]" class="<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( $setting_data[$field_name] , true ); ?> />
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

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['hide_add_new_menu'] ) ) {

      $new_formatted_data['hide_add_new_menu'] = true;

    }

    if( ! empty( $formatted_data['hide_delete_menu'] ) ) {

      $new_formatted_data['hide_delete_menu'] = true;

    }

    if( ! empty( $formatted_data['hide_manage_locations'] ) ) {

      $new_formatted_data['hide_manage_locations'] = true;

    }

    if( ! empty( $formatted_data['hide_menu_settings'] ) ) {

      $new_formatted_data['hide_menu_settings'] = true;

    }

    if( ! empty( $formatted_data['hide_live_preview_button'] ) ) {

      $new_formatted_data['hide_live_preview_button'] = true;

    }

    if( ! empty( $formatted_data['remove_meta_boxes_items'] ) ) {

      foreach( $formatted_data['remove_meta_boxes_items'] as $meta_box_id => $v ) {

        $meta_box_id = strip_tags( $meta_box_id );

        $new_formatted_data['remove_meta_boxes_items'][ $meta_box_id ] = true;

      }

    }

    if( ! empty( $formatted_data['hide_link_target'] ) ) {

      $new_formatted_data['hide_link_target'] = true;

    }

    if( ! empty( $formatted_data['hide_title_attribute'] ) ) {

      $new_formatted_data['hide_title_attribute'] = true;

    }

    if( ! empty( $formatted_data['hide_css_classes'] ) ) {

      $new_formatted_data['hide_css_classes'] = true;

    }

    if( ! empty( $formatted_data['hide_xfn'] ) ) {

      $new_formatted_data['hide_xfn'] = true;

    }

    if( ! empty( $formatted_data['hide_description'] ) ) {

      $new_formatted_data['hide_description'] = true;

    }

    return $new_formatted_data;

  }

  private static function get_meta_boxes() {

    $meta_boxes = array();

    $args = array( 'show_in_nav_menus' => true );

    $post_types = get_post_types( $args , 'objects' );

    if( ! empty( $post_types ) ) {

      foreach( $post_types as $post_type ) {

        if ( ! empty( $post_type ) ) {

          $id = sprintf( 'add-post-type-%s' , $post_type->name );

          $meta_boxes[] = array( 'id' => $id , 'title' => $post_type->labels->name );

        }

      }

    }

    $meta_boxes[] = array( 'id' => 'add-custom-links' , 'title' => __( 'Custom Links' ) );

    $args = array( 'show_in_nav_menus' => true );

    $taxonomies = get_taxonomies( $args , 'objects' );

    if( ! empty( $taxonomies ) ) {

      foreach( $taxonomies as $tax ) {

        if ( ! empty( $tax ) ) {

          $id = sprintf( 'add-%s' , $tax->name );

          $meta_boxes[] = array( 'id' => $id , 'title' => $tax->labels->name );

        }

      }

    }

    return apply_filters( 'mywp_setting_nav_menu_meta_boxes' , $meta_boxes );

  }

}

MywpSettingScreenAdminNavMenu::init();

endif;
