<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpSetting' ) ) {
  return false;
}

if( ! MywpApi::is_manager() ) {
  return false;
}

$current_block_editor_screen_id = MywpSettingBlockEditor::get_current_block_editor_screen_id();

if( empty( $current_block_editor_screen_id ) ) {

  return false;

}

$current_block_editor_panels = MywpSettingBlockEditor::get_current_block_editor_panels();

$current_block_editor_panels_setting_data = MywpSettingBlockEditor::get_current_block_editor_panels_setting_data();

?>

<div id="setting-screen-management-block-editor-panels">

  <ul id="block-editor-panel-bulk-actions">
    <li><a href="javascript:void(0);" class="button button-secondary" id="block-editor-panel-bulk-action-show">
      <?php _e( 'All Show' , 'my-wp' ); ?>
    </a></li>
    <li><a href="javascript:void(0);" class="button button-secondary" id="block-editor-panel-bulk-action-hide">
      <?php _e( 'All Hide' , 'my-wp' ); ?>
    </a></li>
  </ul>

  <table class="form-table" id="block-editor-panels-table">
    <thead>
      <tr>
        <th></th>
        <th><?php _e( 'Hide' ); ?></th>
      </tr>
    </thead>
    <tbody>

      <?php foreach( $current_block_editor_panels as $block_editor_panel_id => $block_editor_panel ) : ?>

        <?php $action = false; ?>

        <?php if( ! empty( $current_block_editor_panels_setting_data[ $block_editor_panel_id ]['action'] ) ) : ?>

          <?php $action = $current_block_editor_panels_setting_data[ $block_editor_panel_id ]['action']; ?>

        <?php endif; ?>

        <tr class="block-editor-panel-tr">
          <th><?php echo $block_editor_panel['title']; ?></th>
          <td>
            <select name="mywp[data][block_editor_panels][<?php echo esc_attr( $block_editor_panel_id ); ?>][action]" class="block-editor-panel-action-select">
              <option value="" <?php selected( $action , '' ); ?>></option>
              <option value="hide" <?php selected( $action , 'hide' ); ?>><?php _e( 'Hide' ); ?></option>
            </select>
          </td>
        </tr>

      <?php endforeach; ?>

    </tbody>
  </table>

</div>
