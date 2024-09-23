<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAdminSidebar' ) ) :

final class MywpAdminSidebar {

  private static $default_sidebar;

  public static function init() {

    add_action( 'mywp_wp_loaded' , array( __CLASS__ , 'mywp_wp_loaded' ) );

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    add_action( 'admin_head' , array( __CLASS__ , 'set_default_sidebar' ) , 999 );

  }

  public static function set_default_sidebar() {

    global $menu;
    global $submenu;

    if( empty( $menu ) or empty( $submenu ) ) {

      return false;

    }

    $current_menus = $menu;
    $current_submenus = $submenu;

    $remove_classes = array( 'menu-top-first' , 'menu-top-last' , 'menu-top' );

    foreach( $current_menus as $key => $current_menu ) {

      $current_menu[2] = urldecode( html_entity_decode( $current_menu[2] ) );
      $current_menus[ $key ][2] = $current_menu[2];

      $current_menus[ $key ][2] = self::get_remove_return_url( $current_menu[2] );

      if( empty( $current_menu[4] ) ) {

        continue;

      }

      $changed_class = false;

      foreach( $remove_classes as $remove_class ) {

        if( strpos( $current_menu[4] , "{$remove_class}" ) !== false ) {

          $current_menu[4] = str_replace( "{$remove_class}" , '' , $current_menu[4] );

          $changed_class = true;

        }

      }

      if( $changed_class ) {

        $current_menus[ $key ][4] = preg_replace( '/\s(?=\s)/' , '' , $current_menu[4] );

      }

      $current_menus[ $key ][4] = ltrim( $current_menus[ $key ][4] );
      $current_menus[ $key ][4] = rtrim( $current_menus[ $key ][4] );

    }

    foreach( $current_submenus as $parent_id => $current_submenus_arr ) {

      foreach( $current_submenus_arr as $key => $current_submenu ) {

        $current_submenu[2] = urldecode( html_entity_decode( $current_submenu[2] ) );

        $current_submenus[ $parent_id ][ $key ][2] = $current_submenu[2];

        $current_submenus[ $parent_id ][ $key ][2] = self::get_remove_return_url( $current_submenu[2] );

      }

    }

    $current_menus = apply_filters( 'mywp_admin_sidebar_set_default_sidebar_menus' , $current_menus );
    $current_submenus = apply_filters( 'mywp_admin_sidebar_set_default_sidebar_submenu' , $current_submenus );

    self::$default_sidebar = array( 'menu' => $current_menus , 'submenu' => $current_submenus  );

  }

  public static function get_default_sidebar() {

    return self::$default_sidebar;

  }

  private static function get_remove_return_url( $url = false ) {

    if( empty( $url ) ) {

      return $url;

    }

    if( strpos( $url , 'customize.php' ) === false or strpos( $url , 'return=' ) === false ) {

      return $url;

    }

    $return_url = sprintf( '?return=%s' , wp_unslash( $_SERVER['REQUEST_URI'] ) );

    if( strpos( $url , $return_url . '&' ) !== false ) {

      $url = str_replace( $return_url . '&' , '?' , $url );

    } elseif( strpos( $url , $return_url ) !== false ) {

      $url = str_replace( $return_url , '' , $url );

    }

    return $url;

  }

  private static function find_default_menu( $find_id = false , $find_parent_id = false ) {

    if( empty( $find_id ) ) {

      return false;

    }

    $default_sidebar = self::get_default_sidebar();

    if( empty( $default_sidebar['menu'] ) ) {

      return false;

    }

    $find_id = strip_tags( do_shortcode( $find_id ) );
    $find_parent_id = strip_tags( $find_parent_id );

    $found_current_default = false;
    $found_parent_default = false;
    $found_childs_default = false;

    if( ! empty( $find_parent_id ) ) {

      if( empty( $default_sidebar['submenu'][ $find_parent_id ] ) ) {

        return false;

      }

      foreach( $default_sidebar['menu'] as $key => $menu ) {

        if( ! isset( $menu[2] ) ) {

          continue;

        }

        if( $menu[2] !== $find_parent_id ) {

          continue;

        }

        $found_parent_default = $menu;

        break;

      }

      if( empty( $found_parent_default ) ) {

        return false;

      }

      foreach( $default_sidebar['submenu'][ $find_parent_id ] as $key => $submenu ) {

        if( ! isset( $submenu[2] ) ) {

          continue;

        }

        if( $submenu[2] !== $find_id ) {

          continue;

        }

        $found_current_default = $submenu;

        break;

      }

    } else {

      foreach( $default_sidebar['menu'] as $key => $menu ) {

        if( ! isset( $menu[2] ) ) {

          continue;

        }

        if( $menu[2] !== $find_id ) {

          continue;

        }

        $found_current_default = $menu;

        break;

      }

      if( ! empty( $found_current_default ) && ! empty( $default_sidebar['submenu'][ $find_id ] ) ) {

        $found_childs_default = $default_sidebar['submenu'][ $find_id ];

      }

    }

    return array( 'current' => $found_current_default , 'parent' => $found_parent_default , 'childs' => $found_childs_default );

  }

  private static function is_third_party_menu( $current_menu_id = false , $parent_menu_id = false ) {

    if( empty( $current_menu_id ) ) {

      return false;

    }

    $current_menu_id = strip_tags( $current_menu_id );

    if( in_array( $current_menu_id , array( 'index.php' ) ) ) {

      return false;

    }

    if( empty( $parent_menu_id ) ) {

      $parent_menu_id = 'admin.php';

    }

    $parent_menu_id = strip_tags( $parent_menu_id );

    $menu_hook = get_plugin_page_hook( $current_menu_id , $parent_menu_id );

    $menu_file = $current_menu_id;

    $pos = strpos( $menu_file , '?' );

    if ( false !== $pos ) {

      $menu_file = substr( $menu_file, 0, $pos );

    }

    if( ! empty( $menu_hook ) ) {

      return true;

    } elseif( file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! file_exists( ABSPATH . "/wp-admin/$menu_file" ) ) {

      return true;

    }

    return false;

  }

