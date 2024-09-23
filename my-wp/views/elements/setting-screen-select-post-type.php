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

$current_setting_post_type_name = MywpSettingPostType::get_current_post_type_id();

if( empty( $current_setting_post_type_name ) ) {

  return false;

}

$selectable_post_types = MywpSettingPostType::get_setting_post_types();

?>

<div id="setting-screen-select-post-types">

  <?php printf( __( 'Select %s' ) , __( 'Post Type' , 'my-wp' ) ); ?>

  <select name="mywp[data][post_type]" id="setting-screen-select-post-type" disabled="disabled">

    <?php foreach( $selectable_post_types as $selectable_post_type ) : ?>

      <?php $post_type_url = add_query_arg( array( 'setting_post_type' => $selectable_post_type->name ) , remove_query_arg( 'select_post_type' ) ); ?>

      <option value="<?php echo esc_attr( $selectable_post_type->name ); ?>" data-post_type_url="<?php echo esc_url( $post_type_url ); ?>" <?php selected( $current_setting_post_type_name , $selectable_post_type->name ); ?>><?php echo esc_attr( $selectable_post_type->labels->name ); ?></option>

    <?php endforeach; ?>

  </select>

  <span class="spinner"></span>

</div>
