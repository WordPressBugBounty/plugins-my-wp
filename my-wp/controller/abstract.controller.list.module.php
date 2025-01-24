<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAbstractControllerListModule' ) ) :

abstract class MywpAbstractControllerListModule extends MywpControllerAbstractModule {

  public static function get_list_column_default() {

    $list_column_default = array(
      'id' => '',
      'sort' => '',
      'orderby' => '',
      'title' => '',
      'width' => '',
    );

    return $list_column_default;

  }

  public static function get_custom_search_filter_field_default() {

    $custom_search_filter_field_default = array(
      'id' => '',
      'title' => '',
      'type' => '',
      'multiple' => false,
      'choices' => array(),
      'placeholder' => '',
      'description' => '',
      'html' => '',
      'filtered' => false,
      'input_name' => '',
      'input_value' => '',
    );

    return $custom_search_filter_field_default;

  }

  protected static function custom_search_filter() {

    $setting_data = static::get_setting_data();

    if( empty( $setting_data['custom_search_filter'] ) ) {

      return false;

    }

    $class = get_called_class();

    if( ! static::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    add_action( 'admin_footer' , array( $class , 'print_custom_search_filter' ) );

    static::custom_search_filter_do();

    static::after_do_function( __FUNCTION__ );

  }

  protected static function custom_search_filter_do() {}

  public static function custom_search_filter_query( $query ) {

    $nonce_key = 'mywp_controller_' . static::$id . '_custom_search_filter';

    if( empty( $_REQUEST[ $nonce_key ] ) ) {

      return false;

    }

    check_admin_referer( $nonce_key , $nonce_key );

    if( empty( $_REQUEST['mywp_controller_' . static::$id ]['custom_search_filter_request'] ) ) {

      return false;

    }

    $custom_search_filter_requests = $_REQUEST['mywp_controller_' . static::$id ]['custom_search_filter_request'];

    static::custom_search_filter_query_do( $query , $custom_search_filter_requests );

  }

  protected static function custom_search_filter_query_do( $query , $custom_search_filter_requests ) {}

  public static function print_custom_search_filter() {

    $custom_search_filter_fields = static::get_custom_search_filter_fields();

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    if( ! is_array( $custom_search_filter_fields ) ) {

      return false;

    }

    foreach( $custom_search_filter_fields as $custom_search_filter_field_id => $custom_search_filter_field ) {

      if( empty( $custom_search_filter_field ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      if( ! is_array( $custom_search_filter_field ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      if( empty( $custom_search_filter_field['type'] ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      if( is_array( $custom_search_filter_field['type'] ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      if( in_array( $custom_search_filter_field['type'] , array( 'radio' ) , true ) ) {

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['multiple'] = false;

      }

      if( in_array( $custom_search_filter_field['type'] , array( 'checkbox' , 'date' ) , true ) ) {

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['multiple'] = true;

      }

      if( empty( $custom_search_filter_field['placeholder'] ) ) {

        $placeholder = '';

        if( ! empty( $custom_search_filter_field['title'] ) ) {

          $placeholder = strip_tags( $custom_search_filter_field['title'] );

        }

        if( $custom_search_filter_field['type'] === 'number' ) {

          $placeholder = '0';

        } elseif( $custom_search_filter_field['type'] === 'date' ) {

          $placeholder = '0000-00-00';

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['placeholder'] = $placeholder;

      }

      $input_name = sprintf( 'mywp_controller_%s[custom_search_filter_request][%s]' , static::$id , $custom_search_filter_field_id );

      /*
      if( ! empty( $custom_search_filter_fields[ $custom_search_filter_field_id ]['multiple'] ) ) {

        if( ! in_array( $custom_search_filter_field['type'] , array( 'date' ) , true ) ) {

          $input_name .= '[]';

        }

      }
      */

      $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_name'] = $input_name;

      $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = '';

      if( ! empty( $custom_search_filter_fields[ $custom_search_filter_field_id ]['multiple'] ) ) {

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = array();

      }

      if( $custom_search_filter_field['type'] === 'date' ) {

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = array( 'from' => '' , 'to' => '' );

      }

    }

    $custom_search_filter_requests = array();

    if( ! empty( $_REQUEST['mywp_controller_' . static::$id ]['custom_search_filter_request'] ) ) {

      $custom_search_filter_requests = $_REQUEST['mywp_controller_' . static::$id]['custom_search_filter_request'];

    }

    foreach( $custom_search_filter_fields as $custom_search_filter_field_id => $custom_search_filter_field ) {

      if( ! isset( $custom_search_filter_requests[ $custom_search_filter_field_id ] ) ) {

        continue;

      }

      $input_value = $custom_search_filter_requests[ $custom_search_filter_field_id ];

      if( $custom_search_filter_field['type'] === 'text' ) {

        $input_value = MywpHelper::sanitize_text( $input_value );

        if( empty( $input_value ) ) {

          continue;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $input_value;

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

      } elseif( $custom_search_filter_field['type'] === 'number' ) {

        $input_value = MywpHelper::sanitize_number( $input_value );

        if( $input_value === false ) {

          continue;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $input_value;

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

      } elseif( $custom_search_filter_field['type'] === 'select' ) {

        if( ! empty( $custom_search_filter_field['multiple'] ) ) {

          if( ! is_array( $input_value ) ) {

            continue;

          }

          $new_input_value = array();

          foreach( $input_value as $key => $value ) {

            $value = MywpHelper::sanitize_text( $value );

            if( empty( $value ) ) {

              continue;

            }

            if( ! isset( $custom_search_filter_field['choices'][ $value ] ) ) {

              continue;

            }

            $new_input_value[] = $value;

          }

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $new_input_value;

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

        } else {

          $input_value = MywpHelper::sanitize_text( $input_value );

          if( empty( $input_value ) ) {

            continue;

          }

          if( ! isset( $custom_search_filter_field['choices'][ $input_value ] ) ) {

            continue;

          }

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $input_value;

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

        }

      } elseif( $custom_search_filter_field['type'] === 'radio' ) {

        $input_value = MywpHelper::sanitize_text( $input_value );

        if( empty( $input_value ) ) {

          continue;

        }

        if( ! isset( $custom_search_filter_field['choices'][ $input_value ] ) ) {

          continue;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $input_value;

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

      } elseif( $custom_search_filter_field['type'] === 'checkbox' ) {

        if( ! is_array( $input_value ) ) {

          continue;

        }

        $new_input_value = array();

        foreach( $input_value as $key => $value ) {

          $value = MywpHelper::sanitize_text( $value );

          if( empty( $value ) ) {

            continue;

          }

          if( ! isset( $custom_search_filter_field['choices'][ $value ] ) ) {

            continue;

          }

          $new_input_value[] = $value;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $new_input_value;

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

      } elseif( $custom_search_filter_field['type'] === 'date' ) {

        foreach( $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] as $k => $v ) {

          if( empty( $custom_search_filter_requests[ $custom_search_filter_field_id ][ $k ] ) ) {

            continue;

          }

          $date = MywpHelper::sanitize_date( $custom_search_filter_requests[ $custom_search_filter_field_id ][ $k ] );

          if( empty( $date ) ) {

            continue;

          }

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'][ $k ] = $date;

          $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

        }

      } else {

        if( empty( $custom_search_filter_requests[ $custom_search_filter_field_id ] ) ) {

          continue;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = $custom_search_filter_requests[ $custom_search_filter_field_id ];

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = true;

      }

    }

    $custom_search_filter_fields = static::get_custom_search_filter_fields_after( $custom_search_filter_fields , $custom_search_filter_requests );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    if( ! is_array( $custom_search_filter_fields ) ) {

      return false;

    }

    $custom_search_filter_field_default = static::get_custom_search_filter_field_default();

    foreach( $custom_search_filter_fields as $custom_search_filter_field_id => $custom_search_filter_field ) {

      if( empty( $custom_search_filter_field ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      if( ! is_array( $custom_search_filter_field ) ) {

        unset( $custom_search_filter_fields[ $custom_search_filter_field_id ] );

        continue;

      }

      $custom_search_filter_fields[ $custom_search_filter_field_id ] = wp_parse_args( $custom_search_filter_field , $custom_search_filter_field_default );

    }

    $custom_search_filter_add_class = '';

    if( ! empty( $custom_search_filter_requests ) ) {

      $custom_search_filter_add_class = ' active ';

    }

    $class = get_called_class();

    ?>

    <style>
    #mywp-custom-search-filter {
      background: #fff;
      display: none;
      margin: 22px auto 0 auto;
      border: 1px solid #c3c4c7;
    }
    #mywp-custom-search-filter.show {
      display: block;
    }
    #mywp-custom-search-filter .title-toggle {
      margin: 0 auto;
      padding: 0;
    }
    #mywp-custom-search-filter .title-toggle a {
      display: flex;
      text-decoration: none;
      padding: 6px 0;
      align-items: center;
      color: #1d2327;
    }
    #mywp-custom-search-filter .title-toggle a:hover {
      background: #f0f0f1;
    }
    #mywp-custom-search-filter .title-toggle a .icon {
      text-align: right;
      width: 26px;
    }
    #mywp-custom-search-filter .title-toggle a .icon::before {
      font-family: dashicons;
      display: inline-block;
      line-height: 1;
      font-weight: 400;
      font-style: normal;
      speak: never;
      text-decoration: inherit;
      text-transform: none;
      text-rendering: auto;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      width: 20px;
      height: 20px;
      font-size: 20px;
      vertical-align: top;
      text-align: center;
      content: "\f140";
    }
    #mywp-custom-search-filter.active .title-toggle a .icon::before {
      content: "\f142";
    }
    #mywp-custom-search-filter .title-toggle a .title {
      flex: 1;
      text-align: left;
      font-size: 15px;
    }
    #mywp-custom-search-filter .filter-form {
      display: none;
    }
    #mywp-custom-search-filter.active .filter-form {
      display: block;
      padding: 16px 24px;
    }
    #mywp-custom-search-filter .filter-form-fields {
      margin: 0 auto 14px auto;
      border-top: 1px solid #eee;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field {
      display: flex;
      align-items: start;
      padding: 12px 4px;
      border-bottom: 1px solid #eee;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field.filtered {
      background: #F2F9FF;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-header {
      width: 18%;
      text-align: right;
      padding: 4px 10px 0 0;
      font-weight: 600;
      font-size: 14px;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content {
      flex: 1;
      text-align: left;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > label {
      display: inline-block;
      margin: 0 16px 0 auto;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > select.multiple {
      height: 140px;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > ul {
      margin: 0 auto;
      padding: 4px 0 0 0;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > ul > li {
      display: inline-block;
      margin: 0 16px 0 auto;
    }
    #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > .clear-radio {
      margin: 4px auto 0 auto;
    }
    #mywp-custom-search-filter .filter-form-submit {
      text-align: center;
      margin: 0 auto 6px auto;
    }

    @media screen and (max-width: 782px) {

      #mywp-custom-search-filter .filter-form-fields .filter-form-field {
        display: block;
        padding: 8px 0;
      }
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-header {
        width: auto;
        text-align: left;
        padding: 0;
        font-size: 16px;
        margin: 0 auto 4px auto;
      }
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content {
        display: block;
      }
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > label,
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content > ul > li {
        display: block;
        margin: 0 auto 8px auto;
      }
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content input[type=text],
      #mywp-custom-search-filter .filter-form-fields .filter-form-field .form-field-content input[type=number] {
        max-width: 100%;
        width :100%;
        display: block;
      }

    }
    </style>
    <script>
    jQuery(function( $ ) {

      let $mywp_custom_search_filter = $('#mywp-custom-search-filter');

      if( $mywp_custom_search_filter.length < 1 ) {

        return false;

      }

      $mywp_custom_search_filter.insertBefore( $('.wp-header-end') ).addClass('show');

      $mywp_custom_search_filter.find('.title-toggle a').on('click', function() {

        if( $mywp_custom_search_filter.hasClass('active') ) {

          $mywp_custom_search_filter.removeClass('active');

        } else {

          $mywp_custom_search_filter.addClass('active');

        }

      });

      $mywp_custom_search_filter.find('.clear-radio .button').on('click', function() {

        let $ul = $(this).parent().parent().find('ul');

        $ul.find('li').each( function( index , el ) {

          let $li = $(el);

          $li.find('input[type=radio]').prop('checked', false);

        });

      });

      $mywp_custom_search_filter.find('form').on('submit', function() {

        $mywp_custom_search_filter.find('input[type=text], input[type=number]').each( function( index , el ) {

          if( $(el).val() === '' ) {

            $(el).prop('name', '');

          }

        });

        $mywp_custom_search_filter.find('input[type=checkbox]').each( function( index , el ) {

          if( $(el).prop('checked') === false ) {

            $(el).prop('name', '');

          }

        });

        $mywp_custom_search_filter.find('select').each( function( index , el ) {

          if( $(el).find('option:selected').val() === '' ) {

            $(el).prop('name', '');

          }

        });

        return true;

      });

    });
    </script>
    <div id="mywp-custom-search-filter" class="<?php echo $custom_search_filter_add_class; ?>">

      <p class="title-toggle">
        <a href="javascript:void(0);">
          <span class="icon"></span>
          <span class="title"><?php _e( 'Search Filter' , 'my-wp' ); ?></span>
        </a>
      </p>

      <div class="filter-form">

        <form action="" method="get">

          <?php wp_nonce_field( 'mywp_controller_' . static::$id . '_custom_search_filter' , 'mywp_controller_' . static::$id . '_custom_search_filter' ); ?>

          <?php do_action( 'mywp_controller_' . static::$id . '_custom_search_filter_form' ); ?>

          <div class="filter-form-fields">

            <?php foreach( $custom_search_filter_fields as $custom_search_filter_field_id => $custom_search_filter_field ) : ?>

              <?php if( empty( $custom_search_filter_field ) ) : ?>

                <?php continue; ?>

              <?php endif; ?>

              <?php if( ! is_array( $custom_search_filter_field ) ) : ?>

                <?php continue; ?>

              <?php endif; ?>

              <?php if( is_array( $custom_search_filter_field['type'] ) ) : ?>

                <?php continue; ?>

              <?php endif; ?>

              <?php $filter_form_field_add_class = ''; ?>

              <?php if( ! empty( $custom_search_filter_field['filtered'] ) ) : ?>

                <?php $filter_form_field_add_class .= ' filtered '; ?>

              <?php endif; ?>

              <div class="filter-form-field filter-form-field-<?php echo esc_attr( $custom_search_filter_field_id ); ?> <?php echo esc_attr( $filter_form_field_add_class ); ?>">

                <div class="form-field-header">
                  <?php echo esc_html( $custom_search_filter_field['title'] ); ?>
                </div>

                <div class="form-field-content">

                  <?php if( $custom_search_filter_field['type'] === 'text' ) : ?>

                    <input type="text" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>" value="<?php echo esc_attr( $custom_search_filter_field['input_value'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder'] ); ?>" class="regular-text" />

                  <?php elseif( $custom_search_filter_field['type'] === 'number' ) : ?>

                    <input type="number" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>" value="<?php echo esc_attr( $custom_search_filter_field['input_value'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder'] ); ?>" />

                  <?php elseif( $custom_search_filter_field['type'] === 'select' ) : ?>

                    <?php $select_multiple = ''; ?>

                    <?php $input_name = $custom_search_filter_field['input_name']; ?>

                    <?php if( ! empty( $custom_search_filter_field['multiple'] ) ) : ?>

                      <?php $select_multiple = ' multiple '; ?>

                      <?php $input_name .= '[]'; ?>

                    <?php endif; ?>

                    <select name="<?php echo esc_attr( $input_name ); ?>" class="<?php echo esc_attr( $select_multiple ); ?>" <?php echo esc_attr( $select_multiple ); ?>>

                      <option value=""></option>

                      <?php if( ! empty( $custom_search_filter_field['choices'] ) ) : ?>

                        <?php foreach( $custom_search_filter_field['choices'] as $key => $val ) : ?>

                          <?php if( is_array( $key ) or is_array( $val ) ) : ?>

                            <?php continue; ?>

                          <?php endif; ?>

                          <?php $selected = false; ?>

                          <?php if( ! empty( $custom_search_filter_field['multiple'] ) ) : ?>

                            <?php if( is_array( $custom_search_filter_field['input_value'] ) && in_array( (string) $key , $custom_search_filter_field['input_value'] , true ) ) : ?>

                              <?php $selected = true; ?>

                            <?php endif; ?>

                          <?php else : ?>

                            <?php if( (string) $key === $custom_search_filter_field['input_value'] ) : ?>

                              <?php $selected = true; ?>

                            <?php endif; ?>

                          <?php endif; ?>

                          <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected , true ); ?>>
                            <?php echo esc_html( $val ); ?>
                          </option>

                        <?php endforeach; ?>

                      <?php endif; ?>

                    </select>

                  <?php elseif( $custom_search_filter_field['type'] === 'radio' ) : ?>

                    <?php if( ! empty( $custom_search_filter_field['choices'] ) ) : ?>

                      <ul>

                        <?php foreach( $custom_search_filter_field['choices'] as $key => $val ) : ?>

                          <?php if( is_array( $key ) or is_array( $val ) ) : ?>

                            <?php continue; ?>

                          <?php endif; ?>

                          <li>
                            <label>
                              <input type="radio" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key , $custom_search_filter_field['input_value'] ); ?> />
                              <?php echo esc_html( $val ); ?>
                            </label>
                          </li>

                        <?php endforeach; ?>

                      </ul>

                      <p class="clear-radio">
                        <button type="button" class="button button-secondary button-small"><?php _e( 'Clear' ); ?></button>
                      </p>

                    <?php endif; ?>

                  <?php elseif( $custom_search_filter_field['type'] === 'checkbox' ) : ?>

                    <?php if( ! empty( $custom_search_filter_field['choices'] ) ) : ?>

                      <ul>

                        <?php foreach( $custom_search_filter_field['choices'] as $key => $val ) : ?>

                          <?php if( is_array( $key ) or is_array( $val ) ) : ?>

                            <?php continue; ?>

                          <?php endif; ?>

                          <?php $checked = false; ?>

                          <?php if( in_array( (string) $key , $custom_search_filter_field['input_value'] , true ) ) : ?>

                            <?php $checked = true; ?>

                          <?php endif; ?>

                          <li>
                            <label>
                              <input type="checkbox" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $checked , true ); ?> />
                              <?php echo esc_html( $val ); ?>
                            </label>
                          </li>

                        <?php endforeach; ?>

                      </ul>

                    <?php endif; ?>

                  <?php elseif( $custom_search_filter_field['type'] === 'date' ) : ?>

                    <label class="from">
                      <?php _e( 'From' ); ?>
                      <input type="text" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>[from]" value="<?php echo esc_attr( $custom_search_filter_field['input_value']['from'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder'] ); ?>" class="from" />
                    </label>

                    <label class="to">
                      <?php _e( 'To' ); ?>
                      <input type="text" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>[to]" value="<?php echo esc_attr( $custom_search_filter_field['input_value']['to'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder'] ); ?>" class="to" />
                    </label>

                  <?php elseif( $custom_search_filter_field['type'] === 'html' ) : ?>

                    <?php echo $custom_search_filter_field['html']; ?>

                  <?php elseif( $custom_search_filter_field['type'] === 'custom' ) : ?>

                    <?php do_action( "mywp_controller_{$class::$id}_custom_search_filter_form_field_content_{$custom_search_filter_field_id}" , $custom_search_filter_field ); ?>

                    <?php do_action( "mywp_controller_{$class::$id}_custom_search_filter_form_field_content" , $custom_search_filter_field , $custom_search_filter_field_id ); ?>

                  <?php else : ?>

                    <?php
                      $message = sprintf( 'Unknown $custom_search_filter_field["type"] = %s.' , $custom_search_filter_field['type'] );

                      $called_text = sprintf( '%s::%s()' , $class , __FUNCTION__ );

                      MywpHelper::error_message( $message , $called_text );

                      //print_r($custom_search_filter_field);
                    ?>

                  <?php endif; ?>

                  <?php if( ! empty( $custom_search_filter_field['description'] ) ) : ?>

                    <div class="form-field-description">

                      <?php echo $custom_search_filter_field['description']; ?>

                    </div>

                  <?php endif; ?>

                </div>

              </div>

            <?php endforeach; ?>

          </div>

          <p class="filter-form-submit">
            <button type="submit" class="button button-primary"><?php _e( 'Search' ); ?></button>
          </p>

        </form>

      </div>

    </div>
    <?php

  }

  protected static function get_custom_search_filter_fields() {}

  protected static function get_custom_search_filter_fields_after( $custom_search_filter_fields , $custom_search_filter_requests ) {

    return $custom_search_filter_fields;

  }

}

endif;
