<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenFrontendToolbar' ) ) :

final class MywpSettingScreenFrontendToolbar extends MywpAbstractSettingToolbarModule {

  static protected $id = 'frontend_toolbar';

  static protected $priority = 20;

  static private $menu = 'frontend';

  static protected $post_type = 'mywp_front_toolbar';

  static protected $default_toolbar_items;

  static protected $current_setting_toolbar_items;

  static protected $find_parent_id;

  static protected $user_roles;

  static protected $hidden_child_items = array( 'search' );

  protected static function custom_after_init() {

    add_filter( 'mywp_setting_' . self::$id . '_print_item_content_show_child_items_content' , array( __CLASS__ , 'frontend_print_item_content_show_child_items_content' ) , 10 , 3 );

    add_action( 'mywp_setting_' . self::$id . '_print_item_content' , array( __CLASS__ , 'frontend_print_item_content' ) , 10 );

    add_filter( 'mywp_setting_' . self::$id . '_available_toolbar_items' , array( __CLASS__ , 'frontend_available_toolbar_items' ) );

    add_filter( 'mywp_setting_' . self::$id . '_print_item_header_pre_title' , array( __CLASS__ , 'frontend_print_item_header_pre_title' ) , 10 , 2 );

    add_filter( 'mywp_setting_' . self::$id . '_print_item_content_item_type' , array( __CLASS__ , 'frontend_print_item_content_item_type' ) , 10 , 2 );

    add_filter( 'mywp_setting_' . self::$id . '_get_item_icon_class' , array( __CLASS__ , 'frontend_get_item_icon_class' ) , 10 , 2 );

    add_action( 'mywp_setting_admin_print_footer_scripts_' . self::$id , array( __CLASS__ , 'frontend_refresh_js' ) );

    add_action( 'mywp_setting_screen_header_' . self::$id , array( __CLASS__ , 'frontend_refresh_button' ) );

  }

  public static function frontend_print_item_content_show_child_items_content( $show_child_items_content , $item , $item_type ) {

    if( in_array( $item->item_type , array( 'edit_post' ) ) ) {

      $show_child_items_content = false;

    }

    return $show_child_items_content;

  }

  public static function frontend_print_item_content( $item ) {

    if( empty( $item ) ) {

      return false;

    }

    if( in_array( $item->item_type , array( 'edit_post' ) ) ) {

      printf( '<p class="item-content-notice">%s</p>' , __( 'This menu item is only display for single page.' , 'my-wp' ) );

    }

  }

  public static function frontend_available_toolbar_items( $available_toolbar_items ) {

    if( empty( $available_toolbar_items ) ) {

      return $available_toolbar_items;

    }

    $toolbar_item_edit_post = array(
      'title' => __( 'Edit Post' ),
      'item_type' => 'edit_post',
      'item_capability' => 'edit_post',
      'item_icon_class' => 'dashicons-before dashicons-edit',
    );

    $new_available_toolbar_items = array();

    foreach( $available_toolbar_items as $key => $toolbar_item ) {

      if( $toolbar_item['title'] === '----------------' ) {

        $new_available_toolbar_items[] = $toolbar_item_edit_post;

      }

      $new_available_toolbar_items[] = $toolbar_item;

    }

    return $new_available_toolbar_items;

  }

  public static function frontend_print_item_header_pre_title( $pre_title , $item ) {

    if( $item->item_type === 'edit_post' ) {

      $pre_title = sprintf( '<span class="%s" style=""></span>' , esc_attr( $item->item_icon_class ) );
      $pre_title .= sprintf( '<span class="item-title">%s</span>' , __( 'Edit Post' ) );
      $pre_title .= sprintf( '<span class="item-default-title">(%s)</span>' , __( 'Edit Post' ) );

    }

    return $pre_title;

  }

