<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSettingBlockEditor' ) ) :

final class MywpSettingBlockEditor {

  private static $current_block_editor_screen_id;

  private static $current_block_editor_panels;

  private static $current_block_editor_panels_setting_data;

  private static $is_use_block_editor;

  public static function get_setting_block_editor_panels() {

    $selectable_post_types = MywpSettingPostType::get_setting_post_types();

    $taxonomies = get_taxonomies( array( 'show_ui' => true , 'show_in_rest' => true ) , 'objects' );

    $setting_block_editor_panels = array();

    foreach( $selectable_post_types as $post_type => $post_type_obj ) {

      $setting_block_editor_panels[ $post_type ]['post-status'] = array(
        'id' => 'post-status',
        'title' => __( 'Overview' )
      );

      if ( post_type_supports( $post_type , 'revisions' ) ) {

        $setting_block_editor_panels[ $post_type ]['last-revision'] = array(
          'id' => 'last-revision',
          'title' => __( 'Revision' ),
        );

      }

      $setting_block_editor_panels[ $post_type ]['post-link'] = array(
        'id' => 'post-link',
        'title' => __( 'Permalinks' )
      );

    }

    if( ! empty( $taxonomies ) ) {

      foreach( $taxonomies as $taxonomy ) {

        foreach( $selectable_post_types as $post_type => $post_type_obj ) {

          if( in_array( $post_type , (array) $taxonomy->object_type , true ) ) {

            $taxonomy_panel_name = 'taxonomy-panel-' . $taxonomy->name;

            $setting_block_editor_panels[ $post_type ][ $taxonomy_panel_name ] = array(
              'id' => $taxonomy_panel_name,
              'title' => $taxonomy->labels->menu_name,
            );

          }

        }

      }

    }

    foreach( $selectable_post_types as $post_type => $post_type_obj ) {

      if ( post_type_supports( $post_type , 'thumbnail' ) ) {

        $setting_block_editor_panels[ $post_type ]['featured-image'] = array(
          'id' => 'featured-image',
          'title' => __( 'Featured Images' ),
        );

      }

      if ( post_type_supports( $post_type , 'excerpt' ) ) {

        $setting_block_editor_panels[ $post_type ]['post-excerpt'] = array(
          'id' => 'post-excerpt',
          'title' => __( 'Excerpt' ),
        );

      }

      $setting_block_editor_panels[ $post_type ]['discussion-panel'] = array(
        'id' => 'discussion-panel',
        'title' => __( 'Discussion' ),
      );

      if( $post_type === 'page' ) {

        $setting_block_editor_panels[ $post_type ]['page-attributes'] = array(
          'id' => 'page-attributes',
          'title' => __( 'Page Attributes' ),
        );

      } else {

        $setting_block_editor_panels[ $post_type ]['page-attributes'] = array(
          'id' => 'page-attributes',
          'title' => __( 'Post Attributes' ),
        );

      }

    }

    return apply_filters( 'mywp_setting_block_editor_panels' , $setting_block_editor_panels );

  }

  public static function get_setting_block_editor_panel( $block_editor_screen_id = false ) {

    if( empty( $block_editor_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$block_editor_screen_id' );

      MywpHelper::error_require_message( '$block_editor_screen_id' , $called_text );

      return false;

    }

    $setting_block_editor_panels = self::get_setting_block_editor_panels();

    if( empty( $setting_block_editor_panels ) or empty( $setting_block_editor_panels[ $block_editor_screen_id ] ) ) {

      return false;

    }

    return $setting_block_editor_panels[ $block_editor_screen_id ];

  }

  public static function set_current_block_editor_screen_id( $block_editor_screen_id = false ) {

    $block_editor_screen_id = strip_tags( $block_editor_screen_id );

    self::$current_block_editor_screen_id = $block_editor_screen_id;

    self::set_current_block_editor_panels( $block_editor_screen_id );

  }

  public static function get_current_block_editor_screen_id() {

    return self::$current_block_editor_screen_id;

  }

  private static function set_current_block_editor_panels( $block_editor_screen_id = false ) {

    if( empty( $block_editor_screen_id ) ) {

      $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$block_editor_screen_id' );

      MywpHelper::error_require_message( '$block_editor_screen_id' , $called_text );

      return false;

    }

    $block_editor_panels = self::get_setting_block_editor_panel( $block_editor_screen_id );

    if( empty( $block_editor_panels ) ) {

      return false;

    }

    self::$current_block_editor_panels = $block_editor_panels;

  }

  public static function get_current_block_editor_panels() {

    return self::$current_block_editor_panels;

  }

  public static function set_current_block_editor_panels_setting_data( $block_editor_panels_setting_data = array() ) {

    if( ! is_array( $block_editor_panels_setting_data ) ) {

      $block_editor_panels_setting_data = array();

    }

    if( ! empty( $block_editor_panels_setting_data ) ) {

      foreach( $block_editor_panels_setting_data as $key => $data ) {

        if( ! isset( $data['action'] ) ) {

          unset( $block_editor_panels_setting_data[ $key ] );

        }

      }

    }

    self::$current_block_editor_panels_setting_data = $block_editor_panels_setting_data;

  }

  public static function get_current_block_editor_panels_setting_data() {

    return self::$current_block_editor_panels_setting_data;

  }

  public static function set_is_use_block_editor( $is_use_block_editor = false ) {

    self::$is_use_block_editor = $is_use_block_editor;

  }

  public static function get_is_use_block_editor() {

    return self::$is_use_block_editor;

  }

}

endif;
