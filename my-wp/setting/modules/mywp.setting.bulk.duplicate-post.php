<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenBulkDuplicatePostFromSite' ) ) :

final class MywpSettingScreenBulkDuplicatePostFromSite extends MywpAbstractSettingBulkModule {

  static protected $id = 'bulk_duplicate_post';

  static protected $priority = 20;

  protected static function get_setting_screen_title() {

    return __( 'Bulk duplicate posts from site in Network' , 'my-wp' );

  }

  protected static function get_search_items( $filter_fields ) {

    if( empty( $filter_fields['select_site_id'] ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select Site.' , 'my-wp' ) ) );

    }

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

    $is_switch_blog = false;

    if( is_multisite() ) {

      if( $filter_fields['select_site_id'] !== (int) get_current_blog_id() ) {

        $is_switch_blog = true;

        switch_to_blog( $filter_fields['select_site_id'] );

      }

    }

    $posts = get_posts( $post_args );

    if( empty( $posts ) ) {

      $posts = array();

    }

    ob_start();

    self::print_found_items_posts( $posts , $filter_fields );

    $found_items_html = ob_get_contents();

    ob_end_clean();

    if( $is_switch_blog ) {

      restore_current_blog();

    }

    $search_items = array(
      'items' => $found_items_html,
      'count' => sprintf( _n( '%s Post' , '%s Posts' , count( $posts ) ) , count( $posts ) ),
    );

    return $search_items;

  }

  protected static function get_prepare_bulk_items( $do_run , $post_ids , $filter_fields , $bulk_fields ) {

    if( empty( $post_ids ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post.' , 'my-wp' ) ) );

    }

    if( empty( $filter_fields['select_site_id'] ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select Site.' , 'my-wp' ) ) );

    }

    $is_switch_blog = false;

    if( is_multisite() ) {

      if( $filter_fields['select_site_id'] !== (int) get_current_blog_id() ) {

        $is_switch_blog = true;

        switch_to_blog( $filter_fields['select_site_id'] );

      }

    }

    ob_start();

    self::print_prepare_bulk_items( $do_run , $post_ids , $filter_fields , $bulk_fields );

    $prepare_bulk_items_html = ob_get_contents();

    ob_end_clean();

    if( $is_switch_blog ) {

      restore_current_blog();

    }

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

    if( empty( $filter_fields['select_site_id'] ) ) {

      $results['details'] = 'Empty select Site';

      return $results;

    }

    $args = array(
      'is_duplicate_custom_field' => $bulk_fields['is_custom_field'],
      'is_duplicate_featured_image' => $bulk_fields['is_featured_image'],
      'is_duplicate_terms' => $bulk_fields['is_terms'],
    );

    $duplicate_post_from_site_in_network = MywpApi::duplicate_post_from_site_in_network( $filter_fields['select_site_id'] , $post_id , $args );

    $errors = MywpApi::get_errors();

    if( ! empty( $duplicate_post_from_site_in_network ) ) {

      $results['is_success'] = true;

      $results['details'] = sprintf( 'New Post ID: [%s]<br />' , $duplicate_post_from_site_in_network );

    }

    if( ! empty( $errors ) ) {

      foreach( $errors as $error_message ) {

        $results['details'] .= sprintf( '%s<br />' , $error_message );

      }

    }

    return $results;

  }

  protected static function get_mywp_current_setting_screen_header_filter_item_title() {

    return __( 'Filter and search for posts to duplicate' , 'my-wp' );

  }

  protected static function print_setting_screen_header_filter_item_fields() {

    self::print_setting_screen_header_filter_item_field_site();

    self::print_setting_screen_header_filter_item_field_post_type();

    self::print_setting_screen_header_filter_item_field_post_status();

  }

  protected static function get_mywp_current_setting_screen_content_filtered_item_title() {

    return __( 'Select posts to duplicate' , 'my-wp' );

  }

  protected static function get_mywp_current_setting_screen_content_filtered_items_columns() {

    self::get_mywp_current_setting_screen_content_filtered_items_columns_posts();

  }

  protected static function get_mywp_current_setting_screen_content_bulk_form_title() {

    return __( 'Confirm duplication details' , 'my-wp' );

  }

  protected static function print_mywp_current_setting_screen_content_bulk_form() {

    $post_post_type_object = get_post_type_object( 'post' );

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
              <th><?php _e( 'Other' , 'my-wp' ); ?></th>
              <td>
                <label>
                  <input type="checkbox" class="bulk-filter" value="1" data-bulk_filter="is_custom_field" />
                  <?php _e( 'Custom Fields' , 'my-wp' ); ?>
                </label><br />
                <label>
                  <input type="checkbox" class="bulk-filter" value="1" data-bulk_filter="is_featured_image" />
                  <?php echo esc_html( $post_post_type_object->labels->featured_image ); ?>
                </label><br/>
                <label>
                  <input type="checkbox" class="bulk-filter" value="1" data-bulk_filter="is_terms" />
                  <?php _e( 'All Terms' , 'my-wp' ); ?>
                </label><br/>
              </td>
            </tr>
          </tbody>
        </table>

        <div style="text-align: center;">

          <p>
            <button type="button" class="button button-large button-primary do-bulk" data-do_run="1" data-confirm_run="0">
              <?php _e( 'Duplicate Posts' , 'my-wp' ); ?>
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
        <p><?php _e( 'Please duplicate posts.' , 'my-wp' ); ?></p>

      </div>

      <div class="active-content">

        <p>
          <?php echo _e( 'Bulk items' ); ?>:
          <span class="items-count"></span>
        </p>

        <table class="wp-list-table widefat fixed striped table-view-list posts">
          <thead>
            <th class="id"><?php _e( 'Post ID' , 'my-wp' ); ?></th>
            <th class="is-process"><?php _e( 'Bulk process' , 'my-wp' ); ?></th>
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

    ?>

    <?php foreach( $post_ids as $post_id ) : ?>

      <?php $post = get_post( $post_id ); ?>

      <tr class="result-item item item-<?php echo esc_attr( $post_id ); ?> wait">
        <th class="id">
          <?php echo esc_html( $post_id ); ?>
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
        <td class="details"></td>
      </tr>

    <?php endforeach; ?>

    <?php

  }

}

MywpSettingScreenBulkDuplicatePostFromSite::init();

endif;
