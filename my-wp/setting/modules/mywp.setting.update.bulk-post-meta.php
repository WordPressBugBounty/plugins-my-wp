<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenUpdateBulkPostMeta' ) ) :

final class MywpSettingScreenUpdateBulkPostMeta extends MywpAbstractSettingModule {

  static protected $id = 'update_bulk_post_meta';

  static protected $priority = 10;

  static private $menu = 'update';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Bulk Post custom fields' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_ajax_manager() {

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'show_posts' ) , array( __CLASS__ , 'ajax_show_posts' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'show_bulk_post_rows' ) , array( __CLASS__ , 'ajax_show_bulk_post_rows' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'update_post_meta' ) , array( __CLASS__ , 'ajax_update_post_meta' ) );

  }

  public static function ajax_show_posts() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'show_posts' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $selected_post_type = self::get_selected_post_type();

    if( empty( $selected_post_type ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post Type.' , 'my-wp' ) ) );

    }

    $selected_post_status = self::get_selected_post_status();

    if( empty( $selected_post_status ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post Status.' , 'my-wp' ) ) );

    }

    $post_args = array(
      'numberposts' => -1,
      'orderby' => 'ID',
      'order' => 'ASC',
      'post_type' => $selected_post_type,
      'post_status' => $selected_post_status,
    );

    $posts = get_posts( $post_args );

    ob_start();

    self::print_post_items( $posts , $post_args );

    $result_html = ob_get_contents();

    ob_end_clean();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function ajax_show_bulk_post_rows() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'show_bulk_post_rows' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $post_ids = array();

    if( ! empty( $_POST['post_ids'] ) && is_array( $_POST['post_ids'] ) ) {

      foreach( $_POST['post_ids'] as $post_id ) {

        $post_ids[] = (int) $post_id;

      }

    }

    if( empty( $post_ids ) ) {

      wp_send_json_error( array( 'error' => __( 'Please select an Post.' , 'my-wp' ) ) );

    }

    $meta_key = false;

    if( ! empty( $_POST['meta_key'] ) ) {

      $meta_key = sanitize_key( $_POST['meta_key'] );

    }

    if( empty( $meta_key ) ) {

      wp_send_json_error( array( 'error' => __( 'Please enter the Meta key.' , 'my-wp' ) ) );

    }

    $bulk_meta_value = false;

    if( isset( $_POST['bulk_meta_value'] ) ) {

      $bulk_meta_value = $_POST['bulk_meta_value'];

    }

    ob_start();

    self::print_update_items( $post_ids , $meta_key , $bulk_meta_value );

    $result_html = ob_get_contents();

    ob_end_clean();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function ajax_update_post_meta() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'update_post_meta' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    $post_id = false;

    if( ! empty( $_POST['post_id'] ) ) {

      $post_id = (int) $_POST['post_id'];

    }

    if( empty( $post_id ) ) {

      return false;

    }

    $meta_key = false;

    if( ! empty( $_POST['meta_key'] ) ) {

      $meta_key = sanitize_key( $_POST['meta_key'] );

    }

    if( empty( $meta_key ) ) {

      return false;

    }

    $bulk_meta_value = false;

    if( isset( $_POST['bulk_meta_value'] ) ) {

      $bulk_meta_value = $_POST['bulk_meta_value'];

    }

    if( is_array( $bulk_meta_value ) or is_object( $bulk_meta_value ) ) {

      return false;

    }

    $bulk_meta_value_unserialize = maybe_unserialize( stripslashes_deep( $bulk_meta_value ) );

    $is_do_run = false;

    if( ! empty( $_POST['is_do_run'] ) ) {

      $is_do_run = true;

    }

    $is_updated = true;

    if( $is_do_run ) {

      $from_meta_value = get_post_meta( $post_id , $meta_key , true );

      if( ! empty( $bulk_meta_value_unserialize ) ) {

        $is_updated = update_post_meta( $post_id , $meta_key , $bulk_meta_value_unserialize , $from_meta_value );

      } else {

        $is_updated = update_post_meta( $post_id , $meta_key , $bulk_meta_value , $from_meta_value );

      }

    }

    wp_send_json_success( array( 'is_updated' => $is_updated , 'is_do_run' => $is_do_run ) );

  }

  public static function mywp_current_admin_print_footer_scripts() {

    ?>
    <style>
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-filters {
      background: #fafafa;
      border: 1px solid #eee;
      padding: 20px;
      margin: 0 auto 50px auto;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts {
      margin: 0 auto 50px auto;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts .wp-list-table thead th.check {
      width: 2.2em;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts .wp-list-table thead th.id {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts .wp-list-table thead th.type {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts .wp-list-table thead th.status {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-posts .wp-list-table thead th.title {
      width: 20%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form {
      position: relative;
      margin: 0 auto 50px auto;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form.disabled .active-content {
      opacity: 0.1;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form .disabled-content {
      position: absolute;
      width: 100%;
      height: 100%;
      margin: 0 auto;
      text-align: center;
      background: rgba(0, 0, 0, 0.2);
      display: none;
      min-height: 130px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form.disabled .disabled-content {
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form .disabled-content p {
      color: #f00;
      font-size: 22px;
      font-weight: bold;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form .active-content .selected-posts ul {
      margin: 0;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-form .active-content .selected-posts ul li {
      margin: 0;
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results {
      position: relative;
      margin: 0 auto 50px auto;
      min-height: 200px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results.disabled .active-content {
      opacity: 0.1;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .disabled-content {
      position: absolute;
      width: 100%;
      height: 100%;
      margin: 0 auto;
      text-align: center;
      background: rgba(0, 0, 0, 0.2);
      display: none;
      min-height: 130px;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results.disabled .disabled-content {
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .disabled-content p {
      color: #f00;
      font-size: 22px;
      font-weight: bold;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table thead th.id {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table thead th.is-updated {
      width: 10%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table thead th.from-meta {
      width: 30%;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table thead th.update-arrow {
      width: 2.2em;
      color: #aaa;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr td.is-updated .updating {
      display: none;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr.wait td.is-updated .updating {
      display: block;
      opacity: 0.5;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr.processing td.is-updated .updating {
      display: block;
      opacity: 1;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr.processing td.is-updated .updating .dashicons-update {
      -webkit-animation: spin 1.5s linear infinite;
      -moz-animation: spin 1.5s linear infinite;
      -ms-animation: spin 1.5s linear infinite;
      -o-animation: spin 1.5s linear infinite;
      animation: spin 1.5s linear infinite;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr td.is-updated .success {
      color: #19bf1e;
      display: none;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr td.is-updated.success .success {
      display: block;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr td.is-updated .warning {
      color: #c50d0d;
      display: none;
    }
    body.mywp-setting #setting-screen #setting-screen-section-wrap #setting-screen-section #setting-update-bulk-post-meta-update-results .wp-list-table tbody tr td.is-updated.warning .warning {
      display: block;
    }
    </style>
    <script>
    jQuery(function( $ ) {

      let $setting_update_bulk_post_meta_posts = $('#setting-update-bulk-post-meta-posts');

      let $setting_update_bulk_post_meta_update_form = $('#setting-update-bulk-post-meta-update-form');

      let $setting_update_bulk_post_meta_update_results = $('#setting-update-bulk-post-meta-update-results');

      function show_all_posts( post_type , post_status ) {

        let $spinner = $('<span class="spinner"></span>');

        $spinner.css('visibility', 'visible');

        $setting_update_bulk_post_meta_posts.html( $spinner );

        $setting_update_bulk_post_meta_update_results.find('.wp-list-table tbody').empty();

        $setting_update_bulk_post_meta_update_results.addClass('disabled');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'show_posts' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'show_posts' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'show_posts' ) ) ); ?>',
          selected_post_type: post_type,
          selected_post_status: post_status
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

          if( xhr.data.result_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $setting_update_bulk_post_meta_posts.html( xhr.data.result_html );

          change_meta_update_form();

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      }

      show_all_posts( '<?php echo esc_js( self::get_selected_post_type() ); ?>' , '<?php echo esc_js( self::get_selected_post_status() ); ?>' );

      $('#setting-update-bulk-post-meta-show-posts').on('click', function() {

        let $setting_update_bulk_post_meta_filters = $(this).parent();

        let selected_post_type = $setting_update_bulk_post_meta_filters.find('#setting-update-bulk-post-meta-selected-post-type').val();

        let selected_post_status = $setting_update_bulk_post_meta_filters.find('#setting-update-bulk-post-meta-selected-post-status').val();

        show_all_posts( selected_post_type , selected_post_status )

      });

      $(document).on('click', '#update-bulk-post-meta-all-posts-check', function() {

        let all_checked = $(this).prop('checked');

        let $table = $(this).parent().parent().parent().parent().parent();

        $table.find('tbody tr').each( function( index , el ) {

          let $check = $(el).find('th.check .update-bulk-post-meta-select-post-id');

          $check.prop('checked', all_checked);

        });

        change_meta_update_form();

      });

      function get_selected_posts() {

        let posts = [];

        let $list_table = $(document).find('#setting-update-bulk-post-meta-posts .wp-list-table');

        if( $list_table.length < 1 ) {

          $setting_update_bulk_post_meta_update_form.addClass('disabled');

          return posts;

        }

        $list_table.find('tbody tr').each( function( index , el ) {

          let $check = $(el).find('th.check .update-bulk-post-meta-select-post-id');

          if( $check.prop('checked') ) {

            let post = {
              ID: $check.val(),
              type: $(el).find('.type').text(),
              status: $(el).find('.status').text(),
              title: $(el).find('.title').text()
            };

            posts.push( post );

          }

        });

        return posts;

      }

      function change_meta_update_form() {

        let selected_posts = get_selected_posts();

        let $meta_update_form_selected_posts = $setting_update_bulk_post_meta_update_form.find('.active-content table tr.selected-posts td');

        $meta_update_form_selected_posts.html('');

        if( selected_posts.length < 1 ) {

          $setting_update_bulk_post_meta_update_form.addClass('disabled');

          return false;

        }

        $setting_update_bulk_post_meta_update_form.removeClass('disabled');

        let html = '<ul>';

        $.each( selected_posts , function( index , post ) {

          html += '<li><code>[' + post.ID + ']</code> ' + post.title + '</li>'

        });

        html += '</ul>';

        $meta_update_form_selected_posts.html( html );

        return false;

      }

      $(document).on('click', '#setting-update-bulk-post-meta-posts .wp-list-table tbody tr th.check .update-bulk-post-meta-select-post-id', function() {

        change_meta_update_form();

      });

      $('.setting-update-bulk-post-meta-update-post-meta').on('click', function() {

        let is_do_run = parseInt( $(this).data('do_run') );

        let $update_form = $(this).parent().parent().parent();

        let $spinner = $update_form.find('.spinner');

        let meta_key = $update_form.find('#setting-update-bulk-post-meta-update-post-meta-key').val();

        let bulk_meta_value = $update_form.find('#setting-update-bulk-post-meta-update-post-meta-val').val();

        let selected_posts = get_selected_posts();

        let post_ids = [];

        if( selected_posts.length > 0 ) {

          $.each( selected_posts , function( index , post ) {

            post_ids.push( post.ID );

          });

        }

        if( is_do_run === 1 ) {

          let is_confirm = window.confirm( mywp_admin_setting.confirm_update_message );

          if( ! is_confirm ) {

            return false;

          }

        }

        $spinner.css('visibility', 'visible');

        $setting_update_bulk_post_meta_update_results.find('.wp-list-table tbody').empty();

        $setting_update_bulk_post_meta_update_results.addClass('disabled');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'show_bulk_post_rows' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'show_bulk_post_rows' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'show_bulk_post_rows' ) ) ); ?>',
          post_ids: post_ids,
          meta_key: meta_key,
          bulk_meta_value: bulk_meta_value
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

          if( xhr.data.result_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $setting_update_bulk_post_meta_update_results.removeClass('disabled');

          $setting_update_bulk_post_meta_update_results.find('.wp-list-table tbody').html( xhr.data.result_html );

          let scroll_position = $setting_update_bulk_post_meta_update_results.offset().top;

          scroll_position = ( scroll_position - 40 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

          update_post_meta( is_do_run , meta_key , bulk_meta_value );

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

      function update_post_meta( is_do_run , meta_key , bulk_meta_value ) {

        let $setting_update_bulk_post_meta_update_results = $(document).find('#setting-update-bulk-post-meta-update-results');

        let $update_post = $setting_update_bulk_post_meta_update_results.find('.wp-list-table tbody tr.result-post.wait').first();

        let $is_updated = $update_post.find('td.is-updated');

        if( $update_post.length < 1 ) {

          alert( mywp_admin_setting.finish_message );

          return false;

        }

        $update_post.removeClass('wait').addClass('processing');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'update_post_meta' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'update_post_meta' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'update_post_meta' ) ) ); ?>',
          is_do_run: is_do_run,
          post_id: $update_post.find('.post-id').val(),
          meta_key: meta_key,
          bulk_meta_value: bulk_meta_value
        };

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            $is_updated.addClass('warning');

            return false;

          }

          if( ! xhr.success ) {

            $is_updated.addClass('warning');

            return false;

          }

          if( xhr.data.is_updated === undefined ) {

            $is_updated.addClass('warning');

            return false;

          }

          if( ! xhr.data.is_do_run ) {

            $is_updated.append( '<span><?php echo esc_html( esc_js( __( 'Dry run' , 'my-wp' ) ) ); ?></span>' );

          }

          if( ! xhr.data.is_updated ) {

            $is_updated.addClass('warning');

            return false;

          }

          $is_updated.addClass('success');

          return true;

        }).fail( function( xhr ) {

          $is_updated.addClass('warning');

          return false;

        }).always( function() {

          $update_post.removeClass('processing');

          $is_updated.removeClass('update');

          update_post_meta( is_do_run , meta_key , bulk_meta_value );

        });

      }

    });
    </script>
    <?php

  }

  public static function mywp_current_setting_screen_header() {

    $all_post_types = MywpApi::get_all_post_types();

    $all_post_statuses = MywpApi::get_all_post_statuses();

    ?>
    <h3 class="mywp-setting-screen-subtitle">1: <?php _e( 'Filter post' , 'my-wp' ); ?></h3>

    <div id="setting-update-bulk-post-meta-filters">

      <select id="setting-update-bulk-post-meta-selected-post-type">
        <option value="any"><?php _e( 'All' ); ?></option>
        <?php foreach( $all_post_types as $key => $post_type ) : ?>

          <option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( $post_type->name , self::get_selected_post_type() ); ?>>[<?php echo esc_attr( $post_type->name ); ?>] <?php echo esc_attr( $post_type->label ); ?></option>

        <?php endforeach; ?>
      </select>

      <select id="setting-update-bulk-post-meta-selected-post-status">
        <option value="any"><?php _e( 'All' ); ?></option>
        <?php foreach( $all_post_statuses as $key => $post_status ) : ?>

          <option value="<?php echo esc_attr( $post_status->name ); ?>" <?php selected( $post_status->name , self::get_selected_post_status() ); ?>>[<?php echo esc_attr( $post_status->name ); ?>] <?php echo esc_attr( $post_status->label ); ?></option>

        <?php endforeach; ?>

      </select>

      <a href="javascript:void(0);" id="setting-update-bulk-post-meta-show-posts" class="button button-secondary"><span class="dashicons dashicons-filter"></span><?php _e( 'Filter' ); ?></a>

    </div>

    <?php

  }

  public static function mywp_current_setting_screen_content() {

    ?>
    <h3 class="mywp-setting-screen-subtitle">2: <?php _e( 'All Posts' ); ?></h3>

    <div id="setting-update-bulk-post-meta-posts"></div>

    <div id="setting-update-bulk-post-meta-update-form">

      <h3 class="mywp-setting-screen-subtitle">3: <?php _e( 'Bulk update post custom fields' , 'my-wp' ); ?></h3>

      <div class="disabled-content">

        <p><span class="dashicons dashicons-arrow-up-alt"></span></p>
        <p><?php _e( 'Please check posts you want to bulk update.' , 'my-wp' ); ?></p>

      </div>

      <div class="active-content">

        <table class="form-table">
          <tbody>
            <tr class="selected-posts">
              <th><?php _e( 'Update Post IDs' , 'my-wp' ); ?></th>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <th><?php _e( 'Meta key' , 'my-wp' ); ?></th>
              <td><input type="text" class="large-text" id="setting-update-bulk-post-meta-update-post-meta-key" placeholder="<?php echo esc_attr( '_wp_page_template' ); ?>" /></td>
            </tr>
            <tr>
              <th><?php _e( 'Meta value' , 'my-wp' ); ?></th>
              <td><textarea class="large-text" id="setting-update-bulk-post-meta-update-post-meta-val" placeholder="<?php echo esc_attr( 'default' ); ?>"></textarea></td>
            </tr>
          </tbody>
        </table>

        <div style="text-align: center;">

          <p class="mywp-description">
            <span class="dashicons dashicons-lightbulb"></span> <?php _e( 'It is recommend that you backup your database and do a dry run.' , 'my-wp' ); ?>
          </p>

          <p>
            <button type="button" class="button button-large button-primary setting-update-bulk-post-meta-update-post-meta" data-do_run="0"><?php _e( 'Bulk update post meta of checked posts (Dry run)' , 'my-wp' ); ?></button>
          </p>

          <p>
            <button type="button" class="button button-large button-primary button-caution setting-update-bulk-post-meta-update-post-meta" data-do_run="1"><?php _e( 'Bulk update post meta of checked posts' , 'my-wp' ); ?></button>
          </p>

          <p>
            <span class="spinner"></span>
          </p>

        </div>

      </div>

    </div>

    <div id="setting-update-bulk-post-meta-update-results">

      <h3 class="mywp-setting-screen-subtitle">4: <?php _e( 'Finished' , 'my-wp' ); ?></h3>

      <div class="disabled-content">

        <p><span class="dashicons dashicons-arrow-up-alt"></span></p>
        <p><?php _e( 'Please bulk update.' , 'my-wp' ); ?></p>

      </div>

      <div class="active-content">

        <table class="wp-list-table widefat fixed striped table-view-list posts">
          <thead>
            <th class="id"><?php _e( 'ID' , 'my-wp' ); ?></th>
            <th class="is-updated"><?php _e( 'Updated' , 'my-wp' ); ?></th>
            <th class="from-meta"><?php _e( 'From meta value' , 'my-wp' ); ?></th>
            <th class="update-arrow">&nbsp;</th>
            <th class="to-meta"><?php _e( 'To meta value' , 'my-wp' ); ?></th>
          </thead>
          <tbody>
          </tbody>
        </table>

      </div>

    </div>

    <?php

  }

  private static function get_selected_post_type() {

    $selected_post_type = 'post';

    if( ! empty( $_POST['selected_post_type'] ) ) {

      $selected_post_type = strip_tags( $_POST['selected_post_type'] );

    }

    return $selected_post_type;

  }

  private static function get_selected_post_status() {

    $selected_post_status = 'publish';

    if( ! empty( $_POST['selected_post_status'] ) ) {

      $selected_post_status = strip_tags( $_POST['selected_post_status'] );

    }

    return $selected_post_status;

  }

  private static function print_post_items( $posts , $post_args ) {

    ?>

    <p>
      <pre>$results = get_posts( <?php print_r( $post_args ); ?> )</pre>
    </p>

    <p><?php echo _e( 'Search Results' ); ?>: <?php echo count( $posts ); ?><?php _e( 'posts' ); ?></p>

    <?php if( empty( $posts ) ) : ?>

      <p><?php _e( 'Post not found.' , 'my-wp' ); ?></p>

    <?php else : ?>

      <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
          <th class="check"><label><input type="checkbox" id="update-bulk-post-meta-all-posts-check" value="1" /></label></th>
          <th class="id"><?php _e( 'ID' , 'my-wp' ); ?></th>
          <th class="type"><?php _e( 'Post Type' , 'my-wp' ); ?></th>
          <th class="status"><?php _e( 'Post Status' , 'my-wp' ); ?></th>
          <th class="title"><?php _e( 'Post Title' , 'my-wp' ); ?></th>
          <th class="metas"><?php _e( 'All Post Metas' , 'my-wp' ); ?></th>
        </thead>
        <tbody>

          <?php foreach( $posts as $post ) : ?>

            <?php $post_id = (int) $post->ID; ?>

            <tr class="post post-<?php echo esc_attr( $post_id ); ?>">
              <th class="check">
                <input type="checkbox" class="update-bulk-post-meta-select-post-id" value="<?php echo esc_attr( $post_id ); ?>" />
              </th>
              <td class="id"><?php echo esc_html( $post_id ); ?></td>
              <td class="type"><?php echo esc_html( $post->post_type ); ?></td>
              <td class="status"><?php echo esc_html( $post->post_status ); ?></td>
              <td class="title"><?php echo esc_html( $post->post_title ); ?></td>
              <td class="metas"><textarea readonly class="large-text"><?php echo esc_textarea( print_r( get_post_meta( $post_id ) , true ) ); ?></textarea></td>
            </tr>

          <?php endforeach; ?>

        </tbody>
      </table>

    <?php endif; ?>

    <?php

  }

  private static function print_update_items( $post_ids ,  $meta_key , $bulk_meta_value ) {

    $bulk_meta_value_unserialize = maybe_unserialize( stripslashes_deep( $bulk_meta_value ) );

    ?>

    <?php if( empty( $post_ids ) ) : ?>

      <tr>
        <td colspan="5"><?php _e( 'Post not found.' , 'my-wp' ); ?></td>
      </tr>

    <?php else : ?>

      <?php foreach( $post_ids as $post_id ) : ?>

        <?php $post = get_post( $post_id ); ?>

        <?php $from_meta_value = get_post_meta( $post_id , $meta_key , true ); ?>

        <?php $is_updated = 1; ?>

        <tr class="result-post post post-<?php echo esc_attr( $post_id ); ?> wait">
          <th class="id">
            <?php echo $post_id; ?>
            <input type="hidden" class="post-id" value="<?php echo esc_attr( $post_id ); ?>" />
          </th>
          <td class="is-updated">
            <div class="updating">
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
              <?php echo esc_html( $from_meta_value ); ?>
            <?php endif; ?>
          </td>
          <td class="update-arrow">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
          </td>
          <td class="to-meta">
            <?php if( is_array( $bulk_meta_value_unserialize ) or is_object( $bulk_meta_value_unserialize ) ) : ?>
              <textarea class="large-text" readonly="readonly"><?php echo esc_textarea( print_r( $bulk_meta_value_unserialize , true ) ); ?></textarea>
            <?php else : ?>
              <?php echo esc_html( $bulk_meta_value ); ?>
            <?php endif; ?>
          </td>
        </tr>

      <?php endforeach; ?>

    <?php endif; ?>

    <?php

  }

}

MywpSettingScreenUpdateBulkPostMeta::init();

endif;
