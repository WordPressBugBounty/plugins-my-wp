let mywp_popup = {};

jQuery(function( $ ) {

  $('body.mywp-setting .mywp_form').on('submit', function() {

    if( $(this).hasClass('mywp_form_remove') ) {

      if( window.confirm( mywp_admin_setting.confirm_delete_message ) ) {

        $(this).find('.submit .spinner').css('visibility', 'visible');

      } else {

        return false;

      }

    } else {

      $(this).find('.submit .spinner').css('visibility', 'visible');

    }

  });

  $('body.mywp-setting #select-advance-setting-toggle').on('click', function() {

    let $setting_screen_advance = $('#setting-screen-advance');
    let $setting_screen_advance_check = $setting_screen_advance.find('#select-advance-setting-check');
    let toggle = parseInt( $setting_screen_advance_check.val() );

    if( toggle ) {

      $setting_screen_advance_check.val( '0' );
      $setting_screen_advance.removeClass( 'active' );

    } else {

      $setting_screen_advance_check.val( '1' );
      $setting_screen_advance.addClass( 'active' );

    }

    return false;

  });

  $('body.mywp-setting #select-advance-setting-check').on('change', function() {

    let $setting_screen_advance = $('#setting-screen-advance');

    if( $(this).prop('checked') ) {

      $setting_screen_advance.addClass( 'active' );

    } else {

      $setting_screen_advance.removeClass( 'active' );

    }

  });

  $('body.mywp-setting #setting-screen-select-post-type').prop('disabled', '');

  $('body.mywp-setting #setting-screen-select-post-type').on('change', function() {

    $(this).parent().find('.spinner').css('visibility', 'visible');

    let $selected = $(this).find('option:selected');

    let url = $selected.data('post_type_url');

    if( ! url ) {

      return false;

    }

    $(location).attr('href', url);

  });

  $('body.mywp-setting #setting-screen-input-post-id-update').on('click', function() {

    $(this).parent().find('.spinner').css('visibility', 'visible');

    let $input = $(this).parent().find('#setting-screen-input-post-id');

    let url = $input.data('post_url');

    if( ! url ) {

      return false;

    }

    url = url.replace( '###POST_ID###' , Number( $input.val() ) );

    $(location).attr('href', url);

  });

  $('body.mywp-setting #setting-screen-select-taxonomy').prop('disabled', '');

  $('body.mywp-setting #setting-screen-select-taxonomy').on('change', function() {

    $(this).parent().find('.spinner').css('visibility', 'visible');

    let $selected = $(this).find('option:selected');

    let url = $selected.data('taxonomy_url');

    if( ! url ) {

      return false;

    }

    $(location).attr('href', url);

  });

  $('body.mywp-setting .mywp-popup-close').on('click', function() {

    mywp_popup.close();

  });

  $('body.mywp-setting #mywp-popup-bg').on('click', function() {

    mywp_popup.close();


  });

  $('#meta-box-screen-refresh-button').on('click', function() {

    let $button = $(this);
    let url = $button.prop('href');

    $.ajax({
      url: url,
      beforeSend: function( xhr ) {
        $button.parent().find('.dashicons-update').addClass('spin');
      }
    }).done( function( xhr ) {

      location.reload();

    }).fail( function( xhr ) {

      load_meta_box_error();

    });

    return false;

  });

  function load_meta_box_error() {

    $('#meta-box-screen-refresh .dashicons-update').removeClass('spin');

    alert( mywp_admin_setting.error_try_again );

  }

  function render_meta_box_management() {

    $('#meta-boxes-table tbody tr').each( function ( index , el ) {

      let $tr = $(el);
      let action = $tr.find('.meta-box-action-select').val();
      let disabled = false;

      if( action == 'remove' || action == 'hide' ) {

        disabled = true;

      }

      $tr.find('.meta-box-change-title').prop('disabled', disabled);

      if( disabled ) {

        $tr.find('.meta-box-change-title').addClass('disabled');

      } else {

        $tr.find('.meta-box-change-title').removeClass('disabled');

      }

    });

  }

  render_meta_box_management();

  $('#meta-boxes-table .meta-box-action-select').on('change', function() {

    render_meta_box_management();

  });

  function meta_box_bulk_action( action = false ) {

    let defined_action = false;

    if( action == 'remove' || action == 'hide' || action == '' ) {

      defined_action = true;

    }

    if( ! defined_action ) {

      return false;

    }

    $('#meta-boxes-table tbody tr').each( function ( index , el ) {

      let $tr = $(el);

      $tr.find('.meta-box-action-select').val( action );

    });

    render_meta_box_management();

  }

  $('#meta-box-bulk-actions #meta-box-bulk-action-show').on('click', function() {

    meta_box_bulk_action( '' );

  });

  $('#meta-box-bulk-actions #meta-box-bulk-action-remove').on('click', function() {

    meta_box_bulk_action( 'remove' );

  });

  $('#meta-box-bulk-actions #meta-box-bulk-action-hide').on('click', function() {

    meta_box_bulk_action( 'hide' );

  });

  function block_editor_panel_bulk_action( action = false ) {

    let defined_action = false;

    if( action == 'hide' || action == '' ) {

      defined_action = true;

    }

    if( ! defined_action ) {

      return false;

    }

    $('#block-editor-panels-table tbody tr').each( function ( index , el ) {

      let $tr = $(el);

      $tr.find('.block-editor-panel-action-select').val( action );

    });

  }

  $('#block-editor-panel-bulk-actions #block-editor-panel-bulk-action-show').on('click', function() {

    block_editor_panel_bulk_action( '' );

  });

  $('#block-editor-panel-bulk-actions #block-editor-panel-bulk-action-hide').on('click', function() {

    block_editor_panel_bulk_action( 'hide' );

  });

  $('body.mywp-setting #setting-screen-setting-list-columns-refresh-button').on('click', function() {

    let $button = $(this);
    let $button_icon = $button.parent().find('.dashicons-update');
    let url = $button.attr('href');

    if( ! url ) {

      alert( mywp_admin_setting.not_found_update_url );

      return false;

    }

    $button_icon.addClass('spin');

    $.ajax({
      url: url,
      cache: false,
      timeout: 10000
    }).done( function( xhr ) {

      location.reload();

    }).fail( function( xhr ) {

      $button_icon.removeClass('spin');

      alert( mywp_admin_setting.error_try_again );

    });

    return false;

  });

  if( $('body.mywp-setting #setting-screen-setting-list-columns .list-columns-sortable-items').length ) {

    $('body.mywp-setting #setting-screen-setting-list-columns .list-columns-sortable-items').sortable({
      placeholder: 'sortable-placeholder',
      handle: '.list-column-item-header',
      connectWith: '.list-columns-sortable-items',
      distance: 2
    });

  }

  $(document).on('click', 'body.mywp-setting #setting-screen-setting-list-columns .active-toggle', function() {

    $(this).parent().parent().toggleClass('active');

  });

  $(document).on('click', 'body.mywp-setting #setting-screen-setting-list-columns .column-remove', function() {

    let $setting_column = $(this).parent().parent().parent().parent();

    $setting_column.slideUp( 'normal' , function() {

      $setting_column.remove();

    });

  });

  mywp_popup.open = function( html = false ) {

    $('body.mywp-setting #mywp-popup-content-inner').html( html );
    $('body.mywp-setting #mywp-popup').addClass('active');

  }

  mywp_popup.close = function() {

    $('body.mywp-setting #mywp-popup-content-inner').html('');
    $('body.mywp-setting #mywp-popup').removeClass('active');

  }

});