  public static function default_item_convert( $item = false ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$item' );

    if( empty( $item ) or ! is_object( $item ) ) {

      MywpHelper::error_not_found_message( '$item' , $called_text );

      return false;

    }

    if( empty( $item->item_type ) ) {

      return false;

    }

    if( $item->item_type !== 'default') {

      return false;

    }

    if( empty( $item->item_default_type ) ) {

      return false;

    }

    if( empty( $item->item_default_id ) && empty( $item->item_default_parent_id ) ) {

      return false;

    }

    $default_menu = self::find_default_menu( $item->item_default_id , $item->item_default_parent_id );

    if( empty( $default_menu['current'] ) ) {

      return false;

    }

    $item->item_default_title = $default_menu['current'][0];
    $item->item_capability = $default_menu['current'][1];

    if( empty( $default_menu['parent'] ) ) {

      if( ! empty( $default_menu['childs'] ) ) {

        $child_menus = array_values( $default_menu['childs'] );

        $menu_slug = $child_menus[0][2];

        $is_third_party_menu = self::is_third_party_menu( $menu_slug , $default_menu['current'][2] );

      } else {

        $menu_slug = $default_menu['current'][2];

        $is_third_party_menu = self::is_third_party_menu( $menu_slug );

      }

      if( $is_third_party_menu ) {

        $item->item_link_url = add_query_arg( array( 'page' => $menu_slug ) , admin_url( 'admin.php' ) );

      } elseif( strpos( $menu_slug , 'http' ) !== false ) {

        $item->item_link_url = $menu_slug;

      } else {

        $item->item_link_url = admin_url( $menu_slug );

      }

    } else {

      $menu_slug = $default_menu['current'][2];

      $is_third_party_menu = self::is_third_party_menu( $menu_slug , $default_menu['parent'][2] );

      if( $is_third_party_menu ) {

        $menu_file = $default_menu['parent'][2];

        $pos = strpos( $menu_file , '?' );

        if ( false !== $pos ) {

          $menu_file = substr( $menu_file, 0, $pos );

        }

        $found_menu = self::find_default_menu( $default_menu['parent'][2] );

        if( ! empty( $found_menu['childs'] ) ) {

          $child_menus = array_values( $found_menu['childs'] );

          $admin_is_parent = self::is_third_party_menu( $child_menus[0][2] , $found_menu['current'][2] );

        } else {

          $admin_is_parent = self::is_third_party_menu( $found_menu['current'][2] );

        }

        if (
          (
            ! $admin_is_parent &&
            file_exists( WP_PLUGIN_DIR . "/$menu_file" ) &&
            ! is_dir( WP_PLUGIN_DIR . "/{$default_menu['parent'][2]}" )
          )
          ||
          file_exists( $menu_file )
        ) {

          $item->item_link_url = add_query_arg( array( 'page' => $menu_slug ) , admin_url( $default_menu['parent'][2] ) );

        } else {

          $item->item_link_url = add_query_arg( array( 'page' => $menu_slug ) , admin_url( 'admin.php' ) );

        }


      } elseif( strpos( $menu_slug , 'http' ) !== false ) {

        $item->item_link_url = $menu_slug;

      } else {

        $item->item_link_url = admin_url( $menu_slug );

      }

    }

    if( ! empty( $default_menu['current'][4] ) ) {

      $item->item_li_class = $item->item_link_class = $default_menu['current'][4];

      if( strpos( $default_menu['current'][4] , 'wp-menu-separator' ) !== false ) {

        $item->item_type = 'custom';
        $item->item_custom_html = '<div class="separator"></div>';
        $item->item_li_class = '';
        $item->item_li_id = '';
        $item->item_link_url = '';

        return $item;

      }

    }

    $customizer_url = admin_url( 'customize.php' );

    if( strpos( $item->item_link_url , $customizer_url ) !== false && strpos( $item->item_link_url , 'return' ) === false ) {

      $param_str = false;

      if( strpos( $item->item_link_url , $customizer_url . '?' ) !== false ) {

        $param_str = str_replace( $customizer_url . '?' , '&' , $item->item_link_url );

      }

      $url = urldecode( add_query_arg( array( 'return' => wp_unslash( $_SERVER['REQUEST_URI'] ) ) , $customizer_url ) );

      $item->item_link_url = $url . $param_str;

    }

    if( ! empty( $default_menu['current'][5] ) ) {

      $item->item_li_id = preg_replace( '|[^a-zA-Z0-9_:.]|' , '-' , $default_menu['current'][5] );

    }

    if( ! empty( $default_menu['current'][6] ) ) {

      if( strpos( $default_menu['current'][6] , 'data:image/svg+xml;base64,' ) ) {

        $item->item_icon_class = ' svg';
        $item->item_icon_style = sprintf( ' background-image: url( "%s" );' , esc_attr( $default_menu['current'][6] ) );

      } elseif( strpos( $default_menu['current'][6] , 'dashicons-' ) !== false ) {

        //$item->item_icon_class = ' dashicons-before ' . $default_menu['current'][6];

      } elseif( $default_menu['current'][6] === 'none' or $default_menu['current'][6] === 'div' ) {

        $item->item_icon_class = ' dashicons-before ';

      } else {

        $item->item_icon_img = $default_menu['current'][6];

      }

    }

    return apply_filters( 'mywp_admin_sidebar_default_item_convert' , $item );

  }

}

MywpAdminSidebar::init();

endif;
