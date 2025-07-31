<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenUpdateBulkPostMeta' ) ) :

final class MywpSettingScreenUpdateBulkPostMeta extends MywpAbstractSettingBulkModule {

  static protected $id = 'bulk_update_post_meta';

  static protected $priority = 10;

  protected static function get_setting_screen_title() {

    return __( 'Bulk update of Posts custom fields' , 'my-wp' );

  }

  protected static function get_search_items( $filter_fields ) {

    if( empty( $filter_fields['select_post_type'] ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post Type.' , 'my-wp' ) ) );

    }

    if( empty( $filter_fields['select_post_status'] ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post Status.' , 'my-wp' ) ) );

    }

    $post_args = array(
      'numberposts' => -1,
      'orderby' => array(
        'menu_order' => 'ASC',
        'ID' => 'DESC',
      ),
      'post_type' => $filter_fields['select_post_type'],
      'post_status' => $filter_fields['select_post_status'],
    );

    $posts = get_posts( $post_args );

    if( empty( $posts ) ) {

      $posts = array();

    }

    ob_start();

    self::print_found_items_posts( $posts , $filter_fields );

    $found_items_html = ob_get_contents();

    ob_end_clean();

    $search_items = array(
      'items' => $found_items_html,
      'count' => sprintf( _n( '%s Post' , '%s Posts' , count( $posts ) ) , count( $posts ) ),
    );

    return $search_items;

  }

  protected static function get_prepare_bulk_items( $do_run , $post_ids , $filter_fields , $bulk_fields ) {

    if( empty( $bulk_fields['meta_key'] ) ) {

      wp_send_json_error( array( 'error' => __( 'Please input meta_key.' , 'my-wp' ) ) );

    }

    ob_start();

    self::print_prepare_bulk_items( $do_run , $post_ids , $filter_fields , $bulk_fields );

    $prepare_bulk_items_html = ob_get_contents();

    ob_end_clean();

    $prepare_bulk_items = array(
      'bulk_items' => $prepare_bulk_items_html,
      'count' => sprintf( _n( '%s Post' , '%s Posts' , count( $post_ids ) ) , count( $post_ids ) ),
    );

    return $prepare_bulk_items;

  }

  protected static function do_bulk_item( $post_id , $filter_fields , $bulk_fields ) {

    $results = array(
      'is_success' => false,
      'details' => '',
    );

    if( empty( $bulk_fields['meta_key'] ) ) {

      $results['details'] = 'Empty meta key';

      return $results;

    }

    $from_meta_value = get_post_meta( $post_id , $bulk_fields['meta_key'] , true );

    $update_post_meta = update_post_meta( $post_id , $bulk_fields['meta_key'] , $bulk_fields['meta_value'] , $from_meta_value );

    if( ! empty( $update_post_meta ) ) {

      if( is_wp_error( $update_post_meta ) ) {

        $results['details'] = sprintf( '[%s] %s.' , $update_post_meta->get_error_code() , $update_post_meta->get_error_message() );

      } else {

        $results['is_success'] = true;

      }

    }

    return $results;

  }

  protected static function get_mywp_current_setting_screen_header_filter_item_title() {

    return __( 'Filter and search for posts to update' , 'my-wp' );

  }

  protected static function print_setting_screen_header_filter_item_fields() {

    self::print_setting_screen_header_filter_item_field_post_type();

    self::print_setting_screen_header_filter_item_field_post_status();

  }

  protected static function get_mywp_current_setting_screen_content_filtered_item_title() {

    return __( 'Select posts to update' , 'my-wp' );

  }

  protected static function get_mywp_current_setting_screen_content_filtered_items_columns() {

    self::get_mywp_current_setting_screen_content_filtered_items_columns_posts();

  }

  protected static function get_mywp_current_setting_screen_content_bulk_form_title() {

    return __( 'Confirm update details' , 'my-wp' );

  }

  protected static function print_mywp_current_setting_screen_content_bulk_form() {

    ?>

    <h3 class="mywp-setting-screen-subtitle">
      3: <?php echo esc_html( self::get_mywp_current_setting_screen_content_bulk_form_title() ); ?>
    </h3>

    <div id="bulk-form" class="bulk-contents">

      <div class="disabled-content">

        <p><span class="dashicons dashicons-arrow-up-alt"></span></p>

        <p><?php _e( 'Please select the posts.' , 'my-wp' ); ?></p>

      </div>

      <div class="active-content">

        <table class="form-table">
          <tbody>
            <tr class="selected-items">
              <th><?php _e( 'Selected Post IDs' , 'my-wp' ); ?></th>
              <td>&nbsp;</td>
            </tr>
            <tr class="other-fields">
              <th><?php _e( 'Meta key' , 'my-wp' ); ?></th>
              <td>
                <input type="text" class="large-text bulk-filter" data-bulk_filter="meta_key" placeholder="<?php echo esc_attr( '_wp_page_template' ); ?>" />
              </td>
            </tr>
            <tr class="other-fields">
              <th><?php _e( 'Meta value' , 'my-wp' ); ?></th>
              <td>
                <textarea class="large-text bulk-filter" data-bulk_filter="meta_value" placeholder="<?php echo esc_attr( 'default' ); ?>"></textarea>
              </td>
            </tr>
          </tbody>
        </table>

        <div style="text-align: center;">

          <p class="mywp-description">
            <span class="dashicons dashicons-lightbulb"></span> <?php _e( 'It is recommend that you backup your database and do a dry run.' , 'my-wp' ); ?>
          </p>

          <p>
            <button type="button" class="button button-large button-primary do-bulk" data-do_run="0" data-confirm_run="0">
              <?php _e( 'Bulk update (Dry run)' , 'my-wp' ); ?>
            </button>
          </p>

          <p>
            <button type="button" class="button button-caution button-primary do-bulk" data-do_run="1" data-confirm_run="1">
              <?php _e( 'Bulk update' , 'my-wp' ); ?>
            </button>
          </p>

          <p>
            <span class="spinner"></span>
          </p>

        </div>

      </div>

    </div>

    <?php

  }

  protected static function print_mywp_current_setting_screen_content_bulk_result_contents() {

    ?>

    <div id="bulk-results" class="bulk-contents">

      <div class="disabled-content">

        <p><span class="dashicons dashicons-arrow-up-alt"></span></p>
        <p><?php _e( 'Please bulk update.' , 'my-wp' ); ?></p>

      </div>

      <div class="active-content">

        <table class="wp-list-table widefat fixed striped table-view-list posts">
          <thead>
            <th class="id"><?php _e( 'ID' , 'my-wp' ); ?></th>
            <th class="is-process"><?php _e( 'Bulk process' , 'my-wp' ); ?></th>
            <th class="from-meta"><?php _e( 'From meta value' , 'my-wp' ); ?></th>
            <th class="update-arrow">&nbsp;</th>
            <th class="to-meta"><?php _e( 'To meta value' , 'my-wp' ); ?></th>
            <th class="details"><?php _e( 'Details' , 'my-wp' ); ?></th>
          </thead>
          <tbody>
          </tbody>
        </table>

      </div>

    </div>

    <?php

  }

  private static function print_prepare_bulk_items( $do_run , $post_ids , $filter_fields , $bulk_fields ) {

    $bulk_meta_value_unserialize = maybe_unserialize( stripslashes_deep( $bulk_fields['meta_value'] ) );

    ?>

    <?php foreach( $post_ids as $post_id ) : ?>

      <?php $post = get_post( $post_id ); ?>

      <?php $from_meta_value = get_post_meta( $post_id , $bulk_fields['meta_key'] , true ); ?>

      <tr class="result-item item item-<?php echo esc_attr( $post_id ); ?> wait">
        <th class="id">
          <?php echo $post_id; ?>
          <input type="hidden" class="item-id" value="<?php echo esc_attr( $post_id ); ?>" />
        </th>
        <td class="is-process">
          <div class="processing">
            <span class="dashicons dashicons-update"></span>
          </div>
          <div class="success">
            <span class="dashicons dashicons-yes"></span>
          </div>
          <div class="warning">
            <span class="dashicons dashicons-warning"></span>
          </div>
        </td>
        <td class="from-meta">
          <?php if( is_array( $from_meta_value ) or is_object( $from_meta_value ) ) : ?>
            <textarea class="large-text" readonly="readonly"><?php echo esc_textarea( print_r( $from_meta_value , true ) ); ?></textarea>
          <?php else : ?>
            <code><?php echo esc_html( $from_meta_value ); ?></code>
          <?php endif; ?>
        </td>
        <td class="update-arrow">
          <span class="dashicons dashicons-arrow-right-alt2"></span>
        </td>
        <td class="to-meta">
          <?php if( is_array( $bulk_meta_value_unserialize ) or is_object( $bulk_meta_value_unserialize ) ) : ?>
            <textarea class="large-text" readonly="readonly"><?php echo esc_textarea( print_r( $bulk_meta_value_unserialize , true ) ); ?></textarea>
          <?php else : ?>
            <code><?php echo esc_html( $bulk_fields['meta_value'] ); ?></code>
          <?php endif; ?>
        </td>
        <td class="details"></td>
      </tr>

    <?php endforeach; ?>

    <?php

  }


}

MywpSettingScreenUpdateBulkPostMeta::init();

endif;
