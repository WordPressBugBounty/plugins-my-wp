<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAbstractSettingBulkModule' ) ) :

abstract class MywpAbstractSettingBulkModule extends MywpAbstractSettingModule {

  static protected $setting_screen_title = false;

  protected static function get_setting_screen_title() {}

  private static function get_filter_fields() {

    $filter_fields = array();

    if( empty( $_POST['filter_fields'] ) ) {

      return $filter_fields;

    }

    foreach( $_POST['filter_fields'] as $filter_key => $filter_value ) {

      $filter_key = MywpHelper::sanitize_text( $filter_key );

      if( in_array( $filter_key , array( 'select_site_id' ) , true ) ) {

        $filter_fields[ $filter_key ] = MywpHelper::sanitize_number( $filter_value );

      } elseif( in_array( $filter_key , array( 'select_post_type' , 'select_post_status' ) , true ) ) {

        $filter_fields[ $filter_key ] = MywpHelper::sanitize_text( $filter_value );

      }

    }

    $filter_fields = static::custom_filter_fields( $filter_fields );

    return $filter_fields;

  }

  protected static function custom_filter_fields( $filter_fields ) {

    return $filter_fields;

  }

  private static function get_bulk_fields() {

    $bulk_fields = array();

    if( empty( $_POST['bulk_fields'] ) ) {

      return $bulk_fields;

    }

    foreach( $_POST['bulk_fields'] as $bulk_key => $bulk_value ) {

      $bulk_key = MywpHelper::sanitize_text( $bulk_key );

      if( in_array( $bulk_key , array( 'is_custom_field' , 'is_featured_image' , 'is_terms' ) , true ) ) {

        $bulk_fields[ $bulk_key ] = 0;

        if( ! empty( $bulk_value ) ) {

          $bulk_fields[ $bulk_key ] = 1;

        }

      } elseif( in_array( $bulk_key , array( 'meta_key' ) , true ) ) {

        $bulk_fields[ $bulk_key ] = MywpHelper::sanitize_text( $bulk_value );

      } elseif( in_array( $bulk_key , array( 'meta_value' ) , true ) ) {

        $bulk_fields[ $bulk_key ] = maybe_unserialize( stripslashes_deep( $bulk_value ) );

      } else {

        $bulk_fields[ $bulk_key ] = $bulk_value;

      }

    }

    $bulk_fields = static::custom_bulk_fields( $bulk_fields );

    return $bulk_fields;

  }

  protected static function custom_bulk_fields( $bulk_fields ) {

    return $bulk_fields;

  }

  protected static function after_init() {

    $class = get_called_class();

    add_action( 'mywp_wp_loaded' , array( $class , 'mywp_wp_loaded' ) );

  }

