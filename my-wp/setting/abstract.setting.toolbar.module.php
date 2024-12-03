<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAbstractSettingToolbarModule' ) ) :

abstract class MywpAbstractSettingToolbarModule extends MywpAbstractSettingModule {

  static protected $default_toolbar_items;

  static protected $current_setting_toolbar_items;

  static protected $find_parent_id;

  static protected $user_roles;

  static protected $hidden_child_items;

  protected static function after_init() {

    $class = get_called_class();

    add_filter( 'mywp_setting_' . static::$id . '_regist_default_toolbar_is_not_add_menu' , array( $class , 'mywp_setting_regist_default_toolbar_is_not_add_menu' ) , 10 , 2 );

    add_filter( 'mywp_setting_' . static::$id . '_print_item_content_show_child_items_content' , array( $class , 'mywp_setting_print_item_content_show_child_items_content' ) , 10 , 3 );

    add_action( 'mywp_setting_' . static::$id . '_print_item_class' , array( $class , 'mywp_setting_print_item_class' ) , 10 , 2 );

    add_action( 'mywp_setting_' . static::$id . '_print_item_content' , array( $class , 'mywp_setting_print_item_content' ) , 10 );

    static::custom_after_init();

  }

  protected static function custom_after_init() {}

  protected static function default_item_convert( $item ) {}

  protected static function get_default_toolbar() {

    return false;

  }

  public static function mywp_setting_regist_default_toolbar_is_not_add_menu( $is_not_add_menu , $menu ) {

    if( empty( $menu->id ) ) {

      return $is_not_add_menu;

    }

    if( is_multisite() ) {

      if( in_array( $menu->parent , array( 'my-sites' , 'network-admin' ) ) ) {

        $is_not_add_menu = true;

      }

    }

    if( in_array( $menu->parent , array( 'new-cotent' ) ) ) {

      $is_not_add_menu = true;

    }

    return $is_not_add_menu;

  }

  public static function mywp_setting_print_item_content_show_child_items_content( $show_child_items_content , $item , $item_type ) {

    if( is_multisite() ) {

      if( in_array( $item->item_default_id , array( 'my-sites' , 'network-admin' ) ) ) {

        $show_child_items_content = false;

      }

    }

    if( in_array( $item->item_default_id , array( 'new-content' ) ) ) {

      $show_child_items_content = false;

    }

    if( ! empty( static::$hidden_child_items ) ) {

      if( in_array( $item->item_default_id , static::$hidden_child_items ) ) {

        $show_child_items_content = false;

      }

    }

    return $show_child_items_content;

  }

  public static function mywp_setting_print_item_class( $item_class , $item ) {

    if( empty( $item ) ) {

      return $item_class;

    }

    if( empty( $item->item_default_id ) ) {

      return $item_class;

    }

    if( is_multisite() ) {

      if( in_array( $item->item_default_id , array( 'my-sites' , 'network-admin' ) ) ) {

        $item_class = 'dynamic-submenu';

        return $item_class;

      }

    }

    if( in_array( $item->item_default_id , array( 'new-content' ) ) ) {

      $item_class = 'dynamic-submenu';

    }

    return $item_class;

  }


  public static function mywp_setting_print_item_content( $item ) {

    if( empty( $item ) ) {

      return false;

    }

    if( ! empty( static::$hidden_child_items ) ) {

      if( in_array( $item->item_default_id , static::$hidden_child_items ) ) {

        printf( '<p class="item-content-notice">%s</p>' , __( 'Submenus cannot be create for this menu.' , 'my-wp' ) );

      }

    }

  }

