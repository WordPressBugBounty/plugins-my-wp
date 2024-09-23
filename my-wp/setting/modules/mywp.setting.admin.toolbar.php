<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminToolbar' ) ) :

final class MywpSettingScreenAdminToolbar extends MywpAbstractSettingToolbarModule {

  static protected $id = 'admin_toolbar';

  static protected $priority = 40;

  static private $menu = 'admin';

  static protected $post_type = 'mywp_admin_toolbar';

  static protected $default_toolbar_items;

  static protected $current_setting_toolbar_items;

  static protected $find_parent_id;

  static protected $user_roles;

  static protected $hidden_child_items = array( 'menu-toggle' );

  protected static function custom_after_init() {

    add_filter( 'mywp_setting_' . self::$id . '_print_item_content_show_child_items_content' , array( __CLASS__ , 'admin_print_item_content_show_child_items_content' ) , 10 , 3 );

    add_action( 'mywp_setting_' . self::$id . '_print_item_content' , array( __CLASS__ , 'admin_print_item_content' ) , 10 );

    add_filter( 'mywp_setting_' . self::$id . '_available_toolbar_items' , array( __CLASS__ , 'admin_available_toolbar_items' ) );

    add_filter( 'mywp_setting_' . self::$id . '_print_item_header_pre_title' , array( __CLASS__ , 'admin_print_item_header_pre_title' ) , 10 , 2 );

    add_filter( 'mywp_setting_' . self::$id . '_print_item_content_item_type' , array( __CLASS__ , 'admin_print_item_content_item_type' ) , 10 , 2 );

  }

  public static function admin_print_item_content_show_child_items_content( $show_child_items_content , $item , $item_type ) {

    if( in_array( $item->item_type , array( 'view_post' ) ) ) {

      $show_child_items_content = false;

    }

    return $show_child_items_content;

  }

  public static function admin_print_item_content( $item ) {

    if( empty( $item ) ) {

      return false;

    }

    if( in_array( $item->item_type , array( 'view_post' ) ) ) {

      printf( '<p class="item-content-notice">%s</p>' , __( 'This menu item is only display for edit post.' , 'my-wp' ) );

    }

  }

  public static function admin_available_toolbar_items( $available_toolbar_items ) {

    if( empty( $available_toolbar_items ) ) {

      return $available_toolbar_items;

    }

    $toolbar_item_edit_post = array(
      'title' => __( 'View Post' ),
      'item_type' => 'view_post',
      'item_capability' => 'read_post',
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

  public static function admin_print_item_header_pre_title( $pre_title , $item ) {

    if( $item->item_type === 'view_post' ) {

      $pre_title = sprintf( '<span class="%s" style=""></span>' , esc_attr( $item->item_icon_class ) );
      $pre_title .= sprintf( '<span class="item-title">%s</span>' , __( 'View Post' ) );
      $pre_title .= sprintf( '<span class="item-default-title">(%s)</span>' , __( 'View Post' ) );

    }

    return $pre_title;

  }

  public static function admin_print_item_content_item_type( $item , $item_type ) {

    if( $item_type === 'view_post' ) {

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
                <?php _e( 'View Post' ); ?>
              </td>
            </tr>
            <tr>
              <th><?php _e( 'Link URL' , 'my-wp' ); ?></th>
              <td>
                <?php $link_url = add_query_arg( array( 'p' => '###' ) , home_url() ); ?>
                <?php echo str_replace( '###' , '<code>current_post_id</code>' , $link_url ); ?>
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

  protected static function default_item_convert( $item ) {

    return MywpAdminToolbar::default_item_convert( $item );

  }

  protected static function get_default_toolbar() {

    return MywpAdminToolbar::get_default_toolbar();

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Toolbar' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'admin_toolbar',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-toolbar/' ),
    );

    return $setting_screens;

  }

}

MywpSettingScreenAdminToolbar::init();

endif;