  public static function mywp_wp_loaded() {}

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ static::$id ] = array(
      'title' => static::get_setting_screen_title(),
      'menu' => 'bulk',
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_ajax_manager() {

    $class = get_called_class();

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'search_items' ) , array( $class , 'ajax_search_items' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'prepare_bulk_items' ) , array( $class , 'ajax_prepare_bulk_items' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'do_bulk_item' ) , array( $class , 'ajax_do_bulk_item' ) );

  }

  public static function ajax_search_items() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'search_items' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $filter_fields = static::get_filter_fields();

    $search_items = static::get_search_items( $filter_fields );

    if( empty( $search_items ) ) {

      return false;

    }

    wp_send_json_success( array( 'search_items_html' => $search_items['items'] , 'search_items_count_html' => $search_items['count'] ) );

  }

  protected static function get_search_items( $filter_fields ) {}

  public static function ajax_prepare_bulk_items() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'prepare_bulk_items' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $do_run = false;

    if( ! empty( $_POST['do_run'] ) ) {

      $do_run = true;

    }

    $item_ids = array();

    if( ! empty( $_POST['item_ids'] ) ) {

      if( is_array( $_POST['item_ids'] ) ) {

        foreach( $_POST['item_ids'] as $item_id ) {

          $item_ids[] = (int) $item_id;

        }

      }

    }

    $filter_fields = static::get_filter_fields();

    $bulk_fields = static::get_bulk_fields();

    $prepare_bulk_items = static::get_prepare_bulk_items( $do_run , $item_ids , $filter_fields , $bulk_fields );

    wp_send_json_success( array( 'bulk_items_html' => $prepare_bulk_items['bulk_items'] , 'bulk_items_count_html' => $prepare_bulk_items['count'] ) );

  }

  protected static function get_prepare_bulk_items( $do_run , $item_ids , $filter_fields , $bulk_fields ) {}

  public static function ajax_do_bulk_item() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'do_bulk_item' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $do_run = false;

    if( ! empty( $_POST['do_run'] ) ) {

      $do_run = true;

    }

    $item_id = false;

    if( ! empty( $_POST['item_id'] ) ) {

      $item_id = (int) $_POST['item_id'];

    }

    $filter_fields = static::get_filter_fields();

    $bulk_fields = static::get_bulk_fields();

    if( $do_run ) {

      $do_bulk_item = static::do_bulk_item( $item_id , $filter_fields , $bulk_fields );

      $is_success = $do_bulk_item['is_success'];

      $details = $do_bulk_item['details'];

    } else {

      $is_success = true;

      $details = 'Dry run';

    }

    wp_send_json_success( array( 'is_success' => $is_success , 'details' => $details ) );

  }

  protected static function do_bulk_item( $item_id , $filter_fields , $bulk_fields ) {}

  public static function mywp_current_admin_print_footer_scripts() {

    ?>
    <style>
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-filter-items {
      background: #fafafa;
      border: 1px solid #eee;
      padding: 20px;
      margin: 0 auto 50px auto;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents {
      margin: 0 auto 50px auto;
      position: relative;
      min-height: 120px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.check {
      width: 2.2em;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.id {
      width: 6%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.type {
      width: 8%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.status {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.title {
      width: 20%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table thead th.thumbnail {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table tbody td img {
      max-width: 100%;
      max-height: 60px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table tbody td ul {
      display: block;
      list-style-type: disc;
      list-style-position: inside;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #filtered-items .wp-list-table tbody td ul li {
      display: list-item;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents .active-content {
      opacity: 0.2;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents.active .active-content {
      opacity: 1;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents .disabled-content {
      position: absolute;
      width: 100%;
      height: 100%;
      margin: 0 auto;
      text-align: center;
      background: rgba(0, 0, 0, 0.2);
      min-height: 130px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents.active .disabled-content {
      display: none;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents .disabled-content p {
      color: #f00;
      font-size: 22px;
      font-weight: bold;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents .active-content ul {
      margin: 0;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section .bulk-contents .active-content ul li {
      margin: 0;
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table thead th.id {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table thead th.is-process {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table thead th.update-arrow {
      width: 2.2em;
      color: #aaa;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr td.is-process .processing,
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr td.is-process .success,
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr td.is-process .warning {
      display: none;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.wait td.is-process .processing,
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.success td.is-process .success,
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.warning td.is-process .warning {
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.wait td.is-process .processing {
      display: block;
      opacity: 0.5;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.processing td.is-process .processing {
      display: block;
      opacity: 1;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr.processing td.is-process .processing .dashicons-update {
      -webkit-animation: spin 1.5s linear infinite;
      -moz-animation: spin 1.5s linear infinite;
      -ms-animation: spin 1.5s linear infinite;
      -o-animation: spin 1.5s linear infinite;
      animation: spin 1.5s linear infinite;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr td.is-process .success {
      color: #19bf1e;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #bulk-results .wp-list-table tbody tr td.is-process .warning {
      color: #c50d0d;
    }
    </style>
    <script>
    jQuery(function( $ ) {

      let $bulk_filter_items = $('#bulk-filter-items');

      let $filtered_items = $('#filtered-items');

      let $bulk_form = $('#bulk-form');

      let $bulk_form_selected_items = $bulk_form.find('.active-content table tr.selected-items td');

      let $bulk_results = $('#bulk-results');

      let filter_fields = {};

      let bulk_fields = {};

      let do_run = 0;

      function get_custom_key_value( $el ) {

        if( $el.prop('type') === 'checkbox' ) {

          if( $el.prop('checked') ) {

            return $el.val();

          }

          return 0;

        }

        return $el.val();

      }

      function bulk_filter_item_search() {

        filter_fields = {};

        $bulk_filter_items.find('.item-filter').each( function( index , el ) {

          let key = $(el).data('select_key');

          let value = get_custom_key_value( $(el) );

          filter_fields[ key ] = value;

        });

        let $spinner = $bulk_filter_items.find('.spinner');

        $spinner.css('visibility', 'visible');

        $filtered_items.find('.wp-list-table tbody').empty();

        $filtered_items.removeClass('active');

        $bulk_form_selected_items.empty();

        $bulk_form.removeClass('active');

        $bulk_results.find('.wp-list-table tbody').empty();

        $bulk_results.removeClass('active');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'search_items' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'search_items' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'search_items' ) ) ); ?>',
          filter_fields: filter_fields
        };

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          if( ! xhr.success ) {

            alert( xhr.data.error );

            return false;

          }

          if( xhr.data.search_items_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $filtered_items.find('.wp-list-table tbody').html( xhr.data.search_items_html );

          $filtered_items.find('.items-count').html( xhr.data.search_items_count_html );

          $filtered_items.addClass('active');

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      }

      $bulk_filter_items.find('#bulk-filter-search').on('click', function() {

        bulk_filter_item_search();

      });

      function get_selected_items() {

        let items = [];

        $filtered_items.find('tbody tr').each( function( index , el ) {

          let $check = $(el).find('th.check .filtered-select-item');

          if( $check.prop('checked') ) {

            let item = {
              ID: $check.val(),
              title: $(el).find('.title').text()
            };

            items.push( item );

          }

        });

        return items;

      }

      function show_bulk_form() {

        let selected_items = get_selected_items();

        $bulk_form_selected_items.html('');

        if( selected_items.length < 1 ) {

          $bulk_form.removeClass('active');

          return false;

        }

        let html = '<ul>';

        $.each( selected_items , function( index , item ) {

          html += '<li><code>[' + item.ID + ']</code> ' + item.title + '</li>'

        });

        html += '</ul>';

        $bulk_form_selected_items.html( html );

        $bulk_form.addClass('active');

        return false;

      }

      $(document).on('click', '#filtered-items .wp-list-table tbody tr th.check .filtered-select-item', function() {

        show_bulk_form();

      });

      $filtered_items.find('#all-items-check').on('click', function() {

        let is_check = $(this).prop('checked');

        let $table = $(this).parent().parent().parent().parent().parent();

        $table.find('tbody tr').each( function( index , el ) {

          let $check = $(el).find('th.check .filtered-select-item');

          $check.prop( 'checked' , is_check );

        });

        show_bulk_form();

      });

      $bulk_form.find('.do-bulk').on('click', function() {

        let confirm_run = parseInt( $(this).data('confirm_run') );

        do_run = parseInt( $(this).data('do_run') );

        let $bulk_form = $(this).parent().parent().parent();

        bulk_fields = {};

        $bulk_form.find('.bulk-filter').each( function( index , el ) {

          let key = $(el).data('bulk_filter');

          let value = get_custom_key_value( $(el) );

          bulk_fields[ key ] = value;

        });

        let $spinner = $bulk_form.find('.spinner');

        let selected_items = get_selected_items();

        let item_ids = [];

        if( selected_items.length > 0 ) {

          $.each( selected_items , function( index , item ) {

            item_ids.push( item.ID );

          });

        }

        if( confirm_run === 1 ) {

          if( ! window.confirm( mywp_admin_setting.confirm_update_message ) ) {

            return false;

          }

        }

        $spinner.css('visibility', 'visible');

        $bulk_results.find('.wp-list-table tbody').empty();

        $bulk_results.removeClass('active');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'prepare_bulk_items' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'prepare_bulk_items' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'prepare_bulk_items' ) ) ); ?>',
          do_run: do_run,
          item_ids: item_ids,
          filter_fields: filter_fields,
          bulk_fields: bulk_fields
        };

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          if( ! xhr.success ) {

            alert( xhr.data.error );

            return false;

          }

          if( xhr.data.bulk_items_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $bulk_results.find('.wp-list-table tbody').html( xhr.data.bulk_items_html );

          $bulk_results.find('.items-count').html( xhr.data.bulk_items_count_html );

          $bulk_results.addClass('active');

          let scroll_position = ( $bulk_results.offset().top - 40 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

          do_bulk_item();

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

      function do_bulk_item() {

        let $bulk_item = $bulk_results.find('.wp-list-table tbody tr.result-item.wait').first();

        let $is_process = $bulk_item.find('td.is-process');

        if( $bulk_item.length < 1 ) {

          alert( mywp_admin_setting.finish_message );

          return false;

        }

        $bulk_item.removeClass('wait').addClass('processing');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'do_bulk_item' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'do_bulk_item' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'do_bulk_item' ) ) ); ?>',
          item_id: $bulk_item.find('.item-id').val(),
          do_run: do_run,
          filter_fields: filter_fields,
          bulk_fields: bulk_fields
        };

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            $bulk_item.addClass('warning');

            return false;

          }

          if( ! xhr.success ) {

            $bulk_item.addClass('warning');

            return false;

          }

          $bulk_item.find('td.details').html( xhr.data.details );

          if( xhr.data.is_success === undefined ) {

            $bulk_item.addClass('warning');

            return false;

          }

          if( ! xhr.data.is_success ) {

            $bulk_item.addClass('warning');

            return false;

          }

          $bulk_item.addClass('success');

          return true;

        }).fail( function( xhr ) {

          $bulk_item.addClass('warning');

          return false;

        }).always( function() {

          $bulk_item.removeClass('processing');

          do_bulk_item();

        });

      }

    });
    </script>
    <?php

    static::current_mywp_current_admin_print_footer_scripts();

  }

  protected static function current_mywp_current_admin_print_footer_scripts() {}

  public static function mywp_current_setting_screen_header() {

    static::print_mywp_current_setting_screen_header_filter_items();

  }

  protected static function print_mywp_current_setting_screen_header_filter_items() {

    ?>

    <h3 class="mywp-setting-screen-subtitle">
      1: <?php echo esc_html( static::get_mywp_current_setting_screen_header_filter_item_title() ); ?>
    </h3>

    <div id="bulk-filter-items">

      <?php static::print_setting_screen_header_filter_item_fields(); ?>

      <button type="button" id="bulk-filter-search" class="button button-secondary">
        <?php _e( 'Filter and Search' , 'my-wp' ); ?>
      </button>

      <span class="spinner"></span>

    </div>

    <?php

  }

  protected static function get_mywp_current_setting_screen_header_filter_item_title() {}

  protected static function print_setting_screen_header_filter_item_fields() {}

  protected static function print_setting_screen_header_filter_item_field_post_type() {

    $all_post_types = MywpApi::get_all_post_types();

    ?>

    <select class="item-filter" data-select_key="select_post_type">
      <option value="">-----</option>
      <?php foreach( $all_post_types as $key => $post_type ) : ?>

        <option value="<?php echo esc_attr( $post_type->name ); ?>">
          [<?php echo esc_html( $post_type->name ); ?>]
          <?php echo esc_html( $post_type->label ); ?>
        </option>

      <?php endforeach; ?>
    </select>

    <?php

  }

  protected static function print_setting_screen_header_filter_item_field_post_status() {

    $all_post_statuses = MywpApi::get_all_post_statuses();

    ?>

    <select class="item-filter" data-select_key="select_post_status">
      <option value="">-----</option>
      <option value="any">[any] <?php _e( 'All' ); ?></option>
      <?php foreach( $all_post_statuses as $key => $post_status ) : ?>

        <option value="<?php echo esc_attr( $post_status->name ); ?>">
          [<?php echo esc_html( $post_status->name ); ?>]
          <?php echo esc_html( $post_status->label ); ?>
        </option>

      <?php endforeach; ?>

    </select>

    <?php

  }

  protected static function print_setting_screen_header_filter_item_field_site() {

    if( is_multisite() ) {

      $all_sites = MywpHelper::get_all_sites();

    }

    ?>

    <select class="item-filter" data-select_key="select_site_id">
      <?php if( is_multisite() ) : ?>

        <option value="">-----</option>

        <?php foreach( $all_sites as $WP_Site ) : ?>

          <option value="<?php echo esc_attr( $WP_Site->blog_id ); ?>">
            [<?php echo esc_html( $WP_Site->blog_id ); ?>]
            <?php switch_to_blog( $WP_Site->blog_id ); ?>
            <?php echo esc_html( get_bloginfo( 'name' , 'display' ) ); ?>
            <?php restore_current_blog(); ?>
          </option>

        <?php endforeach; ?>

      <?php else : ?>

        <option value="<?php echo esc_attr( get_current_blog_id() ); ?>">
          [<?php echo esc_html( get_current_blog_id() ); ?>]
          <?php echo esc_html( get_bloginfo( 'name' , 'display' ) ); ?>
        </option>

      <?php endif; ?>
    </select>

    <?php

  }

  protected static function print_found_items_posts( $posts , $filter_fields ) {

    $taxonomies = array();

    if( ! empty( $filter_fields['select_post_type'] ) ) {

      $taxonomies_args = array(
        'object_type' => array( $filter_fields['select_post_type'] ),
      );

      $taxonomies = get_taxonomies( $taxonomies_args , 'objects' );

    }

    ?>

    <?php if( empty( $posts ) ) : ?>

      <tr>
        <td colspan="8"><?php _e( 'Post not found.' , 'my-wp' ); ?></td>
      </tr>

    <?php else : ?>

      <?php foreach( $posts as $post ) : ?>

        <?php $post_id = (int) $post->ID; ?>

        <tr class="item item-<?php echo esc_attr( $post_id ); ?>">
          <th class="check">
            <input type="checkbox" class="filtered-select-item" value="<?php echo esc_attr( $post_id ); ?>" />
          </th>
          <td class="id"><?php echo esc_html( $post_id ); ?></td>
          <td class="type"><?php echo esc_html( $post->post_type ); ?></td>
          <td class="status"><?php echo esc_html( $post->post_status ); ?></td>
          <td class="title"><?php echo esc_html( $post->post_title ); ?></td>
          <td class="thumbnail">
            <?php if( has_post_thumbnail( $post_id ) ) : ?>
              <img src="<?php echo esc_url( get_the_post_thumbnail_url( $post_id ) , 'thumbnail' ); ?>" />
            <?php endif; ?>
          </td>
          <td class="metas">
            <textarea readonly class="large-text"><?php echo esc_textarea( print_r( get_post_meta( $post_id ) , true ) ); ?></textarea>
          </td>
          <td class="terms">

            <?php
            $post_terms = array();

            if( ! empty( $taxonomies ) ) {

              foreach( $taxonomies as $taxonomy ) {

                $terms = wp_get_post_terms( $post_id , $taxonomy->name );

                if( empty( $terms ) ) {

                  continue;

                }

                if( is_wp_error( $terms ) ) {

                  continue;

                }

                foreach( $terms as $term ) {

                  $post_terms[ $taxonomy->name ][] = $term;

                }

              }

            }
            ?>

            <?php if( ! empty( $post_terms ) ) : ?>

              <ul>

                <?php foreach( $post_terms as $taxonomy_name => $terms ) : ?>

                  <?php foreach( $terms as $term ) : ?>

                    <li>
                      [<?php echo esc_html( $taxonomy_name ); ?>]
                      <?php echo esc_html( $term->name ); ?>
                    </li>

                  <?php endforeach; ?>

                <?php endforeach; ?>

              </ul>

            <?php endif; ?>
          </td>
        </tr>

      <?php endforeach; ?>

    <?php endif; ?>

    <?php

  }

  public static function mywp_current_setting_screen_content() {

    static::print_mywp_current_setting_screen_content_filtered_items();

    static::print_mywp_current_setting_screen_content_bulk_form();

    static::print_mywp_current_setting_screen_content_bulk_result();

  }

  protected static function print_mywp_current_setting_screen_content_filtered_items() {

    ?>

    <h3 class="mywp-setting-screen-subtitle">
      2: <?php echo esc_html( static::get_mywp_current_setting_screen_content_filtered_item_title() ); ?>
    </h3>

    <div id="filtered-items" class="bulk-contents">

      <div class="disabled-content">

        <p><span class="dashicons dashicons-arrow-up-alt"></span></p>

        <p><?php echo esc_html( __( 'Please filter and search.' , 'my-wp' ) ); ?></p>

      </div>

      <div class="active-content">

        <p>
          <?php echo _e( 'Search results' ); ?>:
          <span class="items-count"></span>
        </p>

        <table class="wp-list-table widefat fixed striped table-view-list posts">
          <thead>
            <?php static::get_mywp_current_setting_screen_content_filtered_items_columns(); ?>
          </thead>
          <tbody>
          </tbody>
        </table>

      </div>

    </div>

    <?php

  }

  protected static function get_mywp_current_setting_screen_content_filtered_item_title() {}

  protected static function get_mywp_current_setting_screen_content_filtered_items_columns() {}

  protected static function get_mywp_current_setting_screen_content_filtered_items_columns_posts() {

    $post_post_type_object = get_post_type_object( 'post' );

    ?>
    <th class="check">
      <label>
        <input type="checkbox" id="all-items-check" value="1" />
      </label>
    </th>
    <th class="id"><?php _e( 'ID' , 'my-wp' ); ?></th>
    <th class="type"><?php _e( 'Post Type' , 'my-wp' ); ?></th>
    <th class="status"><?php _e( 'Post Status' , 'my-wp' ); ?></th>
    <th class="title"><?php _e( 'Post Title' , 'my-wp' ); ?></th>
    <th class="thumbnail"><?php echo esc_html( $post_post_type_object->labels->featured_image ); ?></th>
    <th class="metas"><?php _e( 'All Post Metas' , 'my-wp' ); ?></th>
    <th class="terms"><?php _e( 'Terms' , 'my-wp' ); ?></th>
    <?php

  }

  protected static function print_mywp_current_setting_screen_content_bulk_form() {}

  protected static function get_mywp_current_setting_screen_content_bulk_form_title() {}

  protected static function print_mywp_current_setting_screen_content_bulk_result() {

    ?>

    <h3 class="mywp-setting-screen-subtitle">
      4: <?php echo esc_html( static::get_mywp_current_setting_screen_content_bulk_result_title() ); ?>
    </h3>

    <?php

    static::print_mywp_current_setting_screen_content_bulk_result_contents();

  }

  protected static function get_mywp_current_setting_screen_content_bulk_result_title() {

    return __( 'Bulk processing results' , 'my-wp' );

  }

  protected static function print_mywp_current_setting_screen_content_bulk_result_contents() {}

}

endif;
