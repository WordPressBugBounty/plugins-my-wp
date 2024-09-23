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

$current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();

if( empty( $current_setting_taxonomy_name ) ) {

  return false;

}

$selectable_taxonomies = MywpSettingTaxonomy::get_setting_taxonomies();

?>

<div id="setting-screen-select-taxonomies">

  <?php printf( __( 'Select %s' ) , __( 'Taxonomy' , 'my-wp' ) ); ?>

  <select name="mywp[data][taxonomy]" id="setting-screen-select-taxonomy" disabled="disabled">

    <?php foreach( $selectable_taxonomies as $selectable_taxonomy ) : ?>

      <?php $taxonomy_url = add_query_arg( array( 'setting_taxonomy' => $selectable_taxonomy->name ) , remove_query_arg( 'setting_taxonomy' ) ); ?>

      <option value="<?php echo esc_attr( $selectable_taxonomy->name ); ?>" data-taxonomy_url="<?php echo esc_url( $taxonomy_url ); ?>" <?php selected( $current_setting_taxonomy_name , $selectable_taxonomy->name ); ?>><?php echo esc_attr( $selectable_taxonomy->labels->name ); ?></option>

    <?php endforeach; ?>

  </select>

  <span class="spinner"></span>

</div>
