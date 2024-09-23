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

$current_setting_post_id = MywpSettingPost::get_current_post_id();

if( empty( $current_setting_post_id ) ) {

  return false;

}

$selected_post = MywpSettingPost::get_setting_post();

?>

<div id="setting-screen-input-post">

  <?php printf( __( 'Enter a post id' , 'my-wp' ) ); ?>

  <?php $post_url = add_query_arg( array( 'setting_post_id' => '###POST_ID###' ) , remove_query_arg( 'setting_post_id' ) ); ?>

  <input type="number" name="mywp[data][post]" id="setting-screen-input-post-id" value="<?php echo esc_attr( $current_setting_post_id ); ?>" data-post_url="<?php echo esc_url( $post_url ); ?>" />

  <button type="button" class="button button-primary" id="setting-screen-input-post-id-update"><?php _e( 'Update' ); ?></button>

  <span class="spinner"></span>

</div>
