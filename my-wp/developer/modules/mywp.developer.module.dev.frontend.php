<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevFrontend' ) ) :

final class MywpDeveloperModuleDevFrontend extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_frontend';

  static protected $priority = 20;

  static protected $find_templates = array();

  static protected $header_templates = array();

  static protected $sidebar_templates = array();

  static protected $footer_templates = array();

  protected static function after_init() {

    $template_types = array(
      'embed',
      '404',
      'search',
      'frontpage',
      'home',
      'taxonomy',
      'attachment',
      'single',
      'page',
      'singular',
      'category',
      'tag',
      'author',
      'date',
      'archive',
      'index',
    );

    foreach( $template_types as $template_type ) {

      add_filter( "{$template_type}_template_hierarchy" , array( __CLASS__ , 'regist_template_hierarchy' ) , 100 );

    }

    add_action( 'get_header' , array( __CLASS__ , 'regist_header_templates' ) , 100 );

    add_action( 'get_sidebar' , array( __CLASS__ , 'regist_sidebar_templates' ) , 100 );

    add_action( 'get_footer' , array( __CLASS__ , 'regist_footer_templates' ) , 100 );

  }

  public static function regist_template_hierarchy( $templates ) {

    $find_templates = $templates;

    if( is_array( $find_templates ) ) {

      foreach( $find_templates as $find_template ) {

        self::$find_templates[] = $find_template;

      }

    }

    return $templates;

  }

  public static function regist_header_templates( $name ) {

    $name = (string) $name;

    if ( '' !== $name ) {

      self::$header_templates[] = "header-{$name}.php";

    }

    self::$header_templates[] = 'header.php';

  }

  public static function regist_sidebar_templates( $name ) {

    $name = (string) $name;

    if ( '' !== $name ) {

      self::$sidebar_templates[] = "sidebar-{$name}.php";

    }

    self::$sidebar_templates[] = 'sidebar.php';

  }

  public static function regist_footer_templates( $name ) {

    $name = (string) $name;

    if ( '' !== $name ) {

      self::$footer_templates[] = "footer-{$name}.php";

    }

    self::$footer_templates[] = 'footer.php';

  }

  public static function mywp_debug_renders( $debug_renders ) {

    if( is_admin() ) {

      return $debug_renders;

    }

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Current Frontend Information' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    global $wpdb;
    global $post;
    global $template;

    $debug_lists = array(
      'is_front_page()' => is_front_page(),
      'is_home()' => is_home(),
      'is_single()' => is_single(),
      'is_page()' => is_page(),
      'is_singular()' => is_singular(),
      'is_category()' => is_category(),
      'is_tag()' => is_tag(),
      'is_tax()' => is_tax(),
      'is_author()' => is_author(),
      'is_date()' => is_date(),
      'is_archive()' => is_archive(),
      'is_search()' => is_search(),
      'is_404()' => is_404(),
      'is_attachment()' => is_attachment(),
      'is_paged()' => is_paged(),
      'is_post_type_archive()' => is_post_type_archive(),
      'is_page_template()' => is_page_template(),
      'is_embed' => is_embed(),
      'is_main_query()' => is_main_query(),
      'is_preview()' => is_preview(),
    );

    $current_template = false;

    $all_templates = array();

    if( ! empty( self::$find_templates ) ) {

      foreach( self::$find_templates as $key => $find_template ) {

        if( empty( $find_template ) ) {

          continue;

        }

        $current_theme_template = MywpApi::get_theme_path() . '/' . $find_template;
        $parent_theme_template = MywpApi::get_theme_path( true ) . '/' . $find_template;
        $wp_compat_template = ABSPATH . WPINC . '/theme-compat/' . $find_template;

        $all_templates[ $current_theme_template ] = array( 'found' => 0 );

        if( file_exists( $current_theme_template ) ) {

          $all_templates[ $current_theme_template ]['found'] = 1;

          if( empty( $current_template ) ) {

            $current_template = $current_theme_template;

          }

        }

        if( is_child_theme() ) {

          $all_templates[ $parent_theme_template ] = array( 'found' => 0 );

          if( file_exists( $parent_theme_template ) ) {

            $all_templates[ $parent_theme_template ]['found'] = 1;

            if( empty( $current_template ) ) {

              $current_template = $parent_theme_template;

            }

          }

        }

        $all_templates[ $wp_compat_template ] = array( 'found' => 0 );

        if( file_exists( $wp_compat_template ) ) {

          $all_templates[ $wp_compat_template ]['found'] = 1;

          if( empty( $current_template ) ) {

            $current_template = $wp_compat_template;

          }

        }

      }

    }

    $file_name = false;

    if( ! empty( $template ) ) {

      $file_name = basename( $template );

    }

    $debug_lists['current_template'] = sprintf( '<strong>%s</strong> (%s)' , $file_name , $current_template );

    $debug_lists['find_locate_templates'] = array();

    foreach( $all_templates as $template_file => $template_vals ) {

      $debug_lists['find_locate_templates'][ $template_file ] = $template_vals['found'];

    }

    $current_header_template = false;

    $all_header_templates = array();

    if( ! empty( self::$header_templates ) ) {

      foreach( self::$header_templates as $key => $find_header_template ) {

        if( empty( $find_header_template ) ) {

          continue;

        }

        $current_theme_header_template = MywpApi::get_theme_path() . '/' . $find_header_template;
        $parent_theme_header_template = MywpApi::get_theme_path( true ) . '/' . $find_header_template;
        $wp_compat_header_template = ABSPATH . WPINC . '/theme-compat/' . $find_header_template;

        $all_header_templates[ $current_theme_header_template ] = array( 'found' => 0 );

        if( file_exists( $current_theme_header_template ) ) {

          $all_header_templates[ $current_theme_header_template ]['found'] = 1;

          if( empty( $current_header_template ) ) {

            $current_header_template = $current_theme_header_template;

          }

        }

        if( is_child_theme() ) {

          $all_header_templates[ $parent_theme_header_template ] = array( 'found' => 0 );

          if( file_exists( $parent_theme_header_template ) ) {

            $all_header_templates[ $parent_theme_header_template ]['found'] = 1;

            if( empty( $current_header_template ) ) {

              $current_header_template = $parent_theme_header_template;

            }

          }

        }

        $all_header_templates[ $wp_compat_header_template ] = array( 'found' => 0 );

        if( file_exists( $wp_compat_header_template ) ) {

          $all_header_templates[ $wp_compat_header_template ]['found'] = 1;

          if( empty( $current_header_template ) ) {

            $current_header_template = $wp_compat_header_template;

          }

        }

      }

    }

    $debug_lists['current_header_template'] = sprintf( '<strong>%s</strong> (%s)' , basename( $current_header_template ) , $current_header_template );

    $debug_lists['find_locate_header_templates'] = array();

    foreach( $all_header_templates as $template_file => $template_vals ) {

      $debug_lists['find_locate_header_templates'][ $template_file ] = $template_vals['found'];

    }

    $current_sidebar_template = false;

    $all_sidebar_templates = array();

    if( ! empty( self::$sidebar_templates ) ) {

      foreach( self::$sidebar_templates as $key => $find_sidebar_template ) {

        if( empty( $find_sidebar_template ) ) {

          continue;

        }

        $current_theme_sidebar_template = MywpApi::get_theme_path() . '/' . $find_sidebar_template;
        $parent_theme_sidebar_template = MywpApi::get_theme_path( true ) . '/' . $find_sidebar_template;
        $wp_compat_sidebar_template = ABSPATH . WPINC . '/theme-compat/' . $find_sidebar_template;

        $all_sidebar_templates[ $current_theme_sidebar_template ] = array( 'found' => 0 );

        if( file_exists( $current_theme_sidebar_template ) ) {

          $all_sidebar_templates[ $current_theme_sidebar_template ]['found'] = 1;

          if( empty( $current_sidebar_template ) ) {

            $current_sidebar_template = $current_theme_sidebar_template;

          }

        }

        if( is_child_theme() ) {

          $all_sidebar_templates[ $parent_theme_sidebar_template ] = array( 'found' => 0 );

          if( file_exists( $parent_theme_sidebar_template ) ) {

            $all_sidebar_templates[ $parent_theme_sidebar_template ]['found'] = 1;

            if( empty( $current_sidebar_template ) ) {

              $current_sidebar_template = $parent_theme_sidebar_template;

            }

          }

        }

        $all_sidebar_templates[ $wp_compat_sidebar_template ] = array( 'found' => 0 );

        if( file_exists( $wp_compat_sidebar_template ) ) {

          $all_sidebar_templates[ $wp_compat_sidebar_template ]['found'] = 1;

          if( empty( $current_sidebar_template ) ) {

            $current_sidebar_template = $wp_compat_sidebar_template;

          }

        }

      }

    }

    $debug_lists['current_sidebar_template'] = sprintf( '<strong>%s</strong> (%s)' , basename( $current_sidebar_template ) , $current_sidebar_template );

    $debug_lists['find_locate_sidebar_templates'] = array();

    foreach( $all_sidebar_templates as $template_file => $template_vals ) {

      $debug_lists['find_locate_sidebar_templates'][ $template_file ] = $template_vals['found'];

    }

    $current_footer_template = false;

    $all_footer_templates = array();

    if( ! empty( self::$footer_templates ) ) {

      foreach( self::$footer_templates as $key => $find_footer_template ) {

        if( empty( $find_footer_template ) ) {

          continue;

        }

        $current_theme_footer_template = MywpApi::get_theme_path() . '/' . $find_footer_template;
        $parent_theme_footer_template = MywpApi::get_theme_path( true ) . '/' . $find_footer_template;
        $wp_compat_footer_template = ABSPATH . WPINC . '/theme-compat/' . $find_footer_template;

        $all_footer_templates[ $current_theme_footer_template ] = array( 'found' => 0 );

        if( file_exists( $current_theme_footer_template ) ) {

          $all_footer_templates[ $current_theme_footer_template ]['found'] = 1;

          if( empty( $current_footer_template ) ) {

            $current_footer_template = $current_theme_footer_template;

          }

        }

        if( is_child_theme() ) {

          $all_footer_templates[ $parent_theme_footer_template ] = array( 'found' => 0 );

          if( file_exists( $parent_theme_footer_template ) ) {

            $all_footer_templates[ $parent_theme_footer_template ]['found'] = 1;

            if( empty( $current_footer_template ) ) {

              $current_footer_template = $parent_theme_footer_template;

            }

          }

        }

        $all_footer_templates[ $wp_compat_footer_template ] = array( 'found' => 0 );

        if( file_exists( $wp_compat_footer_template ) ) {

          $all_footer_templates[ $wp_compat_footer_template ]['found'] = 1;

          if( empty( $current_footer_template ) ) {

            $current_footer_template = $wp_compat_footer_template;

          }

        }

      }

    }

    $debug_lists['current_footer_template'] = sprintf( '<strong>%s</strong> (%s)' , basename( $current_footer_template ) , $current_footer_template );

    $debug_lists['find_locate_footer_templates'] = array();

    foreach( $all_footer_templates as $template_file => $template_vals ) {

      $debug_lists['find_locate_footer_templates'][ $template_file ] = $template_vals['found'];

    }

/*
    if( is_front_page() ) {

    } elseif( is_home() ) {

    } elseif( is_single() ) {

    } elseif( is_page() ) {

    } elseif( is_singular() ) {

    } elseif( is_category() ) {

    } elseif( is_tag() ) {

    } elseif( is_tax() ) {

    } elseif( is_author() ) {

    } elseif( is_date() ) {

    } elseif( is_archive() ) {

    } elseif( is_search() ) {

    } elseif( is_404() ) {

    } elseif( is_attachment() ) {

    } elseif( is_paged() ) {

    } elseif( is_post_type_archive() ) {

    } elseif( is_page_template() ) {

    } elseif( is_embed() ) {

    }
*/

    if( is_singular() ) {

      $debug_lists['post'] = $post;

      if( ! empty( $post ) && is_object( $post ) ) {

        $post_id = (int) $post->ID;

        $debug_lists['post_id'] = $post_id;
        $debug_lists['custom_fields'] = get_post_meta( $post_id );

        $debug_lists['taxonomies'] = array();
        $debug_lists['terms'] = array();

        $post_taxonomies = get_post_taxonomies( $post_id );

        if( ! empty( $post_taxonomies ) ) {

          foreach( $post_taxonomies as $taxonomy_name ) {

            $debug_lists['taxonomies'][ $taxonomy_name ] = false;
            $debug_lists['terms'][ $taxonomy_name ] = false;

            $taxonomy = get_taxonomy( $taxonomy_name );

            $terms = wp_get_post_terms( $post_id , $taxonomy_name );

            if( empty( $taxonomy ) ) {

              continue;

            }

            $debug_lists['taxonomies'][ $taxonomy_name ] = $taxonomy;

            if( ! empty( $terms ) ) {

              $debug_lists['terms'][ $taxonomy_name ] = $terms;

            }

          }

        }

        $debug_lists['get_post( ' . $post_id . ' )'] = get_post( $post_id );
        $debug_lists['MywpPostType::get_post( ' . $post_id . ' )'] = MywpPostType::get_post( $post_id );
        $debug_lists['SELECT * FROM $wpdb->posts WHERE ID = ' . $post_id] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id ) );

      }

      $debug_lists['get_permalink'] = esc_url( get_permalink( $post_id ) );
      $debug_lists['get_page_link'] = esc_url( get_page_link( $post_id ) );

    } elseif( is_archive() ) {

      if( is_category() or is_tag() or is_tax() ) {

        $term = get_queried_object();

        $debug_lists['term'] = $term;

        if( ! empty( $term ) && is_object( $term ) ) {

          $debug_lists['taxonomy'] = $term->taxonomy;
          $debug_lists['term_id'] = $term->term_id;
          $debug_lists['custom_meta'] = get_term_meta( $term->term_id );

        }

      } elseif( is_date() ) {

        $debug_lists['get_query_var("year")'] = get_query_var("year");
        $debug_lists['get_query_var("m")'] = get_query_var("m");
        $debug_lists['get_query_var("monthnum")'] = get_query_var("monthnum");
        $debug_lists['get_query_var("day")'] = get_query_var("day");

      }

    } elseif( is_search() ) {

      $debug_lists['get_search_query()'] = get_search_query();

    }

    return $debug_lists;

  }

  protected static function mywp_developer_debug() {

    if( is_admin() ) {

      return false;

    }

    parent::mywp_developer_debug();

  }

  protected static function mywp_debug_render() {

    if( is_admin() ) {

      return false;

    }

    parent::mywp_debug_render();

  }

}

MywpDeveloperModuleDevFrontend::init();

endif;