  public static function frontend_print_item_content_item_type( $item , $item_type ) {

    if( $item_type === 'edit_post' ) {

      ?>

      <div class="item-content-hidden-fields">

      </div>

      <p class="item-content-show-details"><a href="javascript:void(0);" class="button-item-content-show-details"><?php _e( 'More Details' ); ?></a></p>

      <div class="item-content-details">
        <table class="form-table">
          <tbody>
            <tr>
              <th><?php _e( 'Menu Title' ); ?></th>
              <td>
                <?php _e( 'Edit Post' ); ?>
              </td>
            </tr>
            <tr>
              <th><?php _e( 'Link URL' , 'my-wp' ); ?></th>
              <td>
                <?php $link_url = add_query_arg( array( 'post' => '###' , 'action' => 'edit' ) , admin_url( 'post.php' ) ); ?>
                <?php echo str_replace( '###' , '<code>current_post_id</code>' , $link_url ); ?>
              </td>
            </tr>
            <tr>
              <th><?php _e( 'Icon class' , 'my-wp' ); ?></th>
              <td>
                <div class="item-icon-setting">
                  <div class="item-icon <?php echo esc_attr( $item->item_icon_class ); ?>"></div>
                  <?php echo $item->item_icon_class; ?>
                </div>
              </td>
            </tr>
            <tr>
              <th><?php _e( 'Capability' , 'my-wp' ); ?></th>
              <td>
                <code>
                  <?php if( ! empty( $item->item_capability ) ) : ?>
                    <?php echo $item->item_capability; ?>
                  <?php else : ?>
                    -
                  <?php endif; ?>
                </code>
                <?php self::print_item_content_field_user_role_group( 'edit_posts' ); ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <?php

    }

  }

  public static function frontend_get_item_icon_class( $item_icon_class , $item ) {

    if( $item->id === 'edit' ) {

      $item_icon_class = 'dashicons-before dashicons-edit';

    }

    return $item_icon_class;

  }

  public static function frontend_refresh_js() {

    ?>
    <script>
    jQuery(function( $ ) {

      $('body.mywp-setting #setting-screen-setting-frontend-toolbar-item-refresh-button').on('click', function() {

        let $button = $(this);
        let $button_icon = $button.parent().find('.dashicons-update');
        let url = $button.attr('href');

        if( ! url ) {

          alert( mywp_admin_setting.not_found_update_url );

          return false;

        }

        $button_icon.addClass('spin');

        PostData = {
          mywp_regist_frontend_toolbar: 1
        };

        $.ajax({
          type: 'post',
          url: url,
          data: PostData,
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

    });
    </script>
    <?php

  }

  public static function frontend_refresh_button() {

    $available_toolbar_items = self::get_available_toolbar_items();

    $toolbar_items_link = home_url() . '/';

    ?>

    <?php if( empty( $available_toolbar_items ) ) : ?>

      <p class="mywp-error-message">

        <span class="dashicons dashicons-warning"></span>

        <?php printf( __( '%1$s: %2$s is not found. Please refresh the %2$s.' , 'my-wp' ) , __( 'Error' , 'my-wp' ) , __( 'Toolbar items' , 'my-wp' ) ); ?>

      </p>

    <?php endif; ?>

    <p>
      <a href="<?php echo esc_url( $toolbar_items_link ); ?>" class="button button-secondary button-small" id="setting-screen-setting-frontend-toolbar-item-refresh-button">
        <span class="dashicons dashicons-update"></span>
        <?php _e( 'Refresh Toolbar items' , 'my-wp' ); ?>
      </a>
    </p>

    <?php

  }

  protected static function default_item_convert( $item ) {

    return MywpFrontendToolbar::default_item_convert( $item );

  }

  protected static function get_default_toolbar() {

    return MywpFrontendToolbar::get_default_toolbar();

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Toolbar' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'frontend_toolbar',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/frontend-toolbar/' ),
    );

    return $setting_screens;

  }

}

MywpSettingScreenFrontendToolbar::init();

endif;