  public static function mywp_ajax_manager() {

    $class = get_called_class();

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'add_items' ) , array( $class , 'ajax_add_items' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'remove_items' ) , array( $class , 'ajax_remove_items' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'update_item' ) , array( $class , 'ajax_update_item' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'update_item_order_and_parents' ) , array( $class , 'ajax_update_item_order_and_parents' ) );

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( static::$id , 'remove_cache' ) , array( $class , 'ajax_remove_cache' ) );

  }

  public static function ajax_add_items() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'add_items' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['add_items'] ) or ! is_array( $_POST['add_items'] ) ) {

      return false;

    }

    $add_items = array();

    foreach( $_POST['add_items'] as $key => $post_item ) {

      if( empty( $post_item['item_type'] ) ) {

        continue;

      }

      $add_item = array();

      $add_item['item_type'] = strip_tags( $post_item['item_type'] );

      $add_item['item_custom_html'] = false;

      if( ! empty( $post_item['item_custom_html'] ) ) {

        $add_item['item_custom_html'] = wp_unslash( $post_item['item_custom_html'] );

      }

      $add_item['item_location'] = 'right';

      $add_item['item_default_id'] = false;

      if( ! empty( $post_item['item_default_id'] ) ) {

        $add_item['item_default_id'] = strip_tags( $post_item['item_default_id'] );

      }

      $add_item['item_default_parent_id'] = false;

      if( ! empty( $post_item['item_default_parent_id'] ) ) {

        $add_item['item_default_parent_id'] = strip_tags( $post_item['item_default_parent_id'] );

      }

      $add_item['item_link_title'] = false;

      if( ! empty( $post_item['item_link_title'] ) ) {

        $add_item['item_link_title'] = wp_unslash( $post_item['item_link_title'] );

      }

      $add_item['item_link_url'] = false;

      if( ! empty( $post_item['item_link_url'] ) ) {

        $add_item['item_link_url'] = strip_tags( $post_item['item_link_url'] );

      }

      $add_item['item_capability'] = false;

      if( ! empty( $post_item['item_capability'] ) ) {

        $add_item['item_capability'] = strip_tags( $post_item['item_capability'] );

      }

      $add_item['item_icon_class'] = false;

      if( ! empty( $post_item['item_icon_class'] ) ) {

        $add_item['item_icon_class'] = strip_tags( $post_item['item_icon_class'] );

      }

      $add_item['item_meta'] = array();

      if( ! empty( $post_item['item_meta'] ) && is_array( $post_item['item_meta'] ) ) {

        foreach( $post_item['item_meta'] as $item_meta_key => $item_meta_value ) {

          $item_meta_key = strip_tags( $item_meta_key );
          $item_meta_value = strip_tags( $item_meta_value );

          $add_item['item_meta'][ $item_meta_key ] = $item_meta_value;

        }

      }

      $add_items[] = $add_item;

    }

    if( empty( $add_items ) ) {

      return false;

    }

    $result_html = '';

    $post_terms = array(
      array( 'taxonomy' => 'mywp_term' , 'term' => 'default' ),
    );

    foreach( $add_items as $key => $add_item ) {

      $add_meta_data = array(
        'item_type' => $add_item['item_type'],
        'item_custom_html' => $add_item['item_custom_html'],
        'item_location' => $add_item['item_location'],
        'item_default_id' => $add_item['item_default_id'],
        'item_default_parent_id' => $add_item['item_default_parent_id'],
        'item_link_title' => $add_item['item_link_title'],
        'item_link_url' => $add_item['item_link_url'],
        'item_capability' => $add_item['item_capability'],
        'item_icon_class' => $add_item['item_icon_class'],
        'item_meta' => $add_item['item_meta'],
      );

      $post_id = static::add_post( array( 'menu_order' => 1000 ) , $add_meta_data , $post_terms );

      if ( empty( $post_id ) or is_wp_error( $post_id ) ) {

        continue;

      }

      $post = MywpPostType::get_post( $post_id );

      do_action( 'mywp_setting_' . static::$id . '_ajax_add_item' , $post_id , $add_item );

      ob_start();

      static::print_item( $post );

      $result_html .= ob_get_contents();

      ob_end_clean();

    }

    static::delete_transient_controller_get_toolbar();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function ajax_remove_items() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'remove_items' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['remove_items'] ) ) {

      return false;

    }

    $remove_items = array_map( 'intval' , $_POST['remove_items'] );

    foreach( $remove_items as $key => $post_id ) {

      $post = MywpPostType::get_post( $post_id );

      if( empty( $post )  or ! is_object( $post ) or $post->post_type !== static::$post_type ) {

        continue;

      }

      wp_delete_post( $post_id , true );

    }

    static::delete_transient_controller_get_toolbar();

    wp_send_json_success();

  }

  public static function ajax_update_item() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'update_item' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['update_item'] ) or ! is_array( $_POST['update_item'] ) ) {

      return false;

    }

    $update_item = $_POST['update_item'];

    if( empty( $update_item['item_id'] ) ) {

      return false;

    }

    $post_id = (int) $update_item['item_id'];

    unset( $update_item['item_id'] );

    $post = MywpPostType::get_post( $post_id );

    if( empty( $post )  or ! is_object( $post ) or $post->post_type !== static::$post_type ) {

      return false;

    }

    $item_type = $update_item['item_type'];

    foreach( $update_item as $meta_key => $meta_value ) {

      $meta_key = wp_unslash( strip_tags( $meta_key ) );

      if( $item_type === 'default' ) {

        if( $meta_key === 'item_link_url' ) {

          continue;

        }

      }

      if( in_array( $meta_key , array( 'item_li_class' , 'item_li_id' , 'item_capability' , 'item_link_url' , 'item_link_attr' , 'item_icon_class' , 'item_icon_id' , 'item_icon_img' , 'item_icon_style' ) ) ) {

        $meta_value = strip_tags( $meta_value );
        $meta_value = str_replace( '  ' , ' ' , $meta_value );
        $meta_value = trim( $meta_value );

      } elseif( in_array( $meta_key , array( 'item_link_title' , 'item_custom_html' , 'item_icon_title' ) ) ) {

        $meta_value = wp_unslash( wp_kses_post( $meta_value ) );

      } elseif( in_array( $meta_key , array( 'item_location' ) ) ) {

        $meta_value = strip_tags( $meta_value );

      } elseif( in_array( $meta_key , array( 'item_meta' ) ) && is_array( $meta_value ) ) {

        $meta_value = array();

        foreach( $meta_value as $item_meta_key => $item_meta_value ) {

          $item_meta_key = strip_tags( $item_meta_key );
          $item_meta_value = strip_tags( $item_meta_value );

          $meta_value[ $item_meta_key ] = $item_meta_value;

        }

      } else {

        continue;

      }

      update_post_meta( $post_id , $meta_key , $meta_value );

    }

    do_action( 'mywp_setting_' . static::$id . '_ajax_update_item' , $post_id , $update_item );

    static::delete_transient_controller_get_toolbar();

    wp_send_json_success();

  }

  public static function ajax_update_item_order_and_parents() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'update_item_order_and_parents' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['item_order_parents'] ) or ! is_array( $_POST['item_order_parents'] ) ) {

      return false;

    }

    $saved = false;

    foreach( $_POST['item_order_parents'] as $key => $post_item ) {

      if( ! isset( $post_item['order'] ) or ! isset( $post_item['parent_id'] ) or empty( $post_item['item_id'] ) or ! isset( $post_item['item_location'] ) ) {

        continue;

      }

      $post_id = (int) $post_item['item_id'];

      $post = MywpPostType::get_post( $post_id );

      if( empty( $post )  or ! is_object( $post ) or $post->post_type !== static::$post_type ) {

        continue;

      }

      $menu_order = (int) $post_item['order'];

      $post_parent = (int) $post_item['parent_id'];

      $item_location = strip_tags( $post_item['item_location'] );

      $post_data = array(
        'ID' => $post_id,
        'menu_order' => $menu_order,
        'post_parent' => $post_parent,
        'post_status' => 'publish',
      );

      wp_update_post( $post_data );

      update_post_meta( $post_id , 'item_location' , $item_location );

      $saved = true;

    }

    static::delete_transient_controller_get_toolbar();

    if( $saved ) {

      wp_send_json_success();

    }

  }

  public static function ajax_remove_cache() {

    $action_name = MywpSetting::get_ajax_action_name( static::$id , 'remove_cache' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    static::delete_transient_controller_get_toolbar();

    wp_send_json_success();

  }

  public static function mywp_current_admin_enqueue_scripts() {

    $scripts = array( 'jquery-ui-sortable' );

    foreach( $scripts as $script ) {

      wp_enqueue_script( $script );

    }

  }

  public static function mywp_current_admin_print_styles() {

    ?>
    <style>
    #setting-screen-toolbar-available-item {
      background: #fafafa;
      border: 1px solid #eee;
      padding: 20px;
    }
    #setting-screen-toolbar-available-item #add-items {
      max-width: 100%;
      min-width: 50%;
      margin-right: 2%;
      resize: auto;
      height: 300px;
    }
    #setting-screen-toolbar-available-item .spinner {
      float: right;
    }
    #setting-screen-toolbar-available-item #available-items {
      display: none;
    }
    #setting-screen-toolbar-items {
      padding: 30px 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-items-location {
      padding: 10px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item {
      border: 1px solid #ddd;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item:hover {
      border-color: #999;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.active {
      background: #FEF5EA;
      border-color: #F49C31;
    }
    #setting-screen-toolbar-items .sortable-placeholder,
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header {
      height: 38px;
    }
    #setting-screen-toolbar-items .sortable-placeholder {
      margin: 0;
      background: #ccc;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header {
      cursor: move;
      background: #fafafa;
      line-height: 38px;
      overflow:  hidden;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.active > .item-header {
      border-bottom: 1px solid #F49C31;
      background: #F7E0C5;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-active-toggle {
      float: right;
      display: inline-block;
      width: 38px;
      height: 38px;
      text-decoration: none;
      color: #72777c;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-active-toggle:hover {
      color: #23282d;
      background: #F9CD98;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-active-toggle:focus {
      box-shadow: none;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-active-toggle:before {
      font: 400 20px/1 dashicons;
      content: "\f140";
      display: block;
      text-align: center;
      padding-top: 10px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.active > .item-header .item-active-toggle:before {
      content: "\f142";
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .dashicons-networking {
      float: right;
      color: #c00;
      margin: 9px 12px 0 auto;
      transform:  rotate( 270deg );
      opacity: 0.5;
      display: none;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.dynamic-submenu .item-header .dashicons-networking {
      display: inline-block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap {
      margin-left: 10px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap .dashicons-before:before {
      margin: 8px 4px 0 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap .dashicons-before.svg:before {
      background-repeat: no-repeat;
      background-position: center;
      background-size: 20px auto;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap .item-state {
      font-size: 10px;
      line-height: 14px;
      padding: 2px 4px;
      color: #fff;
      background: #bbb;
      display: inline-block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap .item-title {}
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-header .item-title-wrap .item-default-title {
      color: #999;
      font-style: italic;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content {
      display: none;
      padding: 20px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.active > .item-content {
      display: block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-details {
      display: none;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-fields.show-details .item-content-details {
      display: block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-fields .item-content-show-details {
      margin: 0 auto 28px auto;
      padding: 0;
      text-align: center;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-fields .item-content-show-details .button-item-content-show-details {
      display: inline-block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-fields .item-content-show-details .button-item-content-show-details:before {
      font: 400 20px/1 dashicons;
      content: "\f140";
      display: inline-block;
      text-align: center;
      float: left;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-fields.show-details .item-content-show-details .button-item-content-show-details:before {
      content: "\f142";
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-notice {
      background: rgba(255, 0, 0, 0.1);
      padding: 8px 12px;
      text-align: center;
      color: #c00;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .form-table {
      margin: 0 auto 40px auto;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .form-table th,
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .form-table td {
      background: #fff;
      word-break: break-all;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .form-table th {
      width: 120px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-hidden-fields {
      display: none;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .custom-html {
      height: 300px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content ul.capability-roles {
      margin: 8px 0 0 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content ul.capability-roles li {
      color: #ccc;
      font-size: 12px;
      line-height: 1.2;
      display: inline-block;
      margin: 4px;
      padding: 2px 4px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content ul.capability-roles li.role-can {
      color: #000;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-icon-setting .item-icon {
      display: inline-block;
      margin: 0 12px 10px 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-icon-setting .dashicons-before:before {
      font-size: 34px;
      width: 34px;
      height: 34px;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-update {
      float: right;
      margin: 0 0 6px 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-remove {
      margin: 0 0 6px 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .item-content-notice-dynamic-submenu {
      background: rgba(255, 0, 0, 0.1);
      padding: 8px 12px;
      text-align: center;
      color: #c00;
      display: none;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item.dynamic-submenu .item-content .item-content-notice-dynamic-submenu {
      display: block;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .child-menu-title {
      font-weight: 400;
      margin: 40px 0 0 0;
    }
    #setting-screen-toolbar-items .setting-screen-toolbar-item .item-content .child-menus {
      margin: 10px 0;
      padding: 12px;
      min-height: 60px;
      border: 2px solid #ccc;
      background: #fff;
    }
    #setting-screen-toolbar-item-icons {
      display: none;
    }
    .setting-screen-toolbar-item-icons .available-icon-title {
      font-weight: bold;
      margin: 0 0 10px 0;
      padding: 0;
    }
    .setting-screen-toolbar-item-icons ul {
      margin: 0 0 30px 0;
      padding: 0;
    }
    .setting-screen-toolbar-item-icons ul li {
      margin: 4px;
      padding: 0;
      display: inline-block;
    }
    .wp-core-ui .setting-screen-toolbar-item-icons ul li.current button {
      background: #F49C31;
      border-color: #F49C31;
      color: #fff;
    }
    .setting-screen-toolbar-item-icons ul li button .dashicons-before:before {
      padding-top: 3px;
    }
    </style>
    <?php

  }

  public static function mywp_current_admin_print_footer_scripts() {

    $post_data_js_custom_values = array();

    $post_data_js_custom_values = apply_filters( 'mywp_setting_' . static::$id . '_post_data_js_custom_values' , $post_data_js_custom_values );

    if( ! is_array( $post_data_js_custom_values ) ) {

      $post_data_js_custom_values = array();

    }

    ?>
    <script>
    jQuery(function( $ ) {

      const post_data_js_custom_values = JSON.parse( '<?php echo json_encode( $post_data_js_custom_values ); ?>' );

      $('.sortable-items').sortable({
        placeholder: 'sortable-placeholder',
        handle: '.item-header',
        connectWith: '.sortable-items',
        distance: 2,
        stop: function( ev , ui ) {

          let $sorted_item = ui.item;

          let $spinner = $sorted_item.children().find('> .item-title-wrap .spinner');

          $spinner.css('visibility', 'visible');

          let item_order_parents = [];

          $(document).find('#setting-screen-toolbar-items .setting-screen-toolbar-item').each( function( index , el ) {

            let $item = $(el)

            let post_id = $item.find('> .item-content .id').val();

            let parent_id = 0;

            if( $item.parent().hasClass('child-menus') ) {

              parent_id = $item.parent().parent().parent().find('> .item-content-fields .id').val();

            }

            let item_location = $item.parents('.setting-screen-toolbar-items-location').prop('id').replace( 'setting-screen-toolbar-items-' , '' );

            let item_order_parent = {
              item_id: post_id,
              order: index,
              parent_id: parent_id,
              item_location: item_location
            };

            item_order_parents.push( item_order_parent );

          });

          if( item_order_parents.length === 0 ) {

            $spinner.css('visibility', 'hidden');

            return false;

          }

          PostData = {
            action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'update_item_order_and_parents' ) ); ?>',
            <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'update_item_order_and_parents' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'update_item_order_and_parents' ) ) ); ?>',
            item_order_parents: item_order_parents
          };

          $.each( post_data_js_custom_values, function( key , value ) {

            PostData[ key ] = value;

          });

          $.ajax({
            type: 'post',
            url: ajaxurl,
            data: PostData
          }).done( function( xhr ) {

            if( typeof xhr !== 'object' || xhr.success === undefined ) {

              alert( mywp_admin_setting.unknown_error_reload_page );

              return false;

            }

            return true;

          }).fail( function( xhr ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }).always( function( xhr ) {

            $spinner.css('visibility', 'hidden');

          });

        }

      });

      $('#toolbar-available-item-add-button').on('click', function() {

        let $available_item = $(this).parent();
        let $spinner = $available_item.find('.spinner');

        let add_item_keys = $available_item.find('#add-items').val();

        if( add_item_keys === null || add_item_keys === '' || typeof add_item_keys !== 'object' ) {

          return false;

        }

        let add_items = [];

        $.each( add_item_keys , function( i , add_item_key ) {

          let $available_item = $('#available-items').find('.available-item.item-key-' + add_item_key);

          if( ! $available_item.length ) {

            return true;

          }

          let item_meta = {};

          if( $available_item.find('.item_meta').length ) {

            $available_item.find('.item_meta').each( function( available_item_index , available_item_el ) {

              item_meta[ $(available_item_el).data('meta_key') ] = $(available_item_el).val();

            });

          }

          let add_item = {
            item_type: $available_item.find('.item_type').val(),
            item_custom_html: $available_item.find('.item_custom_html').val(),
            item_location: $available_item.find('.item_location').val(),
            item_default_id: $available_item.find('.item_default_id').val(),
            item_default_parent_id: $available_item.find('.item_default_parent_id').val(),
            item_link_title: $available_item.find('.item_link_title').val(),
            item_link_url: $available_item.find('.item_link_url').val(),
            item_capability: $available_item.find('.item_capability').val(),
            item_icon_class: $available_item.find('.item_icon_class').val(),
            item_meta: item_meta
          };

          add_items.push( add_item );

        });

        if( ! add_items[0] ) {

          return false;

        }

        $spinner.css('visibility', 'visible');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'add_items' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'add_items' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'add_items' ) ) ); ?>',
          add_items: add_items
          <?php do_action( 'mywp_setting_' . static::$id . '_available_item_add_post_data_JS' ); ?>
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

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

          $(document).find('#setting-screen-toolbar-items #setting-screen-toolbar-items-right').append( xhr.data.result_html );

          $(document).find('.sortable-items').sortable({
            connectWith: '.sortable-items'
          });

          let scroll_position = $(document).find('#setting-screen-toolbar-items .setting-screen-toolbar-item').last().offset().top;

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

      $(document).on('click', '#setting-screen-toolbar-items .item-active-toggle', function() {

        $(this).parent().parent().toggleClass('active');

      });

      $(document).on('click', '#setting-screen-toolbar-items .button-item-content-show-details', function() {

        $(this).parent().parent().toggleClass('show-details');

      });

      $(document).on('click', '#setting-screen-toolbar-items .item-content-change-icon', function() {

        let $current_toolbar_item_content_fields = $(this).parent().parent().parent().parent().parent().parent();
        let $current_toolbar_item = $current_toolbar_item_content_fields.parent().parent();
        let current_toolbar_item_id = $current_toolbar_item_content_fields.find('.id').val();
        let current_toolbar_item_icon_class = $current_toolbar_item_content_fields.find('.item_icon_class').val();

        if( ! current_toolbar_item_id ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }

        let selected_icon_class = get_dashicon_class( current_toolbar_item_icon_class );

        let $icons_html = $('#setting-screen-toolbar-item-icons').clone();

        $icons_html.find('.change-icon-toolbar-item-id').attr('value', current_toolbar_item_id );
        $icons_html.find('.icon-' + selected_icon_class).addClass('current');

        mywp_popup.open( $icons_html.html() );

        return false;

      });

      $(document).on('click', '#mywp-popup-content .change-icon', function() {

        let $icon_button = $(this);
        let $popup_content_inner = $icon_button.parent().parent().parent().parent().parent();
        let select_class = get_dashicon_class( $icon_button.find('.dashicons-before').prop('class') );
        let change_icon_toolbar_item_id = $popup_content_inner.find('.change-icon-toolbar-item-id').val();

        if( ! change_icon_toolbar_item_id || ! select_class ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }

        let $target_toolbar_item = $('#setting-screen-toolbar-items .setting-screen-toolbar-item.item-id-' + change_icon_toolbar_item_id);
        let $target_toolbar_item_class = $target_toolbar_item.find('> .item-content > .item-content-fields .item_icon_class');
        let target_toolbar_item_icon_class = $target_toolbar_item_class.val();

        target_toolbar_item_icon_class = target_toolbar_item_icon_class.replace( 'dashicons-before' , '' );

        let selected_icon_class = get_dashicon_class( target_toolbar_item_icon_class );

        if( selected_icon_class ) {

          target_toolbar_item_icon_class = target_toolbar_item_icon_class.replace( selected_icon_class , '' );

        }

        target_toolbar_item_icon_class += 'dashicons-before ' + select_class;

        $target_toolbar_item_class.val( target_toolbar_item_icon_class );

        $target_toolbar_item.find('> .item-content > .item-content-fields .item-icon').prop('class', '').addClass( 'item-icon ' + target_toolbar_item_icon_class );

        let scroll_position = $target_toolbar_item.offset().top;

        scroll_position = ( scroll_position - 40 );

        $( 'html,body' ).animate({
          scrollTop: scroll_position
        });

        mywp_popup.close();

        return false;

      });

      function get_dashicon_class( class_name = false ) {

        if( class_name === false ) {

          return false;

        }

        if( class_name.indexOf( 'dashicons-before' ) !== -1 ) {

          class_name = class_name.replace( 'dashicons-before' , '' );

        }

        let classes_name = class_name.split( ' ' );

        let dashicon_class = '';

        $.each( classes_name, function( index , tmp_class_name ) {

          if( tmp_class_name === '' ) {

            return true;

          }

          let matches = tmp_class_name.match( /dashicons-(.*)/g );

          if( matches ) {

            dashicon_class = matches[0];

          }

        });

        return dashicon_class;

      }

      $(document).on('click', '#setting-screen-toolbar-items .item-remove', function() {

        let $item = $(this).parent().parent().parent();
        let $spinner = $item.find('.item-content .item-content-fields .spinner');

        $spinner.css('visibility', 'visible');

        let remove_items = [];

        $item.find('.id').each( function( i , el ){
          remove_items.push( $(el).val() );
        });

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'remove_items' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'remove_items' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'remove_items' ) ) ); ?>',
          remove_items: remove_items
          <?php do_action( 'mywp_setting_' . static::$id . '_item_remove_post_data_JS' ); ?>
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $item.slideUp( 'normal' , function() {

            $item.remove();

          });

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

      $(document).on('click', '#setting-screen-toolbar-items .item-update', function() {

        let $item = $(this).parent().parent().parent();
        let $item_content_field = $(this).parent();
        let $spinner = $item_content_field.find('.spinner');

        $spinner.css('visibility', 'visible');

        let item_meta = {};

        if( $item_content_field.find('.item_meta_key').length ) {

          $item_content_field.find('.item_meta_key').each( function( item_meta_key , item_meta_el ) {

            item_meta[ $(item_meta_el).val() ] = $(item_meta_el).parent().parent().find('.item_meta_value').val();

          });

        }

        let update_item = {
          item_id: $item_content_field.find('.id').val(),
          item_type: $item_content_field.find('.item_type').val(),
          item_location: $item_content_field.find('.item_location').val(),
          item_link_title: $item_content_field.find('.item_link_title').val(),
          item_li_class: $item_content_field.find('.item_li_class').val(),
          item_li_id: $item_content_field.find('.item_li_id').val(),
          item_custom_html: $item_content_field.find('.item_custom_html').val(),
          item_capability: $item_content_field.find('.item_capability').val(),
          item_link_url: $item_content_field.find('.item_link_url').val(),
          item_link_attr: $item_content_field.find('.item_link_attr').val(),
          item_meta: item_meta,
          item_icon_class: $item_content_field.find('.item_icon_class').val(),
          item_icon_id: $item_content_field.find('.item_icon_id').val(),
          item_icon_img: $item_content_field.find('.item_icon_img').val(),
          item_icon_style: $item_content_field.find('.item_icon_style').val(),
          item_icon_title: $item_content_field.find('.item_icon_title').val(),
        };

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'update_item' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'update_item' ) ); ?>: '<?php echo esc_js(  wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'update_item' ) ) ); ?>',
          update_item: update_item
          <?php do_action( 'mywp_setting_' . static::$id . '_item_update_post_data_JS' ); ?>
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

      $(document).on('click', '.add-meta-field', function() {

        let $item_meta_fields = $(this).parent().find('.item-meta-fields tbody');

        $item_meta_fields.append('<tr><th><input type="text" class="item_meta_key large-text" value="" placeholder="Meta key" /></th><td><input type="text" class="item_meta_value large-text" value="" placeholder="Meta value" /></td><td><a href="javascript:void(0);" class="remove-meta-field button button-secondary"><span class="dashicons dashicons-minus"></span></a></td></tr>');

      });

      $(document).on('click', '.remove-meta-field', function() {

        $(this).parent().parent().remove();

      });

      $('#remove-cache').on('click', function() {

        let $spinner = $(this).parent().find('.spinner').css('visibility', 'visible');

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'remove_cache' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( static::$id , 'remove_cache' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( static::$id , 'remove_cache' ) ) ); ?>'
          <?php do_action( 'mywp_setting_' . static::$id . '_remove_cache_post_data_JS' ); ?>
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

      <?php //do_action( 'mywp_setting_' . static::$id . '_custom_jquery_print_footer_scripts' ); ?>

    });
    </script>
    <?php

  }

  public static function mywp_current_setting_screen_header() {

    $available_toolbar_items = static::get_available_toolbar_items();

    if( empty( $available_toolbar_items ) ) {

      return false;

    }

    ?>

    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Available Items' , 'my-wp' ); ?></h3>

    <div id="setting-screen-toolbar-available-item">

      <select id="add-items" multiple="multiple">

        <?php foreach( $available_toolbar_items as $key => $available_item ) : ?>

          <option value="<?php echo esc_attr( $key ); ?>" class="available-item">
            <?php if( ! empty( $available_item['item_default_parent_id'] ) ) : ?>
              &nbsp;-&nbsp;
            <?php endif; ?>
            <?php if( $available_item['item_type'] === 'group' ) : ?>
              <?php esc_attr_e( 'Group' , 'my-wp' ); ?>
              <?php if( ! empty( $available_item['item_default_id'] ) ) : ?>
                (<?php echo esc_attr( $available_item['item_default_id'] ); ?>)
              <?php endif; ?>
            <?php else : ?>
              <?php $title = strip_tags( $available_item['title'] ); ?>
              <?php if( ! empty( $title ) ) : ?>
                <?php echo esc_attr( $title ); ?>
              <?php else : ?>
                <?php echo esc_attr( strip_tags( $available_item['item_link_title'] ) ); ?>
              <?php endif; ?>
            <?php endif; ?>
          </option>

        <?php endforeach; ?>

      </select>

      <a href="javascript:void(0);" id="toolbar-available-item-add-button" class="button button-secondary"><span class="dashicons dashicons-plus"></span> <?php _e( 'Add Item' , 'my-wp' ); ?></a>

      <span class="spinner"></span>

      <div id="available-items">

        <?php foreach( $available_toolbar_items as $key => $available_item ) : ?>

          <?php if( empty( $available_item['item_type'] ) ) : ?>

            <?php continue; ?>

          <?php endif; ?>

          <div class="available-item item-key-<?php echo esc_attr( $key ); ?>">

            <input type="text" class="item_type" value="<?php echo esc_attr( $available_item['item_type'] ); ?>" />

            <?php if( ! empty( $available_item['item_custom_html'] ) ) : ?>

              <input type="text" class="item_custom_html" value="<?php echo esc_attr( $available_item['item_custom_html'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_location'] ) ) : ?>

              <input type="text" class="item_location" value="<?php echo esc_attr( $available_item['item_location'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_default_id'] ) ) : ?>

              <input type="text" class="item_default_id" value="<?php echo esc_attr( $available_item['item_default_id'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_default_parent_id'] ) ) : ?>

              <input type="text" class="item_default_parent_id" value="<?php echo esc_attr( $available_item['item_default_parent_id'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_link_title'] ) ) : ?>

              <input type="text" class="item_link_title" value="<?php echo esc_attr( $available_item['item_link_title'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_link_url'] ) ) : ?>

              <input type="text" class="item_link_url" value="<?php echo esc_attr( $available_item['item_link_url'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_capability'] ) ) : ?>

              <input type="text" class="item_capability" value="<?php echo esc_attr( $available_item['item_capability'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_icon_class'] ) ) : ?>

              <input type="text" class="item_icon_class" value="<?php echo esc_attr( $available_item['item_icon_class'] ); ?>" />

            <?php endif; ?>

            <?php if( ! empty( $available_item['item_meta'] ) ) : ?>

              <?php foreach( $available_item['item_meta'] as $item_meta_key => $item_meta ) : ?>

                <input type="text" class="item_meta" data-meta_key="<?php echo esc_attr( $item_meta_key ); ?>" value="<?php echo esc_attr( $item_meta ); ?>" />

              <?php endforeach; ?>

            <?php endif; ?>

          </div>

        <?php endforeach; ?>

      </div>

    </div>

    <p>&nbsp;</p>

    <?php

  }

  public static function mywp_current_setting_screen_content() {

    $parent_toolbar_items = static::find_items_to_parent_id();

    if( empty( $parent_toolbar_items ) ) {

      return false;

    }

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Current Toolbar' , 'my-wp' ); ?></h3>

    <p><?php _e( 'Drag menu items to edit and reorder menus.' , 'my-wp' ); ?></p>

    <div id="setting-screen-toolbar">

      <div id="setting-screen-toolbar-items">

        <h4><?php _e( 'Left' ); ?></h4>

        <div id="setting-screen-toolbar-items-left" class="sortable-items setting-screen-toolbar-items-location">

          <?php if( ! empty( $parent_toolbar_items ) ) : ?>

            <?php foreach( $parent_toolbar_items as $key => $item ) : ?>

              <?php if( $item->item_location === 'left' ) : ?>

                <?php static::print_item( $item ); ?>

              <?php endif; ?>

            <?php endforeach; ?>

          <?php endif; ?>

        </div>

        <h4><?php _e( 'Right' ); ?></h4>

        <div id="setting-screen-toolbar-items-right" class="sortable-items setting-screen-toolbar-items-location">

          <?php if( ! empty( $parent_toolbar_items ) ) : ?>

            <?php foreach( $parent_toolbar_items as $key => $item ) : ?>

              <?php if( $item->item_location === 'right' ) : ?>

                <?php static::print_item( $item ); ?>

              <?php endif; ?>

            <?php endforeach; ?>

          <?php endif; ?>

        </div>

      </div>

    </div>

    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = static::get_setting_data();

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Custom Menu UI' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][custom_menu_ui]" class="custom_menu_ui" value="1" <?php checked( $setting_data['custom_menu_ui'] , true ); ?> />
              <?php _e( 'Enable' , 'my-wp' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Cache Timeout' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="number" name="mywp[data][cache_timeout]" class="cache_timeout small-text" value="<?php echo esc_attr( $setting_data['cache_timeout'] ); ?>" />
              <?php _e( 'Minute' ); ?>
            </label>
            <button type="button" class="button button-secondary" id="remove-cache"><span class="dashicons dashicons-trash"></span> <?php _e( 'Remove Cache' , 'my-wp' ); ?></button>
            <span class="spinner"></span>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_after_footer() {

    $icons = MywpApi::get_dashicons();

    ?>

    <div id="setting-screen-toolbar-item-icons">

      <input type="hidden" class="change-icon-toolbar-item-id" value="" />

      <div class="setting-screen-toolbar-item-icons">

        <div class="icons-wp">

          <?php foreach( $icons['categories'] as $icon_category ) : ?>

            <p id="available-icon-title-<?php echo esc_attr( $icon_category['id'] ); ?>" class="available-icon-title"><?php echo $icon_category['title']; ?></p>

            <ul>
              <?php foreach( $icons['all'] as $icon ) : ?>
                <?php if( $icon['cat'] !== $icon_category['id'] ) : ?>
                  <?php continue; ?>
                <?php endif; ?>
                <li class="icon-<?php echo esc_attr( $icon['class'] ); ?>">
                  <button type="button" class="button button-secondary change-icon" title="<?php echo esc_attr( $icon['class'] ); ?>"><span class="dashicons-before <?php echo esc_attr( $icon['class'] ); ?>"></span></button>
                </li>
              <?php endforeach; ?>
            </ul>

          <?php endforeach; ?>

        </div>

      </div>

    </div>

    <?php

  }

  protected static function get_default_toolbar_items() {

    if( ! empty( static::$default_toolbar_items ) ) {

      return static::$default_toolbar_items;

    }

    $default_toolbar = static::get_default_toolbar();

    if( empty( $default_toolbar['left'] ) && empty( $default_toolbar['right'] ) ) {

      return false;

    }

    $args = array( 'show_in_admin_bar' => true );

    $post_types = get_post_types( $args , 'objects' );

    foreach( $default_toolbar as $menu_location => $menus ) {

      foreach( $menus as $key => $menu ) {

        $default_toolbar[ $menu_location ][ $key ]->capability = false;

        if( $menu->id === 'user-info' ) {

          $default_toolbar[ $menu_location ][ $key ]->title = sprintf( '%s <span class="display-name">%s</span>' , '[mywp_user field="avatar" size="64"]' , '[mywp_user field="name"]' );
          $default_toolbar[ $menu_location ][ $key ]->capability = 'read';

        } elseif( $menu->id === 'logout' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url logout="1"]';

        } elseif( $menu->id === 'my-account' ) {

          $howdy  = sprintf( __( 'Howdy, %s' ), '<span class="display-name">[mywp_user field="name"]</span>' );
          $default_toolbar[ $menu_location ][ $key ]->title = $howdy . '[mywp_user field="avatar" size="64"]';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'read';

        } elseif( $menu->id === 'my-sites' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]my-sites.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network';

        } elseif( $menu->id === 'my-sites-super-admin' ) {

          unset( $default_toolbar[ $menu_location ][ $key ] );

        } elseif( $menu->id === 'network-admin' ) {

          $default_toolbar[ $menu_location ][ $key ]->parent = '';
          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network';

        } elseif( $menu->id === 'network-admin-d' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network';

        } elseif( $menu->id === 'network-admin-s' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]sites.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_sites';

        } elseif( $menu->id === 'network-admin-u' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]users.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network_users';

        } elseif( $menu->id === 'network-admin-t' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]themes.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network_themes';

        } elseif( $menu->id === 'network-admin-p' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]plugins.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network_plugins';

        } elseif( $menu->id === 'network-admin-o' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]settings.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network_options';

        } elseif( $menu->id === 'my-sites-list' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_network';

        } elseif( $menu->id === 'site-name' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url]';

        } elseif( $menu->id === 'view-site' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url]';

        } elseif( $menu->id === 'dashboard' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'read';

        } elseif( $menu->id === 'edit-site' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url network_admin="1"]site-info.php?id=[mywp_site field="id"]';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'manage_sites';

        } elseif( $menu->id === 'updates' ) {

          $default_toolbar[ $menu_location ][ $key ]->title = sprintf( '<span class="ab-icon"></span><span class="ab-label">%s</span>' , '[mywp_update_count]' );
          $default_toolbar[ $menu_location ][ $key ]->capability = 'update_core';

        } elseif( $menu->id === 'themes' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]themes.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'switch_themes';

        } elseif( $menu->id === 'widgets' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]widgets.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'edit_theme_options';

        } elseif( $menu->id === 'menus' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]nav-menus.php';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'edit_theme_options';

        } elseif( $menu->id === 'background' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]themes.php?page=custom-background';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'edit_theme_options';

        } elseif( $menu->id === 'header' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_url admin="1"]themes.php?page=custom-header';
          $default_toolbar[ $menu_location ][ $key ]->capability = 'edit_theme_options';

        } elseif( $menu->id === 'customize' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'customize';

        } elseif( $menu->id === 'comments' ) {

          $default_toolbar[ $menu_location ][ $key ]->title = sprintf( '<span class="ab-icon"></span><span class="ab-label awaiting-mod pending-count count-%1$s" aria-hidden="true">%1$s</span>' , '[mywp_comment_count]' );
          $default_toolbar[ $menu_location ][ $key ]->capability = 'edit_posts';

        } elseif( $menu->id === 'new-content' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = $post_types['post']->cap->create_posts;

        } elseif( $menu->id === 'new-media' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'upload_files';

        } elseif( $menu->id === 'new-user' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'create_users';

        } elseif( $menu->id === 'edit' ) {

          $default_toolbar[ $menu_location ][ $key ]->href = '[mywp_post current="1" field="edit_link"]';

        } elseif( $menu->id === 'user-actions' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'read';

        } elseif( $menu->id === 'edit-profile' ) {

          $default_toolbar[ $menu_location ][ $key ]->capability = 'read';

        } elseif( $menu->id === 'search' ) {

          $default_toolbar[ $menu_location ][ $key ]->title = '[mywp_toolbar_item item="search"]';

        }

        if( $menu->parent === 'new-content' ) {

          unset( $default_toolbar[ $menu_location ][ $key ] );

        }

        if( is_multisite() ) {

          if( preg_match( '/^blog-[1-9](.*)+$/' , $menu->id ) ) {

            unset( $default_toolbar[ $menu_location ][ $key ] );

          }

        }

      }

    }

    $default_toolbar = apply_filters( 'mywp_setting_' . static::$id . '_get_default_toolbar_items' , $default_toolbar );

    static::$default_toolbar_items = $default_toolbar;

    return static::$default_toolbar_items;

  }

  protected static function get_available_toolbar_items() {

    $default_toolbar_items = static::get_default_toolbar_items();

    if( empty( $default_toolbar_items['left'] ) && empty( $default_toolbar_items['right'] ) ) {

      return false;

    }

    $available_toolbar_items = array(
      array(
        'title' => __( 'Custom HTML' ),
        'item_type' => 'custom',
      ),
      array(
        'title' => __( 'Custom Link' ),
        'item_type' => 'link',
      ),
      array(
        'title' => __( 'Group' , 'my-wp' ),
        'item_type' => 'group',
      ),
      array(
        'title' => '----------------',
        'item_type' => '',
      ),
    );

    foreach( $default_toolbar_items as $menu_location => $menus ) {

      foreach( $menus as $menu ) {

        if( ! empty( $menu->group ) ) {

          $available_toolbar_items[] = array(
            'title' => '',
            'item_type' => 'group',
            'item_location' => $menu_location,
            'item_default_id' => $menu->id,
            'item_default_parent_id' => $menu->parent,
            'item_capability' => $menu->capability,
            'item_meta' => $menu->meta,
          );

        } else {

          $title = strip_tags( strip_shortcodes( $menu->title ) );

          if( ! preg_match( '/[^\s　]/' , $title ) ) {

            $title = '';

          }

          $item_icon_class = static::get_item_icon_class( $menu );

          if( ! empty( $item_icon_class ) ) {

            $menu->title = str_replace( '<span class="ab-icon"></span>' , '' , $menu->title );

          }

          $available_toolbar_items[] = array(
            'title' => $title,
            'item_type' => 'default',
            'item_location' => $menu_location,
            'item_default_id' => $menu->id,
            'item_default_parent_id' => $menu->parent,
            'item_link_title' => $menu->title,
            'item_link_url' => $menu->href,
            'item_capability' => $menu->capability,
            'item_icon_class' => $item_icon_class,
            'item_meta' => $menu->meta,
          );

        }

      }

    }

    return apply_filters( 'mywp_setting_' . static::$id . '_available_toolbar_items' , $available_toolbar_items );

  }

  protected static function get_current_setting_toolbar_item_posts() {

    $args = array(
      'post_status' => array( 'publish' , 'draft' ),
      'post_type' => static::$post_type,
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'mywp_term',
          'field' => 'slug',
          'terms' => 'default',
        ),
      ),
    );

    $args = apply_filters( 'mywp_setting_' . static::$id . '_get_current_setting_toolbar_item_posts_args' , $args );

    $current_setting_toolbar_item_posts = MywpSetting::get_posts( $args );

    return $current_setting_toolbar_item_posts;

  }

  protected static function get_current_setting_toolbar_items() {

    if( ! empty( static::$current_setting_toolbar_items ) ) {

      return static::$current_setting_toolbar_items;

    }

    $current_setting_toolbar_items = static::get_current_setting_toolbar_item_posts();

    if( empty( $current_setting_toolbar_items ) ) {

      static::regist_default_toolbar_items();

      $current_setting_toolbar_items = static::get_current_setting_toolbar_item_posts();

    }

    if( empty( $current_setting_toolbar_items ) ) {

      return false;

    }

    foreach( $current_setting_toolbar_items as $key => $toolbar_item ) {

      if( $toolbar_item->item_type === 'default') {

        $toolbar_item = static::default_item_convert( $toolbar_item );

        if( ! empty( $toolbar_item ) ) {

          $current_setting_toolbar_items[ $key ] = $toolbar_item;

        } else {

          unset( $current_setting_toolbar_items[ $key ] );

        }

      }

    }

    static::$current_setting_toolbar_items = apply_filters( 'mywp_setting_' . static::$id . '_get_current_setting_toolbar_items' , $current_setting_toolbar_items );

    return $current_setting_toolbar_items;

  }

  protected static function find_items_to_parent_id( $parent_id = 0 ) {

    $current_setting_toolbar_items = static::get_current_setting_toolbar_items();

    if( empty( $current_setting_toolbar_items ) ) {

      return false;

    }

    $parent_id = (int) $parent_id;

    if( ! empty( static::$find_parent_id[ $parent_id ] ) ) {

      return static::$find_parent_id[ $parent_id ];

    }

    $find_items = array();

    foreach( $current_setting_toolbar_items as $item ) {

      if( $item->item_parent !== $parent_id ) {

        continue;

      }

      $find_items[] = $item;

    }

    if( empty( $find_items ) ) {

      return false;

    }

    static::$find_parent_id[ $parent_id ] = $find_items;

    return $find_items;

  }

  protected static function regist_default_toolbar_items() {

    $default_toolbar_item = static::get_default_toolbar_items();

    if( empty( $default_toolbar_item['left'] ) && empty( $default_toolbar_item['right'] ) ) {

      return false;

    }

    MywpHelper::set_time_limit( 300 );

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    static::regist_default_toolbar_find_items( '' );

  }

  protected static function regist_default_toolbar_find_items( $find_parent_item_id = false , $parent_post_id = 0 , $menu_order = 0 ) {

    if( $find_parent_item_id === false ) {

      return false;

    }

    $find_parent_item_id = strip_tags( $find_parent_item_id );

    $parent_post_id = (int) $parent_post_id;

    $menu_order = (int) $menu_order;

    $default_toolbar_item = static::get_default_toolbar_items();

    $find_items = array();

    foreach( $default_toolbar_item as $menu_location => $menus ) {

      foreach( $menus as $menu_key => $menu ) {

        if( (string) $menu->parent === (string) $find_parent_item_id ) {

          $find_items[ $menu_location ][ $menu_key ] = $menu;

        }

      }

    }

    if( empty( $find_items ) ) {

      return false;

    }

    $post_terms = array(
      array( 'taxonomy' => 'mywp_term' , 'term' => 'default' ),
    );

    foreach( $find_items as $menu_location => $menus ) {

      foreach( $menus as $menu_key => $menu ) {

        if( ! empty( $menu->group ) ) {

          $add_meta_data = array(
            'item_type' => 'group',
            'item_location' => $menu_location,
            'item_default_id' => $menu->id,
            'item_default_parent_id' => $menu->parent,
            'item_default_title' => $menu->title,
            'item_capability' => $menu->capability,
            'item_meta' => $menu->meta,
          );

        } else {

          $item_icon_class = static::get_item_icon_class( $menu );

          if( ! empty( $item_icon_class ) ) {

            $menu->title = str_replace( '<span class="ab-icon"></span>' , '' , $menu->title );

          }

          $add_meta_data = array(
            'item_type' => 'default',
            'item_location' => $menu_location,
            'item_default_id' => $menu->id,
            'item_default_parent_id' => $menu->parent,
            'item_default_title' => $menu->title,
            'item_link_title' => $menu->title,
            'item_link_url' => $menu->href,
            'item_icon_class' => $item_icon_class,
            'item_capability' => $menu->capability,
            'item_meta' => $menu->meta,
          );

        }

        $regist_default_toolbar_is_not_add_menu = apply_filters( 'mywp_setting_' . static::$id . '_regist_default_toolbar_is_not_add_menu' , false , $menu );

        if( $regist_default_toolbar_is_not_add_menu ) {

          continue;

        }

        $post_data = array( 'post_status' => 'draft' , 'post_parent' => $parent_post_id , 'menu_order' => $menu_order );

        $post_data = apply_filters( 'mywp_setting_' . static::$id . '_regist_default_toolbar_post_data' , $post_data );

        $add_meta_data = apply_filters( 'mywp_setting_' . static::$id . '_regist_default_toolbar_post_meta_data' , $add_meta_data );

        $add_post_id = static::add_post( $post_data , $add_meta_data , $post_terms );

        ++$menu_order;

        if ( empty( $add_post_id ) ) {

          MywpHelper::error_not_found_message( '$add_post_id' , $called_text );

          continue;

        } elseif( is_wp_error( $add_post_id ) ) {

          MywpHelper::error_message( $add_post_id->get_error_message() , $called_text );

          continue;

        }

        do_action( 'mywp_setting_' . static::$id . '_regist_default_toolbar_find_item' , $add_post_id , $menu , $add_meta_data );

        static::regist_default_toolbar_find_items( $menu->id , $add_post_id , $menu_order );

      }

    }

  }

  protected static function print_item( $item = false , $find_parent = 0 ) {

    if( empty( $item ) or ! is_object( $item ) ) {

      return false;

    }

    $find_parent = (int) $find_parent;

    if( $find_parent !== $item->item_parent ) {

      return false;

    }

    $item_class = apply_filters( 'mywp_setting_' . static::$id . '_print_item_class' , '' , $item , $find_parent );

    ?>

    <div class="setting-screen-toolbar-item item-id-<?php echo esc_attr( $item->ID ); ?> <?php echo esc_attr( $item_class ); ?>">

      <?php static::print_item_header( $item ); ?>

      <?php static::print_item_content( $item ); ?>

      <?php do_action( 'mywp_setting_' . static::$id . '_print_item' , $item , $find_parent ); ?>

    </div>

    <?php

  }

  protected static function print_item_header( $item ) {

    $pre_title = apply_filters( 'mywp_setting_' . static::$id . '_print_item_header_pre_title' , '' , $item );

    if( strpos( $item->item_link_title , '<input ' ) !== false ) {

      $item->item_link_title = esc_html( $item->item_link_title );

    }

    if( strpos( $item->item_default_title , '<input ' ) !== false ) {

      $item->item_default_title = esc_html( $item->item_default_title );

    }

    ?>

    <div class="item-header">

      <a href="javascript:void(0);" class="item-active-toggle">&nbsp;</a>

      <span class="dashicons dashicons-networking"></span>

      <div class="item-title-wrap">

        <?php do_action( 'mywp_setting_' . static::$id . '_print_item_header_pre_add_title' , $item ); ?>

        <?php if( ! empty( $pre_title ) ) : ?>

          <?php echo $pre_title; ?>

        <?php else : ?>

          <?php if( $item->post_status !== 'publish' ) : ?>

            <span class="item-state"><?php _post_states( $item ); ?></span>

          <?php endif; ?>

          <?php if( ! empty( $item->item_icon_img ) ) : ?>

            <img src="<?php echo esc_attr( $item->item_icon_img ); ?>" />

          <?php elseif( ! empty( $item->item_icon_class ) or ! empty( $item->item_icon_style ) ) : ?>

            <span class="<?php echo esc_attr( $item->item_icon_class ); ?>" style="<?php echo esc_attr( $item->item_icon_style ); ?>"></span>

          <?php endif; ?>

          <?php if( in_array( $item->item_type , array( 'default' ) ) ) : ?>

            <span class="item-title"><?php echo strip_tags( strip_shortcodes( $item->item_link_title ) ); ?></span>

          <?php endif; ?>

          <?php if( $item->item_type === 'group' ) : ?>

            <span class="item-title"><?php _e( 'Group' , 'my-wp' ); ?> (<?php echo $item->item_default_id; ?>)</span>

          <?php endif; ?>

          <?php if( $item->item_type === 'custom' ) : ?>

            <span class="item-title"><?php echo wp_html_excerpt( $item->item_custom_html , 20 ); ?></span>
            <span class="item-default-title">(<?php _e( 'Custom HTML' ); ?>)</span>

          <?php elseif( $item->item_type === 'link' ) : ?>

            <span class="item-title"><?php echo strip_tags( strip_shortcodes( $item->item_link_title ) ); ?></span>
            <span class="item-default-title">(<?php _e( 'Custom Link' ); ?>)</span>

          <?php elseif( ! empty( $item->item_default_title ) ) : ?>

            <span class="item-default-title">(<?php echo $item->item_default_title; ?>)</span>

          <?php endif; ?>

        <?php endif; ?>

        <span class="spinner"></span>

      </div>

      <?php do_action( 'mywp_setting_' . static::$id . '_print_item_header' , $item ); ?>

    </div>

    <?php

  }

  protected static function print_item_content( $item ) {

    $item_type = $item->item_type;

    ?>

    <div class="item-content item-type-<?php echo esc_attr( $item_type ); ?>">

      <div class="item-content-fields">

        <?php static::print_item_content_field( 'id' , $item->ID , $item ); ?>
        <?php static::print_item_content_field( 'item_type' , $item->item_type , $item ); ?>
        <?php static::print_item_content_field( 'menu_order' , $item->menu_order , $item ); ?>
        <?php static::print_item_content_field( 'item_parent' , $item->item_parent , $item ); ?>
        <?php static::print_item_content_field( 'item_location' , $item->item_location , $item ); ?>
        <?php static::print_item_content_field( 'item_default_id' , $item->item_default_id , $item ); ?>
        <?php static::print_item_content_field( 'item_default_parent_id' , $item->item_default_parent_id , $item ); ?>

        <?php do_action( 'mywp_setting_' . static::$id . '_print_item_content' , $item ); ?>

        <?php if( $item_type === 'default' ) : ?>

          <div class="item-content-hidden-fields">

            <?php static::print_item_content_field( 'item_capability' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_custom_html' , '' , $item ); ?>

            <?php static::print_item_content_field( 'item_li_class' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_li_id' , '' , $item ); ?>

            <?php static::print_item_content_field( 'item_link_url' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_link_attr' , '' , $item ); ?>

            <?php static::print_item_content_field( 'item_icon_id' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_title' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_style' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_img' , '' , $item ); ?>

          </div>

          <table class="form-table">
            <tbody>
              <tr>
                <th><?php _e( 'Menu Title' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_link_title' , $item->item_link_title , $item ); ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Icon class' , 'my-wp' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_icon_class' , $item->item_icon_class , $item ); ?>
                </td>
              </tr>
            </tbody>
          </table>

          <p class="item-content-show-details"><a href="javascript:void(0);" class="button-item-content-show-details"><?php _e( 'More Details' ); ?></a></p>

          <div class="item-content-details">

            <table class="form-table">
              <tbody>
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
                    <?php static::print_item_content_field_user_role_group( $item->item_capability ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Link URL' , 'my-wp' ); ?></th>
                  <td>
                    <a href="<?php echo esc_url( do_shortcode( $item->item_link_url ) ); ?>"><?php echo $item->item_link_url; ?></a>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'LI id' , 'my-wp' ); ?></th>
                  <td>
                    <?php echo $item->item_li_id; ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Meta Fields' , 'my-wp' ); ?></th>
                  <td>
                    <?php if( ! empty( $item->item_meta ) && is_array( $item->item_meta ) ) : ?>
                      <ul>
                        <?php foreach( $item->item_meta as $item_meta_key => $item_meta_val ) : ?>
                          <li>
                            <?php echo $item_meta_key; ?>: <?php echo $item_meta_val; ?>
                            <span><input type="hidden" class="item_meta_key" value="<?php echo esc_attr( $item_meta_key ); ?>" /></span>
                            <span><input type="hidden" class="item_meta_value" value="<?php echo esc_attr( $item_meta_val ); ?>" /></span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        <?php elseif( $item_type === 'group' ) : ?>

          <div class="item-content-hidden-fields">

          </div>

          <p class="item-content-show-details"><a href="javascript:void(0);" class="button-item-content-show-details"><?php _e( 'More Details' ); ?></a></p>

          <div class="item-content-details">

            <table class="form-table">
              <tbody>
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
                    <?php static::print_item_content_field_user_role_group( $item->item_capability ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'LI id' , 'my-wp' ); ?></th>
                  <td>
                    <?php echo $item->item_li_id; ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Meta Fields' , 'my-wp' ); ?></th>
                  <td>
                    <?php if( ! empty( $item->item_meta ) && is_array( $item->item_meta ) ) : ?>
                      <ul>
                        <?php foreach( $item->item_meta as $item_meta_key => $item_meta_val ) : ?>
                          <li>
                            <?php echo $item_meta_key; ?>: <?php echo $item_meta_val; ?>
                            <span><input type="hidden" class="item_meta_key" value="<?php echo esc_attr( $item_meta_key ); ?>" /></span>
                            <span><input type="hidden" class="item_meta_value" value="<?php echo esc_attr( $item_meta_val ); ?>" /></span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        <?php elseif( $item_type === 'link' ) : ?>

          <div class="item-content-hidden-fields">

            <?php static::print_item_content_field( 'item_custom_html' , '' , $item ); ?>

          </div>

          <table class="form-table">
            <tbody>
              <tr>
                <th><?php _e( 'Menu Title' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_link_title' , $item->item_link_title , $item ); ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Link URL' , 'my-wp' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_link_url' , $item->item_link_url , $item ); ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Link Attributes' , 'my-wp' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_link_attr' , $item->item_link_attr , $item ); ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Icon class' , 'my-wp' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_icon_class' , $item->item_icon_class , $item ); ?>
                </td>
              </tr>
              <tr>
                <th><?php _e( 'Capability' , 'my-wp' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_capability' , $item->item_capability , $item ); ?>
                </td>
              </tr>
            </tbody>
          </table>

          <p class="item-content-show-details"><a href="javascript:void(0);" class="button-item-content-show-details"><?php _e( 'More Details' ); ?></a></p>

          <div class="item-content-details">
            <table class="form-table">
              <tbody>
                <tr>
                  <th><?php _e( 'LI class' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_li_class' , $item->item_li_class , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'LI id' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_li_id' , $item->item_li_id , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Icon id' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_icon_id' , $item->item_icon_id , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Icon img' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_icon_img' , $item->item_icon_img , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Icon style' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_icon_style' , $item->item_icon_style , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Icon html' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_icon_title' , $item->item_icon_title , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Meta Fields' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_meta' , $item->item_meta , $item ); ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        <?php elseif( $item_type === 'custom' ) : ?>

          <div class="item-content-hidden-fields">

            <?php static::print_item_content_field( 'item_link_title' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_link_url' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_link_attr' , '' , $item ); ?>

            <?php static::print_item_content_field( 'item_icon_class' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_id' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_title' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_style' , '' , $item ); ?>
            <?php static::print_item_content_field( 'item_icon_img' , '' , $item ); ?>

          </div>

          <table class="form-table">
            <tbody>
              <tr>
                <th><?php _e( 'Custom HTML' ); ?></th>
                <td>
                  <?php static::print_item_content_field( 'item_custom_html' , $item->item_custom_html , $item ); ?>
                </td>
              </tr>
            </tbody>
          </table>

          <p class="item-content-show-details"><a href="javascript:void(0);" class="button-item-content-show-details"><?php _e( 'More Details' ); ?></a></p>

          <div class="item-content-details">
            <table class="form-table">
              <tbody>
                <tr>
                  <th><?php _e( 'Capability' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_capability' , $item->item_capability , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'LI class' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_li_class' , $item->item_li_class , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'LI id' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_li_id' , $item->item_li_id , $item ); ?>
                  </td>
                </tr>
                <tr>
                  <th><?php _e( 'Meta Fields' , 'my-wp' ); ?></th>
                  <td>
                    <?php static::print_item_content_field( 'item_meta' , $item->item_meta , $item ); ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        <?php else : ?>

          <?php do_action( 'mywp_setting_' . static::$id . '_print_item_content_item_type' , $item , $item_type ); ?>

        <?php endif; ?>

        <?php do_action( 'mywp_setting_' . static::$id . '_print_item_content_after' , $item ); ?>

        <div class="clear"></div>

        <a href="javascript:void(0);" class="item-update button button-primary"><?php _e( 'Update' ); ?></a>

        <a href="javascript:void(0);" class="item-remove button button-secondary button-caution"><span class="dashicons dashicons-no-alt"></span> <?php _e( 'Remove' ); ?></a>

        <span class="spinner"></span>

      </div>

      <p class="item-content-notice-dynamic-submenu"><?php _e( 'This menu item is dynamic submenus content and can not have a submenu.' , 'my-wp' ); ?></p>

      <?php $show_child_items_content = apply_filters( 'mywp_setting_' . static::$id . '_print_item_content_show_child_items_content' , true , $item , $item_type ); ?>

      <?php if( $show_child_items_content ) : ?>

        <div class="child-items-content">

          <p class="child-menu-title"><?php _e( 'Child Menus' , 'my-wp' ); ?></p>

          <div class="child-menus sortable-items">

            <?php $child_toolbar_items = static::find_items_to_parent_id( $item->ID ); ?>

            <?php if( ! empty( $child_toolbar_items ) ) : ?>

              <?php foreach( $child_toolbar_items as $key => $sub_item ) : ?>

                <?php static::print_item( $sub_item , $sub_item->item_parent ); ?>

              <?php endforeach; ?>

            <?php endif; ?>

          </div>

        </div>

      <?php endif; ?>

    </div>

    <?php

  }

  protected static function print_item_content_field( $field_name = false , $value = '' , $item = false , $args = false ) {

    if( empty( $field_name ) or ! is_object( $item ) ) {

      return false;

    }

    $field_name = strip_tags( $field_name );

    if( $field_name === 'id' ) {

      printf( '<input type="hidden" class="id" value="%s" />' , esc_attr( $value ) );

    } elseif( $field_name === 'item_type' ) {

      printf( '<input type="hidden" class="item_type" value="%s" placeholder="default" />' , esc_attr( $value ) );

    } elseif( $field_name === 'menu_order' ) {

      printf( '<input type="hidden" class="menu_order" value="%d" placeholder="0" />' , esc_attr( $value ) );

    } elseif( $field_name === 'item_parent' ) {

      printf( '<input type="hidden" class="post_parent" value="%d" placeholder="0" />' , esc_attr( $value ) );

    } elseif( $field_name === 'item_location' ) {

      printf( '<input type="hidden" class="item_location" value="%s" placeholder="left" />' , esc_attr( $value ) );

    } elseif( $field_name === 'item_default_id' ) {

      printf( '<input type="hidden" class="item_default_id" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'index.php' ) );

    } elseif( $field_name === 'item_default_parent_id' ) {

      printf( '<input type="hidden" class="item_default_parent_id" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'index.php' ) );

    } elseif( $field_name === 'item_capability' ) {

      printf( '<input type="text" class="item_capability large-text" value="%s" placeholder="%s" />' ,  esc_attr( $value ) , esc_attr( 'read' ) );

    } elseif( $field_name === 'item_custom_html' ) {

      printf( '<textarea class="item_custom_html large-text" placeholder="%s">%s</textarea>' , esc_attr( '<div class="" style="">Custom HTML</div>...' ) , esc_textarea( wp_kses_post( $value ) ) );

    } elseif( $field_name === 'item_li_class' ) {

      printf( '<input type="text" class="item_li_class large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'toolbar-item-li-class' ) );

    } elseif( $field_name === 'item_li_id' ) {

      printf( '<input type="text" class="item_li_id large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'toolbar-item-li-id' ) );

    } elseif( $field_name === 'item_link_url' ) {

      printf( '<input type="text" class="item_link_url large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'e.g.) https://example.com or [mywp_url]' ) );

    } elseif( $field_name === 'item_link_title' ) {

      $default_title = $item->item_default_title;

      if( $item->item_type === 'link' ) {

        $default_title = __( 'Example menu title' , 'my-wp' );

      }

      printf( '<input type="text" class="item_link_title large-text" value="%s" placeholder="%s" />' , esc_attr( wp_kses_post( $value ) ) , esc_attr( $default_title ) );

    } elseif( $field_name === 'item_link_attr' ) {

      printf( '<input type="text" class="item_link_attr large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'target="_blank"' ) );

    } elseif( $field_name === 'item_icon_class' ) {

      printf( '<div class="item-icon-setting"><div class="item-icon %s"></div><button type="button" class="button button-secondary item-content-change-icon">%s</button></div>' , esc_attr( $value ) , __( 'Change Icon' , 'my-wp' ) );
      printf( '<input type="text" class="item_icon_class large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'toolbar-item-icon-class' ) );

    } elseif( $field_name === 'item_icon_id' ) {

      printf( '<input type="text" class="item_icon_id large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'toolbar-item-icon-id' ) );

    } elseif( $field_name === 'item_icon_img' ) {

      printf( '<input type="text" class="item_icon_img large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( esc_url( do_shortcode( '[mywp_url]' ) . '/icon.png' ) ) );

    } elseif( $field_name === 'item_icon_style' ) {

      printf( '<input type="text" class="item_icon_style large-text" value="%s" placeholder="%s" />' , esc_attr( $value ) , esc_attr( 'background: #000; color: #fff;' ) );

    } elseif( $field_name === 'item_icon_title' ) {

      printf( '<input type="text" class="item_icon_title large-text" value="%s" placeholder="%s" />' , esc_attr( wp_kses_post( $value ) ) , esc_attr( 'Icon Html' ) );

    } elseif( $field_name === 'item_meta' ) {

      echo '<table class="item-meta-fields">';

      echo '<thead>';

      printf( '<tr><th>%s</th><td>%s</td><td>&nbsp;</td></tr>' , __( 'Meta key' , 'my-wp' ) , __( 'Meta value' , 'my-wp') );

      echo '</thead><tbody>';

      if( ! empty( $value ) ) {

        foreach( $value as $item_meta_key => $item_meta_value ) {

          echo '<tr>';

          printf( '<th><input type="text" class="item_meta_key large-text" value="%s" placeholder="%s" /></th>' , esc_attr( $item_meta_key ) , esc_attr( 'Meta key') );

          echo '<td>';

          printf( '<input type="text" class="item_meta_value large-text" value="%s" placeholder="%s" />' , esc_attr( $item_meta_value ) , esc_attr( 'Meta value') );

          echo '</td>';

          echo '<td>';

          echo '<a href="javascript:void(0);" class="remove-meta-field button button-secondary"><span class="dashicons dashicons-minus"></span></a>';

          echo '</td>';

          echo '</tr>';

        }

      }

      echo '</tbody></table>';

      printf( '<a href="javascript:void(0);" class="add-meta-field button button-secondary"><span class="dashicons dashicons-plus"></span>%s</a>' , __( 'Add' ) );

    } else {

      do_action( 'mywp_setting_' . static::$id . '_print_item_content_field' , $field_name , $value , $item );

    }

  }

  protected static function print_item_content_field_user_role_group( $capability ) {

    echo '<ul class="capability-roles">';

    $user_roles = static::get_user_roles();

    foreach( $user_roles as $role_group_name => $role ) {

      $role_has_class = '';

      if( empty( $capability ) or ! empty( $role['capabilities'][ $capability ] ) ) {

        $role_has_class = ' role-can';

      }

      printf( '<li class="%s %s">%s</li>' , esc_attr( $role_group_name ) , esc_attr( $role_has_class ) , esc_attr( $role['label'] ) );

    }

    echo '</ul>';

  }

  protected static function get_item_icon_class( $item ) {

    $item_icon_class = '';

    if( $item->id === 'menu-toggle' ) {

      $item_icon_class = 'dashicons-before dashicons-menu';

    } elseif( $item->id === 'wp-logo' ) {

      $item_icon_class = 'dashicons-before dashicons-wordpress';

    } elseif( $item->id === 'site-name' ) {

      $item_icon_class = 'dashicons-before dashicons-admin-home';

    } elseif( $item->id === 'updates' ) {

      $item_icon_class = 'dashicons-before dashicons-update';

    } elseif( $item->id === 'comments' ) {

      $item_icon_class = 'dashicons-before dashicons-admin-comments';

    } elseif( $item->id === 'new-content' ) {

      $item_icon_class = 'dashicons-before dashicons-plus';

    }

    $item_icon_class = apply_filters( 'mywp_setting_' . static::$id . '_get_item_icon_class' , $item_icon_class , $item );

    return $item_icon_class;

  }

  protected static function get_user_roles() {

    if( ! empty( static::$user_roles ) ) {

      return static::$user_roles;

    }

    static::$user_roles = MywpApi::get_all_user_roles();

    return static::$user_roles;

  }

  protected static function add_post( $args = array() , $post_metas = array() , $post_terms = array() ) {

    global $wpdb;

    $default_args = array(
      'post_type' => static::$post_type,
      'post_status' => 'draft',
      'post_parent' => 0,
    );

    $post = wp_parse_args( $args , $default_args );

    $post_id = wp_insert_post( $post );

    if ( empty( $post_id ) or is_wp_error( $post_id ) ) {

      return $post_id;

    }

    $post_id = (int) $post_id;

    $post_terms = apply_filters( 'mywp_setting_' . static::$id . '_add_post_terms' , $post_terms , $args );

    if( ! empty( $post_terms ) ) {

      foreach( $post_terms as $post_term ) {

        if( empty( $post_term['taxonomy'] ) or empty( $post_term['term'] ) ) {

          continue;

        }

        $taxonomy = strip_tags( $post_term['taxonomy'] );

        $term = strip_tags( $post_term['term'] );

        $term_exists = term_exists( $term , $taxonomy );

        if( empty( $term_exists ) ) {

          wp_insert_term( $term , $taxonomy );

        }

        wp_set_object_terms( $post_id , $term , $taxonomy );

      }

    }

    $post_metas = apply_filters( 'mywp_setting_' . static::$id . '_add_post_metas' , $post_metas , $args );

    if( ! empty( $post_metas ) ) {

      $add_meta_data = array();

      foreach( $post_metas as $meta_key => $meta_value ) {

        $meta_key = strip_tags( $meta_key );

        $add_meta_data[] = $wpdb->prepare( "(NULL, %d, %s, %s)" , $post_id , wp_unslash( $meta_key ) , maybe_serialize( wp_unslash( $meta_value ) ) );

      }

      $query = "INSERT INTO $wpdb->postmeta (meta_id, post_id, meta_key, meta_value) VALUES " . implode( ',' , $add_meta_data );

      $wpdb->query( $query );

    }

    return $post_id;

  }

  protected static function delete_transient_controller_get_toolbar() {

    do_action( 'mywp_setting_' . static::$id . '_before_delete_transient_controller_get_toolbar' );

    $mywp_transient = new MywpTransient( static::$id . '_get_toolbar' , 'controller' );

    $return = $mywp_transient->remove_data();

    do_action( 'mywp_setting_' . static::$id . '_after_delete_transient_controller_get_toolbar' );

    return $return;

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = static::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['custom_menu_ui'] ) ) {

      $new_formatted_data['custom_menu_ui'] = true;

    }

    if( isset( $formatted_data['cache_timeout'] ) ) {

      $new_formatted_data['cache_timeout'] = (int) $formatted_data['cache_timeout'];

    }

    static::delete_transient_controller_get_toolbar();

    $current_setting_toolbar_items = static::get_current_setting_toolbar_item_posts();

    if( empty( $current_setting_toolbar_items ) ) {

      return false;

    }

    foreach( $current_setting_toolbar_items as $key => $current_setting_toolbar_item ) {

      $post_id = $current_setting_toolbar_item->ID;

      $post = MywpPostType::get_post( $post_id );

      if( empty( $post )  or ! is_object( $post ) or $post->post_type !== static::$post_type ) {

        continue;

      }

      $post = array(
        'ID' => $post_id,
        'post_status' => 'publish',
      );

      wp_update_post( $post );

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    if( empty( $validated_data['remove'] ) ) {

      return false;

    }

    static::delete_transient_controller_get_toolbar();

    $current_setting_toolbar_items = static::get_current_setting_toolbar_item_posts();

    if( empty( $current_setting_toolbar_items ) ) {

      return false;

    }

    foreach( $current_setting_toolbar_items as $key => $current_setting_toolbar_item ) {

      $post_id = $current_setting_toolbar_item->ID;

      $post = MywpPostType::get_post( $post_id );

      if( empty( $post )  or ! is_object( $post ) or $post->post_type !== static::$post_type ) {

        continue;

      }

      wp_delete_post( $post_id );

    }

  }

}

endif;
