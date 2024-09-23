<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminTerms' ) ) :

final class MywpSettingScreenAdminTerms extends MywpAbstractSettingColumnsModule {

  static protected $id = 'admin_terms';

  static protected $priority = 70;

  static private $menu = 'admin';

  static private $taxonomy = '';

  static protected $list_column_id = false;

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Terms' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'admin_terms',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-terms/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_load_setting_screen() {

    $current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( ! empty( $current_setting_taxonomy_name ) ) {

      self::$taxonomy = $current_setting_taxonomy_name;

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$taxonomy ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$taxonomy;

    return $option_key;

  }

  public static function ajax_add_column() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'add_column' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['add_column_id'] ) ) {

      return false;

    }

    $add_column_id = strip_tags( $_POST['add_column_id'] );

    if( empty( $_POST['setting_taxonomy'] ) ) {

      return false;

    }

    MywpSettingTaxonomy::set_current_taxonomy_id( $_POST['setting_taxonomy'] );

    $current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();

    static::set_list_column_id();

    $available_list_columns = self::get_available_list_columns();

    if( empty( $available_list_columns ) ) {

      return false;

    }

    $found_column = false;

    foreach( $available_list_columns as $group => $list_columns ) {

      if( empty( $list_columns['columns'] ) ) {

        continue;

      }

      foreach( $list_columns['columns'] as $column_id => $list_column ) {

        if( $column_id === $add_column_id ) {

          $found_column = $list_column;

          break;

        }

      }

    }

    if( empty( $found_column ) ) {

      return false;

    }

    $result_html = '';

    ob_start();

    self::print_item( $found_column , $found_column['id'] );

    $result_html .= ob_get_contents();

    ob_end_clean();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function mywp_current_admin_print_footer_scripts() {

    $post_data_js_custom_values = array();

    $post_data_js_custom_values = apply_filters( 'mywp_setting_admin_terms_post_data_js_custom_values' , $post_data_js_custom_values );

    if( ! is_array( $post_data_js_custom_values ) ) {

      $post_data_js_custom_values = array();

    }

    ?>
    <script>
    jQuery(function( $ ) {

      const post_data_js_custom_values = JSON.parse( '<?php echo json_encode( $post_data_js_custom_values ); ?>' );

      $('#setting-screen-setting-list-columns #setting-list-columns-available-add-column').on('click', function() {

        let $available_column = $(this).parent();

        let $spinner = $available_column.find('.spinner');

        let select_column_key = $available_column.find('#setting-list-columns-available-select-column').val();

        if( select_column_key === null || select_column_key === '' ) {

          return false;

        }

        let $available_columnm_key = $available_column.find('.available-column.column-key-' + select_column_key);

        if( ! $available_columnm_key.length ) {

          return false;

        }

        let add_column_id = $available_columnm_key.find('.id').val();

        if( add_column_id === null || add_column_id === '' ) {

          return false;

        }

        let $already_column = false;

        $(document).find('#setting-screen-setting-list-columns #setting-list-columns-setting-columns-items .list-columns-item').each( function( index , el ) {

          let list_column_item_id = $(el).find('.list-column-item-id').val();

          if( list_column_item_id === add_column_id ) {

            $already_column = $(el);

            $(el).addClass('already');

            setTimeout( function() {

              $(el).removeClass('already');

            }, 2000);

            return false;

          }

        });

        if( $already_column !== false ) {

          alert( mywp_admin_setting.column_already_added );

          let scroll_position = $already_column.offset().top;

          scroll_position = ( scroll_position - 80 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

          return false;

        }

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ) ); ?>',
          setting_taxonomy: '<?php echo esc_js( MywpSettingTaxonomy::get_current_taxonomy_id() ); ?>',
          add_column_id: add_column_id
          <?php //do_action( 'mywp_setting_admin_terms_available_column_add_post_data_JS' ); ?>
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

        $spinner.css('visibility', 'visible');

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          if( xhr.data.result_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $html = $(xhr.data.result_html);

          $html.addClass('active');

          $(document).find('#setting-list-columns-setting-columns-items').append( $html );

          $(document).find('.list-columns-sortable-items').sortable({
            connectWith: '.list-columns-sortable-items'
          });

          let scroll_position = $(document).find('#setting-list-columns-setting-columns-items .list-columns-item').last().offset().top;

          scroll_position = ( scroll_position - 40 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

    });
    </script>
    <?php

  }

  public static function mywp_current_setting_screen_header() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();
    $current_setting_taxonomy = MywpSettingTaxonomy::get_current_taxonomy();

    if( empty( $current_setting_taxonomy ) ) {

      MywpHelper::error_not_found_message( '$current_setting_taxonomy' , $called_text );

      return false;

    }

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-select-taxonomy.php' );

  }

  public static function mywp_current_setting_screen_advance_content() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $setting_data = self::get_setting_data();

    $current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();
    $current_setting_taxonomy = MywpSettingTaxonomy::get_current_taxonomy();

    if( empty( $current_setting_taxonomy ) ) {

      MywpHelper::error_not_found_message( '$current_setting_taxonomy' , $called_text );

      return false;

    }

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Other' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Number of items per page:' ); ?></th>
          <td>
            <label>
              <input type="number" name="mywp[data][per_page_num]" class="per_page_num small-text" value="<?php echo esc_attr( $setting_data['per_page_num'] ); ?>" placeholder="20" />
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo $current_setting_taxonomy->labels->add_new_item; ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_add_new]" class="hide_add_new" value="1" <?php checked( $setting_data['hide_add_new'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo $current_setting_taxonomy->labels->search_items; ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_search_box]" class="hide_search_box" value="1" <?php checked( $setting_data['hide_search_box'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  protected static function set_list_column_id() {

    self::$list_column_id = MywpSettingTaxonomy::get_current_taxonomy_id();

  }

  protected static function get_list_link() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $current_setting_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( empty( $current_setting_taxonomy_id ) ) {

      MywpHelper::error_require_message( '$current_setting_taxonomy_id' , $called_text );

      return false;

    }

    $list_link = MywpSettingTaxonomy::get_list_link( $current_setting_taxonomy_id );

    return $list_link;

  }

  protected static function get_core_list_columns() {

    $core_list_columns = array(
      'cb' => array(
        'id' => 'cb',
        'type' => 'core',
        'title' => '<input type="checkbox" />',
        'width' => '2.2em',
      ),
      'name' => array(
        'id' => 'name',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'name',
        'title' => _x( 'Name' , 'term name' ),
      ),
      'description' => array(
        'id' => 'description',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'description',
        'title' => __( 'Description' ),
      ),
      'slug' => array(
        'id' => 'slug',
        'type' => 'core',
        'orderby' => 'slug',
        'title' => __( 'Slug' ),
        'width' => '25%',
      ),
      'posts' => array(
        'id' => 'posts',
        'type' => 'core',
        'orderby' => 'count',
        'title' => _x( 'Count' , 'Number/count of items' ),
        'width' => '74px',
      ),
    );

    return $core_list_columns;

  }

  public static function current_available_list_columns( $available_list_columns ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$available_list_columns' );

    if( empty( $available_list_columns['core']['columns'] ) ) {

      MywpHelper::error_require_message( '$available_list_columns["core"]["columns"]' , $called_text );

      return false;

    }

    $current_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( empty( $current_taxonomy_id ) ) {

      MywpHelper::error_require_message( '$current_taxonomy_id' , $called_text );

      return false;

    }

    $available_list_columns['other'] = array(
      'title' => __( 'Other' , 'my-wp' ),
      'columns' => array(
        'mywp_column_id' => array(
          'id' => 'mywp_column_id',
          'type' => 'other',
          'orderby' => 'ID',
          'title' => __( 'ID' , 'my-wp' ),
        ),
        'mywp_column_parent' => array(
          'id' => 'mywp_column_parent',
          'type' => 'other',
          'orderby' => 'parent',
          'title' => __( 'Parent' ),
          'width' => '10%',
        ),
      ),
    );

    $core_list_columns = self::get_core_list_columns();

    $default_list_columns = self::get_default_list_columns();

    foreach( $default_list_columns['columns'] as $column_id => $column_title ) {

      if( isset( $core_list_columns[ $column_id ] ) ) {

        continue;

      }

      if( isset( $taxonomies[ $column_id ] ) ) {

        continue;

      }

      $available_list_column = array(
        'id' => $column_id,
        'type' => 'other',
        'title' => $column_title,
      );

      if( isset( $default_list_columns['sortables'][ $column_id ] ) ) {

        $available_list_column['sort'] = true;

      }

      $available_list_columns['other']['columns'][ $column_id ] = $available_list_column;

    }

    /*
    $available_list_columns['custom_fields'] = array(
      'title' => __( 'Custom Fields' ),
      'columns' => array(),
    );

    $all_custom_fields = MywpPostType::get_post_type_posts_all_custom_fields( $current_setting_taxonomy_id );

    if( ! empty( $all_custom_fields ) ) {

      foreach( $all_custom_fields as $custom_field_name => $v ) {

        $available_list_column = array(
          'id' => $custom_field_name,
          'type' => 'custom_fields',
          'sort' => true,
          'orderby' => $custom_field_name,
          'title' => $custom_field_name,
          'is_need_automatic_output' => true,
        );

        $available_list_columns['custom_fields']['columns'][ $custom_field_name ] = $available_list_column;

      }

    }
    */

    $available_list_columns['deprecated'] = array(
      'title' => __( 'Deprecated' , 'my-wp' ),
      'columns' => array(
        'id' => array(
          'id' => 'id',
          'type' => 'deprecated',
          'orderby' => 'ID',
          'title' => __( 'ID' , 'my-wp' ),
        ),
        'parent' => array(
          'id' => 'parent',
          'type' => 'deprecated',
          'orderby' => 'parent',
          'title' => __( 'Parent' ),
          'width' => '10%',
        ),
      ),
    );

    return $available_list_columns;

  }

  public static function mywp_current_setting_screen_remove_form() {

    $current_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( empty( $current_taxonomy_id ) ) {

      return false;

    }

    ?>

    <input type="hidden" name="mywp[data][taxonomy]" value="<?php echo esc_attr( $current_taxonomy_id ); ?>" />

    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['taxonomy'] ) ) {

      $new_formatted_data['taxonomy'] = strip_tags( $formatted_data['taxonomy'] );

    }

    $list_column_default = MywpControllerModuleAdminTerms::get_list_column_default();

    if( ! empty( $formatted_data['list_columns'] ) ) {

      foreach( $formatted_data['list_columns'] as $list_column_id => $list_column_setting ) {

        $list_column_id = strip_tags( $list_column_id );

        $new_list_column_setting = wp_parse_args( array( 'id' => $list_column_id ) , $list_column_default );

        if( ! empty( $list_column_setting['sort'] ) ) {

          $new_list_column_setting['sort'] = true;

        }

        if( ! empty( $list_column_setting['title'] ) ) {

          $new_list_column_setting['title'] = wp_unslash( $list_column_setting['title'] );

        }

        if( ! empty( $list_column_setting['orderby'] ) ) {

          $new_list_column_setting['orderby'] = wp_unslash( $list_column_setting['orderby'] );

        }

        if( ! empty( $list_column_setting['width'] ) ) {

          $new_list_column_setting['width'] = strip_tags( $list_column_setting['width'] );

        }

        $new_formatted_data['list_columns'][ $list_column_id ] = $new_list_column_setting;

      }

    }

    if( ! empty( $formatted_data['per_page_num'] ) ) {

      $new_formatted_data['per_page_num'] = (int) $formatted_data['per_page_num'];

    }

    if( ! empty( $formatted_data['hide_add_new'] ) ) {

      $new_formatted_data['hide_add_new'] = true;

    }

    if( ! empty( $formatted_data['hide_search_box'] ) ) {

      $new_formatted_data['hide_search_box'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_post_data_format_remove( $formatted_data ) {

    if( ! empty( $formatted_data['taxonomy'] ) ) {

      $formatted_data['taxonomy'] = strip_tags( $formatted_data['taxonomy'] );

    }

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_validate_update( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['taxonomy'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'taxonomy' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_post_data_validate_remove( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['taxonomy'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'taxonomy' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_before_post_data_action_update( $validated_data ) {

    if( ! empty( $validated_data['taxonomy'] ) ) {

      self::$taxonomy = $validated_data['taxonomy'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    $called_text = sprintf( '%s::%s( %s )' , get_called_class() , __FUNCTION__ , '$validated_data' );

    if( ! empty( $validated_data['taxonomy'] ) ) {

      self::$taxonomy = $validated_data['taxonomy'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

      MywpSettingTaxonomy::set_current_taxonomy_id( self::$taxonomy );

      $current_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();

      static::set_list_column_id();

      self::delete_current_list_columns();

    }

  }

}

MywpSettingScreenAdminTerms::init();

endif;
