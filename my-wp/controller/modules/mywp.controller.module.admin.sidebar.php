<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminSidebar' ) ) :

final class MywpControllerModuleAdminSidebar extends MywpControllerAbstractModule {

  static protected $id = 'admin_sidebar';

  static private $sidebar = false;

  static private $sidebar_items = false;

  static private $sidebar_items_added_classes = false;

  static private $child_items = array();

  static private $parent_items = array();

  static private $found_parent_item_ids = array();

  static private $current_url = false;

  static private $find_parent_id = array();

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['custom_menu_ui'] = '';
    $initial_data['cache_timeout'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['custom_menu_ui'] = false;
    $default_data['cache_timeout'] = '60';

    return $default_data;

  }

  protected static function after_init() {

    add_filter( 'mywp_controller_admin_sidebar_get_sidebar' , array( __CLASS__ , 'mywp_controller_admin_sidebar_get_sidebar' ) );

    add_filter( 'mywp_controller_admin_sidebar_get_sidebar_item' , array( __CLASS__ , 'mywp_controller_admin_sidebar_get_sidebar_item' ) );

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'admin_enqueue_scripts' ) );
    add_action( 'admin_head' , array( __CLASS__ , 'hidden_default_menus' ) , 1000 );
    add_action( 'adminmenu' , array( __CLASS__ , 'render_menus' ) );
    add_action( 'admin_body_class' , array( __CLASS__ , 'custom_ui_body_class' ) );
    add_action( 'in_admin_header' , array( __CLASS__ , 'custom_menu_ui_mask' ) );
    add_filter( 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids' , array( __CLASS__ , 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids' ) , 9 , 5 );

  }

  public static function mywp_controller_admin_sidebar_get_sidebar( $posts ) {

    if( empty( $posts ) ) {

      return $posts;

    }

    foreach( $posts as $key => $sidebar_item ) {

      if( $sidebar_item->item_default_parent_id === 'mywp' ) {

        unset( $posts[ $key ] );

      }

    }

    return $posts;

  }

  public static function mywp_controller_admin_sidebar_get_sidebar_item( $sidebar_items ) {

    if( empty( $sidebar_items ) ) {

      return $sidebar_items;

    }

    $default_siodebar = MywpAdminSidebar::get_default_sidebar();

    if( empty( $default_siodebar['submenu'] ) ) {

      return $sidebar_items;

    }

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( $sidebar_item->item_default_type !== 'menu' ) {

        continue;

      }

      if( $sidebar_item->item_default_id === 'mywp' ) {

        if( empty( $default_siodebar['submenu']['mywp'] ) ) {

          continue;

        }

        $found_mywp_child_menu_count = 0;

        foreach( $default_siodebar['submenu']['mywp'] as $mywp_submenu ) {

          $menu_setting_id  = sprintf( '%s.%d' , $key , $found_mywp_child_menu_count );

          $sidebar_items[ $menu_setting_id ] = (object) array(
            'ID' => $menu_setting_id,
            'item_parent' => $sidebar_item->ID,
            'item_type' => 'default',
            'item_default_id' => $mywp_submenu[2],
            'item_default_parent_id' => 'mywp',
            'item_default_title' => $mywp_submenu[0],
            'item_link_title' => $mywp_submenu[0],
            'item_link_url' => add_query_arg( array( 'page' => $mywp_submenu[2] ) , admin_url( 'admin.php' ) ),
            'item_capability' => $mywp_submenu[1],
          );

          ++$found_mywp_child_menu_count;

        }

      }

    }

    return $sidebar_items;

  }

  private static function get_sidebar() {

    if( ! empty( self::$sidebar ) ) {

      return self::$sidebar;

    }

    $setting_data = self::get_setting_data();

    $timeout_min = 0;

    if( ! empty( $setting_data['cache_timeout'] ) ) {

      $timeout_min = (int) $setting_data['cache_timeout'];

    }

    $mywp_transient = new MywpTransient( 'admin_sidebar_get_sidebar' , 'controller' );

    if( ! empty( $timeout_min ) ) {

      $transient_sidebar = $mywp_transient->get_data();

      if( ! empty( $transient_sidebar ) ) {

        self::$sidebar = $transient_sidebar;

        return self::$sidebar;

      }

    }

    $args = array(
      'post_status' => array( 'publish' ),
      'post_type' => 'mywp_admin_sidebar',
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

    $args = apply_filters( 'mywp_controller_admin_sidebar_get_sidebar_args' , $args );

    $posts = MywpController::get_posts( $args , self::$id );

    $sidebar = apply_filters( 'mywp_controller_admin_sidebar_get_sidebar' , $posts );

    self::$sidebar = $sidebar;

    if( ! empty( $timeout_min ) && ! empty( $sidebar ) ) {

      $sidebar_strlen = strlen( maybe_serialize( self::$sidebar ) );

      if( $sidebar_strlen < MywpHelper::get_max_allowed_packet_size() ) {

        $mywp_transient->update_data( self::$sidebar , $timeout_min * MINUTE_IN_SECONDS );

      }

    }

    return $sidebar;

  }

  private static function get_sidebar_items() {

    if( ! empty( self::$sidebar_items ) ) {

      return self::$sidebar_items;

    }

    $sidebar = self::get_sidebar();

    if( empty( $sidebar ) ) {

      return false;

    }

    $sidebar_items = array();

    foreach( $sidebar as $key => $sidebar_item ) {

      if( $sidebar_item->item_type === 'default') {

        $sidebar_item = MywpAdminSidebar::default_item_convert( $sidebar_item );

        if( ! empty( $sidebar_item ) ) {

          $sidebar_items[] = $sidebar_item;

        }

      } else {

        $sidebar_items[] = $sidebar_item;

      }

    }

    $sidebar_items = apply_filters( 'mywp_controller_admin_sidebar_get_sidebar_item' , $sidebar_items );

    if( empty( $sidebar_items ) ) {

      return false;

    }

    ksort( $sidebar_items , SORT_NUMERIC );

    self::$sidebar_items = $sidebar_items;

    return $sidebar_items;

  }

  private static function get_sidebar_items_added_classes(){

    if( ! empty( self::$sidebar_items_added_classes ) ) {

      return self::$sidebar_items_added_classes;

    }

    $sidebar_items = self::get_sidebar_items();

    if( empty( $sidebar_items ) ) {

      return false;

    }

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( ! is_object( $sidebar_item ) ) {

        unset( $sidebar_items[ $key ] );

        continue;

      }

      if( empty( $sidebar_item->item_li_class ) ) {

        $sidebar_items[ $key ]->item_li_class = '';

      }

      if( empty( $sidebar_item->item_link_class ) ) {

        $sidebar_items[ $key ]->item_link_class = '';

      }

      if( empty( $sidebar_item->item_link_url ) ) {

        $sidebar_items[ $key ]->item_link_url = '';

      }

      if( empty( $sidebar_item->item_link_url_parse ) ) {

        $sidebar_items[ $key ]->item_link_url_parse = array();

      }

      if( empty( $sidebar_item->item_link_url_parse_query ) ) {

        $sidebar_items[ $key ]->item_link_url_parse_query = array();

      }

      $sidebar_item->item_link_url = do_shortcode( $sidebar_item->item_link_url );

      $sidebar_items[ $key ]->item_link_url = $sidebar_item->item_link_url;

      if( ! empty( $sidebar_item->item_link_url ) ) {

        $item_link_url_parse = parse_url( $sidebar_item->item_link_url );

        if( isset( $item_link_url_parse['fragment'] ) ) {

          unset( $item_link_url_parse['fragment'] );

        }

        if( empty( $item_link_url_parse['query'] ) ) {

          $item_link_url_parse['query'] = '';

        }

        $sidebar_items[ $key ]->item_link_url_parse = $item_link_url_parse;

        if( ! empty( $item_link_url_parse['query'] ) ) {

          wp_parse_str( $item_link_url_parse['query'] , $item_link_url_parse_query );

          ksort( $item_link_url_parse_query );

          $sidebar_items[ $key ]->item_link_url_parse_query = $item_link_url_parse_query;

        }

      }

    }

    foreach( $sidebar_items as $key => $sidebar_item ) {

      self::$child_items[ $sidebar_item->item_parent ][] = $sidebar_item;

    }

    $tmp = $sidebar_items;

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( empty( $sidebar_item->item_parent ) ) {

        continue;

      }

      foreach( $tmp as $tmp_key => $tmp_sidebar_item ) {

        if( (string) $tmp_sidebar_item->ID === (string) $sidebar_item->item_parent ) {

          self::$parent_items[ $sidebar_item->ID ][] = $tmp_sidebar_item;

          break;

        }

      }

    }

    unset( $tmp );

    $first = true;

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( $first ) {

        $sidebar_items[ $key ]->item_li_class .= ' wp-first-item';
        $sidebar_items[ $key ]->item_link_class .= ' wp-first-item';
        $first = false;

      }

      if( $sidebar_item->item_type === 'separator' ) {

        continue;

      }

      if( empty( $sidebar_item->item_parent ) ) {

        $sidebar_items[ $key ]->item_li_class .= ' menu-top';
        $sidebar_items[ $key ]->item_link_class .= ' menu-top';

      }

      if( ! empty( self::$child_items[ $sidebar_item->ID ] ) ) {

        $sidebar_items[ $key ]->item_li_class .= ' wp-has-submenu';
        $sidebar_items[ $key ]->item_link_class .= ' wp-has-submenu';

      }

    }

    $current_url = self::get_current_url();

    $current_url_parse = parse_url( $current_url );
    $current_url_query = array();

    if( isset( $current_url_parse['fragment'] ) ) {

      unset( $current_url_parse['fragment'] );

    }

    if( ! empty( $current_url_parse['query'] ) ) {

      wp_parse_str( $current_url_parse['query'] , $current_url_query );

      ksort( $current_url_query );

    } else {

      $current_url_parse['query'] = '';

    }

    $found_current_item_ids = array();

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( empty( $sidebar_item->item_link_url_parse['host'] ) or empty( $sidebar_item->item_link_url_parse['path'] ) ) {

        continue;

      }

      if(
        $current_url_parse['scheme'] === $sidebar_item->item_link_url_parse['scheme'] &&
        $current_url_parse['host'] === $sidebar_item->item_link_url_parse['host'] &&
        $current_url_parse['path'] === $sidebar_item->item_link_url_parse['path'] &&
        $current_url_query === $sidebar_item->item_link_url_parse_query
        ) {

        $found_current_item_ids[] = $sidebar_item->ID;

      }

    }

    if( empty( $found_current_item_ids ) ) {

      $identification_query = array();

      if( ! empty( $current_url_query['page'] ) ) {

        $identification_query['page'] = $current_url_query['page'];

      }

      if( ! empty( $current_url_query['post_type'] ) ) {

        if( $current_url_query['post_type'] !== 'post' ) {

          $identification_query['post_type'] = $current_url_query['post_type'];

        }

      }

      if( ! empty( $current_url_query['taxonomy'] ) ) {

        $identification_query['taxonomy'] = $current_url_query['taxonomy'];

      }

      foreach( $sidebar_items as $key => $sidebar_item ) {

        if( empty( $sidebar_item->item_link_url_parse['host'] ) or empty( $sidebar_item->item_link_url_parse['path'] ) ) {

          continue;

        }

        if(
          $current_url_parse['scheme'] === $sidebar_item->item_link_url_parse['scheme'] &&
          $current_url_parse['host'] === $sidebar_item->item_link_url_parse['host'] &&
          $current_url_parse['path'] === $sidebar_item->item_link_url_parse['path'] &&
          http_build_query( $identification_query ) === $sidebar_item->item_link_url_parse['query']
        ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      }

    }

    $found_current_item_ids = apply_filters( 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids' , $found_current_item_ids , $sidebar_items , $current_url , $current_url_parse , $current_url_query );

    if( ! empty( $found_current_item_ids ) ) {

      $found_current_item_ids = array_map( 'strip_tags' , $found_current_item_ids );

    }

    if( ! empty( $found_current_item_ids ) ) {

      foreach( $sidebar_items as $key => $sidebar_item ) {

        if( in_array( $sidebar_item->ID , $found_current_item_ids ) ) {

          $sidebar_items[ $key ]->item_li_class .= ' current';
          $sidebar_items[ $key ]->item_link_class .= ' current';

        }

      }

      foreach( $found_current_item_ids as $found_current_item_id ) {

        $find_item_parents_ids = self::get_find_item_to_parent_ids( $found_current_item_id );

        if( ! empty( $find_item_parents_ids ) ) {

          foreach( $sidebar_items as $key => $sidebar_item ) {

            if( ! in_array( $sidebar_item->ID , $find_item_parents_ids ) ) {

              continue;

            }

            $sidebar_items[ $key ]->item_li_class .= ' wp-has-current-submenu wp-menu-open';
            $sidebar_items[ $key ]->item_link_class .= ' wp-has-current-submenu wp-menu-open';

          }

        }

      }

    }

    $sidebar_items = apply_filters( 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes' , $sidebar_items );

    self::$sidebar_items_added_classes = $sidebar_items;

    return self::$sidebar_items_added_classes;

  }

  public static function mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids( $found_current_item_ids , $sidebar_items , $current_url , $current_url_parse , $current_url_query ) {

    if( empty( $sidebar_items ) ) {

      return $sidebar_items;

    }

    if( ! empty( $found_current_item_ids ) ) {

      return $found_current_item_ids;

    }

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( empty( $sidebar_item->item_link_url_parse['host'] ) or empty( $sidebar_item->item_link_url_parse['path'] ) ) {

        continue;

      }

      if(
        $current_url_parse['scheme'] !== $sidebar_item->item_link_url_parse['scheme'] or
        $current_url_parse['host'] !== $sidebar_item->item_link_url_parse['host']
        ) {

        continue;

      }

      if( strpos( $current_url_parse['path'] , 'post.php' ) !== false && ! empty( $current_url_query['post'] ) ) {

        if( strpos( $sidebar_item->item_link_url_parse['path'] , 'edit.php' ) === false ) {

          continue;

        }

        $post_type = get_post_type( (int) $current_url_query['post'] );

        if( empty( $post_type ) ) {

          continue;

        }

        if( $post_type === 'post' && empty( $sidebar_item->item_link_url_parse_query['post_type'] ) ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        } elseif( ! empty( $sidebar_item->item_link_url_parse_query['post_type'] ) && $sidebar_item->item_link_url_parse_query === array( 'post_type' => $post_type ) ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      } elseif( strpos( $current_url_parse['path'] , 'term.php' ) !== false && $current_url_query['taxonomy'] ) {

        if( strpos( $sidebar_item->item_link_url_parse['path'] , 'edit-tags.php' ) === false ) {

          continue;

        }

        if( (string) $current_url_query['taxonomy'] === (string) $sidebar_item->item_link_url_parse_query['taxonomy'] ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      } elseif( strpos( $current_url_parse['path'] , 'comment.php' ) !== false ) {

        if( strpos( $sidebar_item->item_link_url_parse['path'] , 'edit-comments.php' ) === false ) {

          continue;

        }

        if( $sidebar_item->item_link_url_parse_query === array() ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      } elseif( strpos( $current_url_parse['path'] , 'theme-install.php' ) !== false ) {

        if( strpos( $sidebar_item->item_link_url_parse['path'] , 'themes.php' ) === false ) {

          continue;

        }

        if( $sidebar_item->item_link_url_parse_query === array() ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      } elseif( strpos( $current_url_parse['path'] , 'user-edit.php' ) !== false ) {

        if( strpos( $sidebar_item->item_link_url_parse['path'] , 'users.php' ) === false ) {

          continue;

        }

        if( $sidebar_item->item_link_url_parse_query === array() ) {

          $found_current_item_ids[] = $sidebar_item->ID;

        }

      }

    }

    return $found_current_item_ids;

  }

  private static function get_find_item_to_parent_ids( $find_id = false ) {

    if( empty( $find_id ) ) {

      return false;

    }

    $find_id = strip_tags( $find_id );

    self::set_find_item_parent_ids( $find_id );

    return self::$found_parent_item_ids;

  }

  private static function set_find_item_parent_ids( $find_id ) {

    if( empty( $find_id ) ) {

      return false;

    }

    if( empty( self::$parent_items[ $find_id ] ) ) {

      return false;

    }

    $parent_items = self::$parent_items[ $find_id ];

    foreach( $parent_items as $parent_item ) {

      self::$found_parent_item_ids[] = $parent_item->ID;

      if( ! empty( $parent_item->item_parent ) ) {

        self::set_find_item_parent_ids( $parent_item->ID );

      }

    }

  }

  private static function get_find_menu_items_to_parent_id( $parent_id = 0 ) {

    $sidebar_items_added_classes = self::get_sidebar_items_added_classes();

    if( empty( $sidebar_items_added_classes ) ) {

      return false;

    }

    if( ! empty( $parent_id ) ) {

      if( is_numeric( $parent_id ) ) {

        $parent_id = (int) $parent_id;

      } else {

        $parent_id = strip_tags( $parent_id );

      }

    }

    if( ! empty( self::$find_parent_id[ $parent_id ] ) ) {

      return self::$find_parent_id[ $parent_id ];

    }

    $find_items = array();

    foreach( $sidebar_items_added_classes as $item ) {

      $item_parent = $item->item_parent;

      if( ! empty( $item_parent ) ) {

        if( is_numeric( $item_parent ) ) {

          $item_parent = (int) $item_parent;

        } else {

          $item_parent = strip_tags( $item_parent );

        }

      }

      if( $item_parent !== $parent_id ) {

        continue;

      }

      $find_items[] = $item;

    }

    if( empty( $find_items ) ) {

      return false;

    }

    self::$find_parent_id[ $parent_id ] = $find_items;

    return $find_items;

  }

  private static function get_current_url() {

    if( ! empty( self::$current_url ) ) {

      return self::$current_url;

    }

    $current_url = urldecode( do_shortcode( '[mywp_url current="1"]' ) );

    self::$current_url = $current_url;

    return $current_url;

  }

  public static function admin_enqueue_scripts() {

    $sidebar = self::get_sidebar();

    if( empty( $sidebar ) ) {

      return false;

    }

    wp_register_style( 'mywp_admin_sidebar' , MywpApi::get_plugin_url( 'css' ) . 'admin-sidebar.css' , array() , MYWP_VERSION );
    wp_register_script( 'mywp_admin_sidebar' , MywpApi::get_plugin_url( 'js' ) . 'admin-sidebar.js' , array( 'jquery' ) , MYWP_VERSION );

    wp_enqueue_style( 'mywp_admin_sidebar' );
    wp_enqueue_script( 'mywp_admin_sidebar' );

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['custom_menu_ui'] ) ) {

      wp_register_style( 'mywp_admin_sidebar_custom_ui' , MywpApi::get_plugin_url( 'css' ) . 'admin-sidebar-custom-ui.css' , array( 'mywp_admin_sidebar' ) , MYWP_VERSION );
      wp_register_script( 'mywp_admin_sidebar_custom_ui' , MywpApi::get_plugin_url( 'js' ) . 'admin-sidebar-custom-ui.js' , array( 'jquery' , 'mywp_admin_sidebar' ) , MYWP_VERSION );

      wp_enqueue_style( 'mywp_admin_sidebar_custom_ui' );
      wp_enqueue_script( 'mywp_admin_sidebar_custom_ui' );

    }

  }

  public static function hidden_default_menus() {

    global $menu;
    global $submenu;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $sidebar = self::get_sidebar();

    if( empty( $sidebar ) ) {

      return false;

    }

    $menu = array();
    $submenu = array();

    self::after_do_function( __FUNCTION__ );

  }

  public static function render_menus() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $parent_items = self::get_find_menu_items_to_parent_id();

    if( empty( $parent_items ) ) {

      return false;

    }

    foreach( $parent_items as $item ) {

      self::print_sidebar_item( $item );

    }

    $collapse_item_html = apply_filters( 'mywp_controller_admin_sidebar_collapse_item_html' , '<li id="sidebar-collapse"><span class="collapse-button-icon dashicons-before"></span><span class="collapse-button-label">' . __( 'Collapse menu' ) . '</span></li>' );

    echo $collapse_item_html;

    self::after_do_function( __FUNCTION__ );

  }

  private static function print_sidebar_item( $item ) {

    if( empty( $item ) or empty( $item->item_type ) or empty( $item->ID ) ) {

      return false;

    }

    $item = apply_filters( 'mywp_controller_admin_sidebar_print_sidebar_item' , $item );

    if( ! empty( $item->item_capability ) ) {

      if( ! current_user_can( $item->item_capability ) ) {

        return false;

      }

    }

    $item_id = $item->ID;

    if( is_numeric( $item_id ) ) {

      $item_id = (int) $item_id;

    } else {

      $item_id = strip_tags( $item_id );

    }


    $item_type = $item->item_type;
    $item_parent = $item->item_parent;

    $item->item_link_title = do_shortcode( $item->item_link_title );

    $li_class = '';

    if( ! empty( $item->item_li_class ) ) {

      $li_class = $item->item_li_class;

    }

    $li_id = '';

    if( ! empty( $item->item_li_id ) ) {

      $li_id = $item->item_li_id;

    }

    printf( '<li class="mywp-sidebar-item item-%s item-type-%s %s" id="%s">' , esc_attr( $item_id ) , esc_attr( $item_type ) , esc_attr( $li_class ) , esc_attr( $li_id ) );

    if( $item_type === 'custom' ) {

      echo wp_kses_post( do_shortcode( $item->item_custom_html ) );

    } elseif( $item_type === 'separator' ) {

      echo '<div class="separator"></div>';

    } elseif( in_array( $item_type , array( 'default' , 'link' ) ) ) {

      $link_class = '';

      if( ! empty( $item->item_link_class ) ) {

        $link_class = $item->item_link_class;

      }

      $link_id = '';

      if( ! empty( $item->item_link_id ) ) {

        $link_id = $item->item_link_id;

      }

      $link_attr = '';

      if( ! empty( $item->item_link_attr ) ) {

        $link_attr = $item->item_link_attr;

      }

      printf( '<a href="%s" class="mywp-sidebar-item-link %s" id="%s" %s>' , esc_url( $item->item_link_url ) , esc_attr( $link_class ) , esc_attr( $link_id ) , esc_attr( $link_attr ) );

      $icon_class = '';

      if( ! empty( $item->item_icon_class ) ) {

        $icon_class = $item->item_icon_class;

      }

      $icon_id = '';

      if( ! empty( $item->item_icon_id ) ) {

        $icon_id = $item->item_icon_id;

      }

      $icon_title = '';

      if( ! empty( $item->item_icon_title ) ) {

        $icon_title = $item->item_icon_title;

      }

      $icon_style = '';

      if( ! empty( $item->item_icon_style ) ) {

        $icon_style = $item->item_icon_style;

      }

      $icon_img = '';

      if( ! empty( $item->item_icon_img ) ) {

        $icon_img = $item->item_icon_img;

      }

      if( ! empty( $item->item_icon_img ) ) {

        printf( '<div class="wp-menu-image dashicons-before mywp-sidebar-item-icon-img"><img src="%s" alt="%s"></div>' , esc_attr( $icon_img ) , esc_attr( $icon_title ) );

      } elseif( ! empty( $icon_class ) or ! empty( $icon_style ) or ! empty( $icon_id ) ) {

        printf( '<div class="wp-menu-image mywp-sidebar-item-icon %s" id="%s" style="%s">%s</div>'  , esc_attr( $icon_class ) , esc_attr( $icon_id ) , esc_attr( $icon_style ) , wp_kses_post( $icon_title ) );

      } else {

          echo do_action( 'mywp_controller_admin_sidebar_print_sidebar_item_icon' , $item );

      }

      printf( '<div class="wp-menu-name mywp-sidebar-name">%s</div>' , wp_kses_post( $item->item_link_title ) );

      echo '</a>';

      $child_items = self::get_find_menu_items_to_parent_id( $item_id );

      if( ! empty( $child_items ) ) {

        echo '<ul class="wp-submenu wp-submenu-wrap mywp-sidebar-item-childs">';

        printf( '<li class="wp-submenu-head" aria-hidden="true">%s</li>' , wp_kses_post( $item->item_link_title ) );

        foreach( $child_items as $child_item ) {

          self::print_sidebar_item( $child_item );

        }

        echo '</ul>';

      }

    }

    echo '</li>';

  }

  public static function custom_ui_body_class( $admin_body_class ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $admin_body_class;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['custom_menu_ui'] ) ) {

      return $admin_body_class;

    }

    $admin_body_class .= ' mywp-admin-sidebar-custom-menu-ui ';

    self::after_do_function( __FUNCTION__ );

    return $admin_body_class;

  }

  public static function custom_menu_ui_mask() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['custom_menu_ui'] ) ) {

      return false;

    }

    echo '<div id="sidebar-custom-menu-ui-mask"></div>';

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminSidebar::init();

endif;
