<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAbstractSettingColumnsModule' ) ) :

abstract class MywpAbstractSettingColumnsModule extends MywpAbstractSettingModule {

  static protected $list_column_id = false;

  protected static function get_list_columns_model() {

    $called_text = sprintf( '%s::%s()' , get_called_class() , __FUNCTION__ );

    $mywp_controller = MywpController::get_controller( 'admin_regist_list_columns' );

    if( empty( $mywp_controller['model'] ) ) {

      MywpHelper::error_require_message( '$mywp_controller["model"]' , $called_text );

      return false;

    }

    return $mywp_controller['model'];

  }

  public static function mywp_ajax_manager() {

    $class = get_called_class();

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'add_column' ) , array( $class , 'ajax_add_column' ) );

  }

  public static function ajax_add_column() {}

  public static function mywp_current_admin_enqueue_scripts() {

    $scripts = array( 'jquery-ui-sortable' );

    foreach( $scripts as $script ) {

      wp_enqueue_script( $script );

    }

  }

  public static function mywp_current_setting_screen_content() {

    static::set_list_column_id();

    $called_text = sprintf( '%s::%s()' , get_called_class() , __FUNCTION__ );

    if( empty( static::$list_column_id ) ) {

      MywpHelper::error_require_message( 'static::$list_column_id' , $called_text );

      return false;

    }

    $list_link = static::get_list_link();

    if( empty( $list_link ) ) {

      MywpHelper::error_require_message( '$list_link' , $called_text );

    }

    $available_list_columns = static::get_available_list_columns();

    if( empty( $available_list_columns ) ) {

      MywpHelper::error_require_message( '$available_list_columns' , $called_text );

    }

    $current_setting_list_columns = static::get_current_setting_list_columns();

    if( empty( $current_setting_list_columns ) ) {

      MywpHelper::error_not_found_message( '$current_setting_list_columns' , $called_text );

    }

    ?>

    <div id="setting-screen-setting-list-columns">

      <h3 class="mywp-setting-screen-subtitle">

        <?php _e( 'Columns' , 'my-wp' ); ?>

        <a href="<?php echo esc_url( $list_link ); ?>" class="button button-secondary button-small" id="setting-screen-setting-list-columns-refresh-button">
          <span class="dashicons dashicons-update"></span>
          <?php _e( 'Refresh Columns' , 'my-wp' ); ?>
        </a>

      </h3>

      <div id="setting-list-columns">

        <?php if( empty( $available_list_columns ) or empty( $current_setting_list_columns ) ) : ?>

          <p class="mywp-error-message">

            <span class="dashicons dashicons-warning"></span>

            <?php printf( __( '%1$s: %2$s is not found. Please refresh the %2$s.' , 'my-wp' ) , __( 'Error' , 'my-wp' ) , __( 'Columns' , 'my-wp' ) ); ?>

          </p>

          <p>&nbsp;</p>

        <?php else : ?>

          <div id="setting-list-columns-available">

            <?php if( ! empty( $available_list_columns ) ) : ?>

              <select id="setting-list-columns-available-select-column">

                <option></option>

                <?php foreach( $available_list_columns as $group => $group_item ) : ?>

                  <optgroup label="<?php echo esc_attr( $group_item['title'] ); ?>">

                    <?php if( ! empty( $group_item['columns'] ) ) : ?>

                      <?php foreach( $group_item['columns'] as $key => $available_item ) : ?>

                        <option value="<?php echo esc_attr( $key ); ?>" class="available-item">[<?php echo esc_attr( $key ); ?>] <?php echo esc_attr( strip_shortcodes( $available_item['title'] ) ); ?></option>

                      <?php endforeach; ?>

                    <?php endif; ?>

                  </optgroup>

                <?php endforeach; ?>

              </select>

              <a href="javascript:void(0);" id="setting-list-columns-available-add-column" class="button button-secondary"><span class="dashicons dashicons-plus"></span> <?php _e( 'Add Column' , 'my-wp' ); ?></a>

              <span class="spinner"></span>

              <div id="setting-list-columns-available-columns">

                <?php foreach( $available_list_columns as $group => $group_column ) : ?>

                  <?php if( empty( $group_column['columns'] ) ) : ?>

                    <?php continue; ?>

                  <?php endif; ?>

                  <?php foreach( $group_column['columns'] as $key => $available_column ) : ?>

                    <div class="available-column column-key-<?php echo esc_attr( $key ); ?>">

                      <input type="text" class="id" value="<?php echo esc_attr( $key ); ?>" />

                    </div>

                  <?php endforeach; ?>

                <?php endforeach; ?>

              </div>

            <?php endif; ?>

          </div>

          <p class="mywp-description">
            <span class="dashicons dashicons-lightbulb"></span>
            <?php _e( 'All columns place behind "title or name" and "checkbox" when show on mobile device.' , 'my-wp' ); ?>
          </p>

          <div id="setting-list-columns-setting-columns">

            <div id="setting-list-columns-setting-columns-items" class="list-columns-sortable-items">

              <?php if( ! empty( $current_setting_list_columns ) ) : ?>

                <?php foreach( $current_setting_list_columns as $column_id => $column ) : ?>

                  <?php static::print_item( $column , $column_id ); ?>

                <?php endforeach; ?>

              <?php endif; ?>

            </div>

          </div>

        <?php endif; ?>

      </div>

    </div>

    <?php

  }

  protected static function set_list_column_id() {}

  protected static function get_list_link() {}

  protected static function get_core_list_columns() {}

  protected static function get_default_list_columns() {

    $called_text = sprintf( '%s::%s()' , get_called_class() , __FUNCTION__ );

    if( empty( static::$list_column_id ) ) {

      MywpHelper::error_require_message( 'static::$list_column_id' , $called_text );

      return false;

    }

    static::$list_column_id = strip_tags( static::$list_column_id );

    $list_columns_model = self::get_list_columns_model();

    if( empty( $list_columns_model ) ) {

      MywpHelper::error_require_message( '$list_columns_model' , $called_text );

      return false;

    }

    $option = $list_columns_model->get_option();

    if( empty( $option['regist_columns'][ static::$list_column_id ] ) ) {

      return false;

    }

    $default_list_columns = $option['regist_columns'][ static::$list_column_id ];

    return $default_list_columns;

  }

  protected static function get_available_list_columns() {

    $called_text = sprintf( '%s::%s()' , get_called_class() , __FUNCTION__ );

    $default_list_columns = static::get_default_list_columns();

    if( empty( $default_list_columns ) ) {

      MywpHelper::error_require_message( '$default_list_columns' , $called_text );

      return false;

    }

    $default_column_args = array(
      'id' => '',
      'type' => '',
      'sort' => false,
      'orderby' => '',
      'default_title' => '',
      'title' => '',
      'default_width' => '',
      'width' => '',
      'is_need_automatic_output' => false,
    );

    $available_list_columns = array(
      'core' => array(
        'title' => __( 'Core' , 'my-wp' ),
        'columns' => static::get_core_list_columns(),
      ),
    );

    $class = get_called_class();

    add_filter( "mywp_setting_{$class::$id}_get_available_list_columns_{$class::$list_column_id}" , array( $class , 'current_available_list_columns' ) , 9 );

    $available_list_columns = apply_filters( "mywp_setting_{$class::$id}_get_available_list_columns_{$class::$list_column_id}"  , $available_list_columns );
    $available_list_columns = apply_filters( "mywp_setting_{$class::$id}_get_available_list_columns" , $available_list_columns , static::$list_column_id );

    if( ! empty( $available_list_columns ) ) {

      foreach( $available_list_columns as $group => $available_columns ) {

        if( ! is_array( $available_columns ) or empty( $available_columns['columns'] ) ) {

          continue;

        }

        foreach( $available_columns['columns'] as $available_list_column_id => $available_list_column ) {

          $available_list_columns[ $group ]['columns'][ $available_list_column_id ] = wp_parse_args( $available_list_column , $default_column_args );

          if( ! empty( $available_list_column['title'] ) ) {

            $available_list_columns[ $group ]['columns'][ $available_list_column_id ]['default_title'] = $available_list_column['title'];

          }

          if( ! empty( $available_list_column['width'] ) ) {

            $available_list_columns[ $group ]['columns'][ $available_list_column_id ]['default_width'] = $available_list_column['width'];

          }

        }

      }

    }

    return $available_list_columns;

  }

  public static function current_available_list_columns( $available_list_columns ) {

    return $available_list_columns;

  }

  protected static function get_current_setting_list_columns() {

    $available_list_columns = static::get_available_list_columns();

    if( empty( $available_list_columns ) ) {

      return false;

    }

    $setting_data = static::get_setting_data();

    $current_setting_list_columns = array();

    if( ! empty( $setting_data['list_columns'] ) ) {

      foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

        foreach( $available_list_columns as $group => $available_columns ) {

          if( empty( $available_columns['columns'] ) ) {

            continue;

          }

          foreach( $available_columns['columns'] as $available_list_column_id => $available_list_column ) {

            if( $available_list_column_id !== $column_id ) {

              continue;

            }

            $column_data = $available_list_column;

            if( ! empty( $column_setting['title'] ) ) {

              $column_data['title'] = $column_setting['title'];

            } else {

              $column_data['title'] = '';

            }

            if( ! empty( $column_setting['sort'] ) ) {

              $column_data['sort'] = true;

            } else {

              $column_data['sort'] = false;

            }

            if( ! empty( $column_setting['width'] ) ) {

              $column_data['width'] = $column_setting['width'];

            } else {

              $column_data['width'] = '';

            }

            $current_setting_list_columns[ $column_id ] = $column_data;

          }

        }

      }

    } else {

      $default_list_columns = static::get_default_list_columns();

      if( empty( $default_list_columns['columns'] ) ) {

        return false;

      }

      foreach( $default_list_columns['columns'] as $column_id => $column_title ) {

        foreach( $available_list_columns as $group => $available_columns ) {

          if( empty( $available_columns['columns'] ) ) {

            continue;

          }

          foreach( $available_columns['columns'] as $available_list_column_id => $available_list_column ) {

            if( $available_list_column_id !== $column_id ) {

              continue;

            }

            $column_data = $available_list_column;

            if( ! empty( $default_list_columns['sortables'][ $column_id ] ) ) {

              $column_data['sort'] = true;

              $column_data['orderby'] = $default_list_columns['sortables'][ $column_id ];

            } else {

              $column_data['sort'] = false;

            }

            $current_setting_list_columns[ $column_id ] = $column_data;

          }

        }

      }

    }

    $class = get_called_class();

    $current_setting_list_columns = apply_filters( "mywp_setting_{$class::$id}_get_current_setting_list_columns_{$class::$list_column_id}"  , $current_setting_list_columns );
    $current_setting_list_columns = apply_filters( "mywp_setting_{$class::$id}_get_current_setting_list_columns" , $current_setting_list_columns , static::$list_column_id );

    return $current_setting_list_columns;

  }

  protected static function print_item( $column = false , $column_id = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , get_called_class() , __FUNCTION__ , '$column' , '$column_id' );

    if( empty( $column['type'] ) ) {

      MywpHelper::error_not_found_message( '$column' , $called_text );

      return false;

    }

    $class = get_called_class();

    ?>

    <div class="list-columns-item list-columns-sortable-item list-column-type-<?php echo esc_attr( $column['type'] ); ?>">

      <?php static::print_item_header( $column , $column_id ); ?>

      <?php static::print_item_content( $column , $column_id ); ?>

      <?php do_action( "mywp_setting_{$class::$id}_print_item" , $column , $column_id , $class::$list_column_id ); ?>
      <?php do_action( "mywp_setting_{$class::$id}_print_item_{$class::$list_column_id}" , $column , $column_id ); ?>

    </div>

    <?php

  }

  protected static function print_item_header( $column = false , $column_id = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , get_called_class() , __FUNCTION__ , '$column' , '$column_id' );

    if( empty( $column['type'] ) ) {

      MywpHelper::error_not_found_message( '$column' , $called_text );

      return false;

    }

    ?>

    <div class="list-column-item-header">

      <a href="javascript:void(0);" class="active-toggle">&nbsp;</a>

      <div class="title-wrap">

        <span class="title">

          <?php if( ! empty( $column['title'] ) ) : ?>

            <?php echo strip_shortcodes( $column['title'] ); ?>

          <?php endif; ?>

        </span>

        <span class="default-title">

          <?php if( ! empty( $column['default_title'] ) ) : ?>

            (<?php echo esc_attr( $column['default_title'] ); ?>)

          <?php endif; ?>

        </span>

        <?php if( $column['type'] === 'deprecated' ) : ?>

          <p class="mywp-desc-caution">
            <span class="dashicons dashicons-warning"></span>
            <?php _e( 'Deprecated' , 'my-wp' ); ?>
          </p>

        <?php endif; ?>

        <span class="column-id">

          <?php echo esc_html( $column_id ); ?>

        </span>

      </div>

    </div>

    <?php

  }

  protected static function print_item_content( $column = false , $column_id = false ) {

    $called_text = sprintf( '%s::%s( %s , %s )' , get_called_class() , __FUNCTION__ , '$column' , '$column_id' );

    if( empty( $column['type'] ) ) {

      MywpHelper::error_not_found_message( '$column' , $called_text );

      return false;

    }

    $is_sortable = false;

    $disable_sortable_class = 'sortable-disabled';

    if( ! empty( $column['orderby'] ) or ! empty( $column['sort']) ) {

      $is_sortable = true;

      $disable_sortable_class = '';

    }

    $class = get_called_class();

    ?>

    <div class="list-column-item-content item-type-<?php echo esc_attr( $column['type'] ); ?>">

      <div class="content-wrap">

        <div class="content-hidden">

          <input type="hidden" class="list-column-item-id" value="<?php echo esc_attr( $column['id'] ); ?>" />

          <?php if( is_string( $column['orderby'] ) ) : ?>

            <input type="text" name="mywp[data][list_columns][<?php echo esc_attr( $column['id'] ); ?>][orderby]" class="list-column-orderby" value="<?php echo esc_attr( $column['orderby'] ); ?>" />

          <?php elseif( isset( $column['orderby'][0] ) ) : ?>

            <input type="text" name="mywp[data][list_columns][<?php echo esc_attr( $column['id'] ); ?>][orderby]" class="list-column-orderby" value="<?php echo esc_attr( $column['orderby'][0] ); ?>" />

          <?php endif; ?>

        </div>

        <?php if( ! empty( $column['is_need_automatic_output'] ) ) : ?>

          <p class="mywp-description"><span class="dashicons dashicons-lightbulb"></span> <?php _e( 'This column value output is need Automatic output of column contents enabled.' , 'my-wp' ); ?></p>

        <?php endif; ?>

        <div class="content-fields">

          <table class="form-table">
            <tbody>
              <tr>
                <th><?php _e( 'Column ID' ); ?></th>
                <td>
                  <?php echo $column['id']; ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Title' ); ?></th>
                <td>
                  <input type="text" name="mywp[data][list_columns][<?php echo esc_attr( $column['id'] ); ?>][title]" class="list-column-item-change-title large-text" value="<?php echo esc_attr( $column['title'] ); ?>" placeholder="<?php echo esc_attr( $column['default_title'] ); ?>" />
                </td>
              </tr>
              <tr class="<?php echo esc_attr( $disable_sortable_class ); ?>">
                <th>
                  <?php _e( 'Sorting' , 'my-wp' ); ?>
                </th>
                <td>
                  <label>
                    <input type="checkbox" name="mywp[data][list_columns][<?php echo esc_attr( $column['id'] ); ?>][sort]" class="list-column-item-sort" value="1" <?php checked( $column['sort'] , true ); ?> <?php disabled( $is_sortable , false ); ?> />
                    <?php _e( 'Active' , 'my-wp' ); ?>
                  </label>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Width' ); ?></th>
                <td>
                  <input type="text" name="mywp[data][list_columns][<?php echo esc_attr( $column['id'] ); ?>][width]" class="list-column-item-width regular-text" value="<?php echo esc_attr( $column['width'] ); ?>" placeholder="<?php echo esc_attr( '1em / 10% / auto' ); ?>" />
                  <span class="description"><?php _e( 'Default' ); ?>: <code><?php echo $column['default_width']; ?></code></span>
                </td>
              </tr>
            </tbody>
          </table>

          <?php do_action( "mywp_setting_{$class::$id}_print_item_content_after" , $column , $column_id , $class::$list_column_id ); ?>
          <?php do_action( "mywp_setting_{$class::$id}_print_item_content_after_{$class::$list_column_id}" , $column , $column_id ); ?>

          <div class="clear"></div>

          <a href="javascript:void(0);" class="column-remove button button-secondary button-caution"><span class="dashicons dashicons-no-alt"></span> <?php _e( 'Remove' ); ?></a>

        </div>

      </div>

    </div>

    <?php

  }

  protected static function delete_current_list_columns() {

    $called_text = sprintf( '%s::%s()' , get_called_class() , __FUNCTION__ , '$validated_data' );

    if( empty( static::$list_column_id ) ) {

      MywpHelper::error_require_message( 'static::$list_column_id' , $called_text );

      return false;

    }

    $list_columns_model = self::get_list_columns_model();

    if( empty( $list_columns_model ) ) {

      MywpHelper::error_require_message( '$list_columns_model' , $called_text );

      return false;

    }

    $option = $list_columns_model->get_option();

    if( isset( $option['regist_columns'][ static::$list_column_id ] ) ) {

      unset( $option['regist_columns'][ static::$list_column_id ] );

    }

    $list_columns_model->update_data( $option );

  }

}

endif;
