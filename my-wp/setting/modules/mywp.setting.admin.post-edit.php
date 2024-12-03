<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminPostEdit' ) ) :

final class MywpSettingScreenAdminPostEdit extends MywpAbstractSettingModule {

  static protected $id = 'admin_post_edit';

  static protected $priority = 60;

  static private $menu = 'admin';

  static private $post_type = '';

  static private $is_use_block_editor = false;

  protected static function after_init() {

    $screen_id = self::$id;

    add_action( "mywp_setting_screen_advance_content_{$screen_id}" , array( __CLASS__ , 'mywp_setting_screen_advance_content_20' ) , 20 );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => sprintf( '%s/%s' , __( 'Edit Post' ) , _x( 'Add New' , 'post' ) ),
      'menu' => self::$menu,
      'controller' => 'admin_post_edit',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-edit-post-add-new/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_load_setting_screen() {

    $current_setting_post_type_name = MywpSettingPostType::get_current_post_type_id();

    if( ! empty( $current_setting_post_type_name ) ) {

      self::$post_type = $current_setting_post_type_name;

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$post_type ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$post_type;

    return $option_key;

  }

  public static function mywp_current_setting_screen_header() {

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-select-post-type.php' );

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();
    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( empty( $current_setting_post_type ) ) {

      printf( __( '%1$s: %2$s is not found.' , 'my-wp' ) , __( 'Invalid Post Type' , 'my-wp' ) , $current_setting_post_type_id );

      return false;

    }

    $block_editor_panels_setting_data = array();

    add_filter( 'use_block_editor_for_post_type' , array( 'MywpControllerModuleAdminPostEdit' , 'change_editor' ) );

    if( function_exists( 'use_block_editor_for_post_type' ) ) {

      self::$is_use_block_editor = use_block_editor_for_post_type( $current_setting_post_type_id );

      if( ! empty( $setting_data['use_classic_editor'] ) ) {

        self::$is_use_block_editor = false;

      }

    } else {

      self::$is_use_block_editor = false;

    }

    MywpSettingBlockEditor::set_is_use_block_editor( self::$is_use_block_editor );

    if( self::$is_use_block_editor ) {

      if( ! empty( $setting_data['block_editor_panels'] ) ) {

        $block_editor_panels_setting_data = $setting_data['block_editor_panels'];

      }

      MywpSettingBlockEditor::set_current_block_editor_screen_id( $current_setting_post_type_id );
      MywpSettingBlockEditor::set_current_block_editor_panels_setting_data( $block_editor_panels_setting_data );

    }

    $meta_boxes_setting_data = array();

    if( ! empty( $setting_data['meta_boxes'] ) ) {

      $meta_boxes_setting_data = $setting_data['meta_boxes'];

    }

    $one_post_link = MywpSettingPostType::get_one_post_link_edit( $current_setting_post_type_id );

    MywpSettingMetaBox::set_current_meta_box_screen_id( $current_setting_post_type_id );
    MywpSettingMetaBox::set_current_meta_box_screen_url( $one_post_link );
    MywpSettingMetaBox::set_current_meta_box_setting_data( $meta_boxes_setting_data );

    ?>

    <?php if( function_exists( 'use_block_editor_for_post_type' ) ) : ?>

      <h3 class="mywp-setting-screen-subtitle"><?php _e( 'For Block/Classic Editor' , 'my-wp' ); ?></h3>
      <table class="form-table">
        <tbody>
          <tr>
            <th><?php _e( 'Change the Editor' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][use_classic_editor]" class="use_classic_editor" value="1" <?php checked( $setting_data['use_classic_editor'] , true ); ?> />
                <?php _e( 'Use Classic Editor' , 'my-wp' ); ?>
              </label>
            </td>
          </tr>
        </tbody>
      </table>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

    <?php endif; ?>

    <?php if( self::$is_use_block_editor ) : ?>

      <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Management of Document panels (block editor)' , 'my-wp' ); ?></h3>

      <?php MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-management-block-editor-panels.php' ); ?>

      <p>&nbsp;</p>

    <?php endif; ?>

    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Management of meta boxes' , 'my-wp' ); ?></h3>

    <?php MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-management-meta-boxes.php' ); ?>

    <p>&nbsp;</p>

    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();
    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( empty( $current_setting_post_type ) ) {

      return false;

    }

    $update_messages_default = MywpControllerModuleAdminPostEdit::get_update_messages_default();

    ?>

    <?php if( ! self::$is_use_block_editor ) : ?>

      <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Updated Messages' , 'my-wp' ); ?></h3>
      <table class="form-table">
        <tbody>
          <?php foreach( $update_messages_default as $update_message_key => $update_message ) : ?>
            <?php $val = ''; ?>
            <?php if( ! empty( $setting_data['post_updated_messages'][ $update_message_key ] ) ) : ?>
              <?php $val = $setting_data['post_updated_messages'][ $update_message_key ]; ?>
            <?php endif; ?>
            <tr>
              <th><?php echo $update_message['title']; ?></th>
              <td>
                <label>
                  <input type="text" name="mywp[data][post_updated_messages][<?php echo esc_attr( $update_message_key ); ?>]" class="<?php echo esc_attr( $update_message_key ); ?> large-text" value="<?php echo esc_attr( $val ); ?>" placeholder="<?php echo esc_attr( $update_message['message'] ); ?>" />
                </label>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <p>&nbsp;<p>

    <?php endif; ?>

    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'General' ); ?></h3>

    <table class="form-table">
      <tbody>
        <?php if( self::$is_use_block_editor ) : ?>

          <tr>
            <th><?php _e( 'Top left button' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][block_editor_top_left_icon]" class="block_editor_top_left_icon" value="1" <?php checked( $setting_data['block_editor_top_left_icon'] , true ); ?> />
                <?php _e( 'Change to back icon' , 'my-wp' ); ?>
              </label>
            </td>
          </tr>

        <?php endif; ?>

        <?php if( ! self::$is_use_block_editor ) : ?>

          <tr>
            <th>
              <?php if( ! empty( $current_setting_post_type->labels->add_new ) ) : ?>

                <?php echo esc_html( $current_setting_post_type->labels->add_new ); ?>

              <?php else : ?>

                <?php echo _x( 'Add New' , 'post' ); ?>

              <?php endif; ?>
            </th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_add_new]" class="hide_add_new" value="1" <?php checked( $setting_data['hide_add_new'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php _e( 'Title' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_title]" class="hide_title" value="1" <?php checked( $setting_data['hide_title'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>

        <?php endif; ?>

        <tr>
          <th><?php _e( 'Change title placeholder' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="text" name="mywp[data][change_title_placeholder]" class="change_title_placeholder large-text" value="<?php echo esc_attr( $setting_data['change_title_placeholder'] ); ?>" placeholder="<?php echo esc_attr( __( 'Add title' ) ); ?>" />
            </label>
          </td>
        </tr>

        <?php if( ! self::$is_use_block_editor ) : ?>

          <tr>
            <th><?php _e( 'Change Post title to Post ID' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][auto_change_title]" class="auto_change_title" value="1" <?php checked( $setting_data['auto_change_title'] , true ); ?> />
                <?php _e( 'Auto Change' , 'my-wp' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php _e( 'Permalinks' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_permalink]" class="hide_permalink" value="1" <?php checked( $setting_data['hide_permalink'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php _e( 'Change Permalink Structure' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_change_permalink]" class="hide_change_permalink" value="1" <?php checked( $setting_data['hide_change_permalink'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php _e( 'Content' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_content]" class="hide_content" value="1" <?php checked( $setting_data['hide_content'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>

        <?php endif; ?>

        <tr>
          <th><?php _e( 'Re-arrange meta boxes' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][prevent_meta_box]" class="prevent_meta_box" value="1" <?php checked( $setting_data['prevent_meta_box'] , true ); ?> />
              <?php _e( 'Prevent' , 'my-wp' ); ?>
            </label>
          </td>
        </tr>

        <?php if( ! self::$is_use_block_editor ) : ?>

          <tr>
            <th><?php _e( 'Forced Editor' , 'my-wp' ); ?></th>
            <td>
              <select name="mywp[data][forced_editor]" class="forced_editor">
                <option value=""></option>
                <option value="tinymce" <?php selected( 'tinymce' , $setting_data['forced_editor'] ); ?>><?php echo esc_attr( sprintf( __( 'Only use %s' , 'my-wp' ) , _x( 'Visual' , 'Name for the Visual editor tab' ) ) ); ?></option>
                <option value="html" <?php selected( 'html' , $setting_data['forced_editor'] ); ?>><?php echo esc_attr( sprintf( __( 'Only use %s' , 'my-wp' ) , _x( 'Text' , 'Name for the Text editor tab (formerly HTML)' ) ) ); ?></option>
              </select>
            </td>
          </tr>

        <?php endif; ?>
      </tbody>
    </table>

    <p>&nbsp;</p>

    <?php

  }

  public static function mywp_setting_screen_advance_content_20() {

    $setting_data = self::get_setting_data();

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();
    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( empty( $current_setting_post_type ) ) {

      return false;

    }

    ?>

    <?php if( ! self::$is_use_block_editor ) : ?>

      <h3 class="mywp-setting-screen-subtitle"><?php echo _e( 'Publish metabox' , 'my-wp' ); ?></h3>
      <table class="form-table">
        <tbody>
          <tr>
            <th><?php echo _e( 'Save Draft' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_draft]" class="hide_publish_metabox_draft" value="1" <?php checked( $setting_data['hide_publish_metabox_draft'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php echo _e( 'Preview Changes' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_preview]" class="hide_publish_metabox_preview" value="1" <?php checked( $setting_data['hide_publish_metabox_preview'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php echo _e( 'Changes Post Status' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_change_post_status]" class="hide_publish_metabox_change_post_status" value="1" <?php checked( $setting_data['hide_publish_metabox_change_post_status'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php echo _e( 'Changes Publish Status' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_change_publish_status]" class="hide_publish_metabox_change_publish_status" value="1" <?php checked( $setting_data['hide_publish_metabox_change_publish_status'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php echo _e( 'Changes Publish On' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_change_publish_on]" class="hide_publish_metabox_change_publish_on" value="1" <?php checked( $setting_data['hide_publish_metabox_change_publish_on'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
          <tr>
            <th><?php echo _e( 'Revisions' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][hide_publish_metabox_revisions]" class="hide_publish_metabox_revisions" value="1" <?php checked( $setting_data['hide_publish_metabox_revisions'] , true ); ?> />
                <?php _e( 'Hide' ); ?>
              </label>
            </td>
          </tr>
        </tbody>
      </table>

      <p>&nbsp;</p>

    <?php endif; ?>

    <?php

  }

  public static function mywp_current_setting_screen_remove_form() {

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();

    if( empty( $current_setting_post_type_id ) ) {

      return false;

    }

    ?>

    <input type="hidden" name="mywp[data][post_type]" value="<?php echo esc_attr( $current_setting_post_type_id ); ?>" />

    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['post_type'] ) ) {

      $new_formatted_data['post_type'] = strip_tags( $formatted_data['post_type'] );

    }

    if( ! empty( $formatted_data['meta_boxes'] ) ) {

      foreach( $formatted_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

        $meta_box_id = strip_tags( $meta_box_id );

        $new_meta_box_setting = array( 'action' => '' , 'title' => '' );

        $new_meta_box_setting['action'] = strip_tags( $meta_box_setting['action'] );

        if( ! empty( $meta_box_setting['title'] ) ) {

          $new_meta_box_setting['title'] = wp_unslash( wp_kses_post( $meta_box_setting['title'] ) );

        }

        $new_formatted_data['meta_boxes'][ $meta_box_id ] = $new_meta_box_setting;

      }

    }

    if( ! empty( $formatted_data['block_editor_panels'] ) ) {

      foreach( $formatted_data['block_editor_panels'] as $meta_box_id => $meta_box_setting ) {

        $meta_box_id = strip_tags( $meta_box_id );

        $new_meta_box_setting = array( 'action' => '' );

        $new_meta_box_setting['action'] = strip_tags( $meta_box_setting['action'] );

        $new_formatted_data['block_editor_panels'][ $meta_box_id ] = $new_meta_box_setting;

      }

    }

    $update_messages_default = MywpControllerModuleAdminPostEdit::get_update_messages_default();

    foreach( $update_messages_default as $key => $v ) {

      if( ! empty( $formatted_data['post_updated_messages'][ $key ] ) ) {

        $new_formatted_data['post_updated_messages'][ $key ] = strip_tags( $formatted_data['post_updated_messages'][ $key ] );

      }

    }

    if( ! empty( $formatted_data['use_classic_editor'] ) ) {

      $new_formatted_data['use_classic_editor'] = true;

    }

    if( ! empty( $formatted_data['block_editor_top_left_icon'] ) ) {

      $new_formatted_data['block_editor_top_left_icon'] = true;

    }

    if( ! empty( $formatted_data['hide_add_new'] ) ) {

      $new_formatted_data['hide_add_new'] = true;

    }

    if( ! empty( $formatted_data['hide_title'] ) ) {

      $new_formatted_data['hide_title'] = true;

    }

    if( ! empty( $formatted_data['change_title_placeholder'] ) ) {

      $new_formatted_data['change_title_placeholder'] = strip_tags( $formatted_data['change_title_placeholder'] );

    }

    if( ! empty( $formatted_data['auto_change_title'] ) ) {

      $new_formatted_data['auto_change_title'] = true;

    }

    if( ! empty( $formatted_data['hide_permalink'] ) ) {

      $new_formatted_data['hide_permalink'] = true;

    }

    if( ! empty( $formatted_data['hide_change_permalink'] ) ) {

      $new_formatted_data['hide_change_permalink'] = true;

    }

    if( ! empty( $formatted_data['hide_content'] ) ) {

      $new_formatted_data['hide_content'] = true;

    }

    if( ! empty( $formatted_data['prevent_meta_box'] ) ) {

      $new_formatted_data['prevent_meta_box'] = true;

    }

    if( ! empty( $formatted_data['forced_editor'] ) ) {

      $new_formatted_data['forced_editor'] = strip_tags( $formatted_data['forced_editor'] );

    }

    if( ! empty( $formatted_data['hide_publish_metabox_draft'] ) ) {

      $new_formatted_data['hide_publish_metabox_draft'] = true;

    }

    if( ! empty( $formatted_data['hide_publish_metabox_preview'] ) ) {

      $new_formatted_data['hide_publish_metabox_preview'] = true;

    }

    if( ! empty( $formatted_data['hide_publish_metabox_change_post_status'] ) ) {

      $new_formatted_data['hide_publish_metabox_change_post_status'] = true;

    }

    if( ! empty( $formatted_data['hide_publish_metabox_change_publish_status'] ) ) {

      $new_formatted_data['hide_publish_metabox_change_publish_status'] = true;

    }

    if( ! empty( $formatted_data['hide_publish_metabox_change_publish_on'] ) ) {

      $new_formatted_data['hide_publish_metabox_change_publish_on'] = true;

    }

    if( ! empty( $formatted_data['hide_publish_metabox_revisions'] ) ) {

      $new_formatted_data['hide_publish_metabox_revisions'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_post_data_format_remove( $formatted_data ) {

    if( ! empty( $formatted_data['post_type'] ) ) {

      $formatted_data['post_type'] = strip_tags( $formatted_data['post_type'] );

    }

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_validate_update( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['post_type'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'post_type' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_post_data_validate_remove( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['post_type'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'post_type' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_before_post_data_action_update( $validated_data ) {

    if( ! empty( $validated_data['post_type'] ) ) {

      self::$post_type = $validated_data['post_type'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    if( ! empty( $validated_data['post_type'] ) ) {

      self::$post_type = $validated_data['post_type'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

      MywpSettingMetaBox::set_current_meta_box_screen_id( self::$post_type );

      MywpSettingMetaBox::delete_current_meta_boxes();

    }

  }

}

MywpSettingScreenAdminPostEdit::init();

endif;
