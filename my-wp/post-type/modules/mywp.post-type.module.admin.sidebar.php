<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpPostTypeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpPostTypeModuleAdminSidebar' ) ) :

final class MywpPostTypeModuleAdminSidebar extends MywpPostTypeAbstractModule {

  protected static $id = 'mywp_admin_sidebar';

  protected static function get_regist_post_type_args() {

    $args = array(
      'label' => 'My WP Admin Sidebar',
      'hierarchical' => true,
      'supports' => array( 'title' , 'page-attributes' , 'custom-fields' ),
    );

    return $args;

  }

  public static function current_mywp_post_type_get_post( $post ) {

    $post_id = $post->ID;

    $post->item_parent = $post->post_parent;

    $post->item_type = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_type' ) );

    $post->item_default_type = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_default_type' ) );
    $post->item_default_id = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_default_id' ) );
    $post->item_default_parent_id = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_default_parent_id' ) );
    $post->item_default_title = '';

    $post->item_capability = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_capability' ) );
    $post->item_custom_html = wp_unslash( MywpPostType::get_post_meta( $post_id , 'item_custom_html' ) );

    $post->item_li_class = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_li_class' ) );
    $post->item_li_id = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_li_id' ) );

    $post->item_link_class = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_link_class' ) );
    $post->item_link_id = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_link_id' ) );
    $post->item_link_title = wp_unslash( MywpPostType::get_post_meta( $post_id , 'item_link_title' ) );
    $post->item_link_url = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_link_url' ) );
    $post->item_link_attr = wp_unslash( strip_tags( MywpPostType::get_post_meta( $post_id , 'item_link_attr' ) ) );

    $post->item_icon_class = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_icon_class' ) );
    $post->item_icon_id = strip_tags( MywpPostType::get_post_meta( $post_id , 'item_icon_id' ) );
    $post->item_icon_title = wp_unslash( MywpPostType::get_post_meta( $post_id , 'item_icon_title' ) );
    $post->item_icon_style = wp_unslash( MywpPostType::get_post_meta( $post_id , 'item_icon_style' ) );
    $post->item_icon_img = MywpPostType::get_post_meta( $post_id , 'item_icon_img' );

    return $post;

  }

  public static function current_manage_posts_columns( $posts_columns ) {

    $old_columns = $posts_columns;

    $posts_columns = array();

    $posts_columns['cb'] = $old_columns['cb'];
    $posts_columns['title'] = $old_columns['title'];
    $posts_columns['order'] = 'Order';
    $posts_columns['id'] = 'ID';
    $posts_columns['parent'] = 'Parent';
    $posts_columns['type'] = 'Type';
    $posts_columns['menu_title'] = 'Menu Title';
    $posts_columns['info'] = 'Info';

    return $posts_columns;

  }

  public static function current_manage_posts_custom_column( $column_name , $post_id ) {

    $mywp_post = MywpPostType::get_post( $post_id );

    if( empty( $mywp_post ) ) {

      return false;

    }

    if( $column_name === 'order' ) {

      if( $mywp_post->menu_order ) {

        echo $mywp_post->menu_order;

      }

    } elseif( $column_name === 'id' ) {

      if( $mywp_post->ID ) {

        echo $mywp_post->ID;

      }

    } elseif( $column_name === 'parent' ) {

      if( $mywp_post->item_parent ) {

        echo $mywp_post->item_parent;

      }

    } elseif( $column_name === 'type' ) {

      if( $mywp_post->item_type ) {

        echo $mywp_post->item_type;

      }

    } elseif( $column_name === 'menu_title' ) {

      if( $mywp_post->item_link_title ) {

        echo $mywp_post->item_link_title;

      }

    } elseif( $column_name === 'info' ) {

      printf( '<textarea class="large-text" readonly="readonly">%s</textarea>' , print_r( $mywp_post , true ) );

    }

  }

}

MywpPostTypeModuleAdminSidebar::init();

endif;
