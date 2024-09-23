<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminGeneral' ) ) :

final class MywpControllerModuleAdminGeneral extends MywpControllerAbstractModule {

  static protected $id = 'admin_general';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['hide_update_notice'] = array(
      'core' => '',
      'plugins' => '',
      'themes' => '',
      'translations' => '',
    );

    $initial_data['hide_screen_tabs'] = array(
      'options' => '',
      'help' => '',
    );

    $initial_data['hide_footer_text'] = array(
      'left' => '',
      'right' => '',
    );

    $initial_data['custom_footer_text'] = '';
    $initial_data['hide_core_title_tag'] = '';
    $initial_data['include_css_file'] = '';
    $initial_data['include_js_file'] = '';
    $initial_data['input_css'] = '';
    $initial_data['max_post_revision'] = '';
    $initial_data['not_use_admin'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['hide_update_notice'] = array(
      'core' => false,
      'plugins' => false,
      'themes' => false,
      'translations' => false,
    );

    $default_data['hide_screen_tabs'] = array(
      'options' => false,
      'help' => false,
    );

    $default_data['hide_footer_text'] = array(
      'left' => false,
      'right' => false,
    );

    $default_data['custom_footer_text'] = '';
    $default_data['hide_core_title_tag'] = false;
    $default_data['include_css_file'] = '';
    $default_data['include_js_file'] = '';
    $default_data['input_css'] = '';
    $default_data['max_post_revision'] = -1;
    $default_data['not_use_admin'] = false;

    return $default_data;

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

    self::not_use_admin();

    add_filter( 'site_transient_update_plugins' , array( __CLASS__ , 'hide_update_notice_plugins' ) );

    add_filter( 'site_transient_update_themes' , array( __CLASS__ , 'hide_update_notice_themes' ) );

    add_filter( 'site_transient_update_core' , array( __CLASS__ , 'hide_update_notice_core' ) );

    add_filter( 'wp_revisions_to_keep' , array( __CLASS__ , 'max_post_revision' ) );

    add_filter( 'admin_title' , array( __CLASS__ , 'hide_core_title_tag' ) );

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'admin_enqueue_scripts' ) );

    add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'include_jc_css' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_screen_tabs' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_footer_text' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'input_css' ) );

    add_action( 'admin_footer' , array( __CLASS__ , 'custom_footer_text' ) );

  }

  private static function not_use_admin() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( MywpHelper::is_doing( 'cron' ) or MywpHelper::is_doing( 'xmlrpc' ) or MywpHelper::is_doing( 'ajax' ) or MywpHelper::is_doing( 'rest' ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['not_use_admin'] ) ) {

      return false;

    }

    wp_safe_redirect( do_shortcode( '[mywp_url]' ) );

    self::after_do_function( __FUNCTION__ );

    exit;

  }

  public static function hide_update_notice_plugins( $site_transient ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $site_transient;

    }

    if( empty( $site_transient ) or empty( $site_transient->response ) ) {

      return $site_transient;

    }

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['hide_update_notice']['translations'] ) ) {

      if( ! empty( $site_transient->translations ) && is_array( $site_transient->translations ) ) {

        $site_transient->translations = array();

      }

    }

    if( ! empty( $setting_data['hide_update_notice']['plugins'] ) ) {

      $site_transient->response = array();

    }

    self::after_do_function( __FUNCTION__ );

    return $site_transient;

  }

  public static function hide_update_notice_themes( $site_transient ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $site_transient;

    }

    if( empty( $site_transient ) or empty( $site_transient->response ) ) {

      return $site_transient;

    }

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['hide_update_notice']['translations'] ) ) {

      if( ! empty( $site_transient->translations ) && is_array( $site_transient->translations ) ) {

        $site_transient->translations = array();

      }

    }

    if( ! empty( $setting_data['hide_update_notice']['themes'] ) ) {

      $site_transient->response = array();

    }

    self::after_do_function( __FUNCTION__ );

    return $site_transient;

  }

  public static function hide_update_notice_core( $site_transient ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $site_transient;

    }

    if( empty( $site_transient ) or empty( $site_transient->updates[0] ) or empty( $site_transient->updates[0]->response ) ) {

      return $site_transient;

    }

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['hide_update_notice']['translations'] ) ) {

      if( ! empty( $site_transient->translations ) && is_array( $site_transient->translations ) ) {

        $site_transient->translations = array();

      }

    }

    if( ! empty( $setting_data['hide_update_notice']['core'] ) ) {

      $site_transient->updates[0]->response = 'latest';

    }

    self::after_do_function( __FUNCTION__ );

    return $site_transient;

  }

  public static function max_post_revision( $revision_num ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $revision_num;

    }

    $setting_data = self::get_setting_data();

    if( ! isset( $setting_data['max_post_revision'] ) ) {

      return $revision_num;

    }

    if( $setting_data['max_post_revision'] === '' ) {

      return $revision_num;

    }

    $revision_num = (int) $setting_data['max_post_revision'];

    self::after_do_function( __FUNCTION__ );

    return $revision_num;

  }

  public static function hide_core_title_tag( $title ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $title;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_core_title_tag'] ) ) {

      return $title;

    }

    $title = str_replace( ' &#8212; WordPress' , '' , $title );

    self::after_do_function( __FUNCTION__ );

    return $title;

  }

  public static function admin_enqueue_scripts() {

    wp_register_style( 'mywp_admin_general' , MywpApi::get_plugin_url( 'css' ) . 'admin-general.css' , array() , MYWP_VERSION );

    wp_enqueue_style( 'mywp_admin_general' );

  }

  public static function include_jc_css() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['include_js_file'] ) && empty( $setting_data['include_css_file'] ) ) {

      return false;

    }

    $include_js_file = do_shortcode( $setting_data['include_js_file'] );
    $include_css_file = do_shortcode( $setting_data['include_css_file'] );

    if( ! empty( $include_js_file ) ) {

      wp_enqueue_script( 'mywp_admin_include' , $include_js_file , array() , MYWP_VERSION , true );

    }

    if( ! empty( $include_css_file ) ) {

      wp_enqueue_style( 'mywp_admin_include' , $include_css_file , array() , MYWP_VERSION );

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_screen_tabs() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_screen_tabs']['options'] ) && empty( $setting_data['hide_screen_tabs']['help'] ) ) {

      return false;

    }

    echo '<style>';

    if( ! empty( $setting_data['hide_screen_tabs']['options'] ) ) {

      echo 'body.wp-admin #screen-options-link-wrap { display: none; }';

    }

    if( ! empty( $setting_data['hide_screen_tabs']['help'] ) ) {

      echo 'body.wp-admin #contextual-help-link-wrap { display: none; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_footer_text() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_footer_text']['left'] ) && empty( $setting_data['hide_footer_text']['right'] ) ) {

      return false;

    }

    echo '<style>';

    if( ! empty( $setting_data['hide_footer_text']['left'] ) && ! empty( $setting_data['hide_footer_text']['right'] ) ) {

      echo 'body.wp-admin #wpfooter { display: none; }';

    } else {

      if( ! empty( $setting_data['hide_footer_text']['left'] ) ) {

        echo 'body.wp-admin #wpfooter #footer-left { display: none; }';

      }

      if( ! empty( $setting_data['hide_footer_text']['right'] ) ) {

        echo 'body.wp-admin #wpfooter #footer-upgrade { display: none; }';

      }

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function input_css() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['input_css'] ) ) {

      return false;

    }

    $input_css = do_shortcode( strip_tags( $setting_data['input_css'] ) );

    echo '<style>';
    echo $input_css;
    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function custom_footer_text() {

    global $wp_version;
    global $post;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['custom_footer_text'] ) ) {

      return false;

    }

    $restore_post = $post;

    $post = false;

    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'wptexturize' );
    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'convert_smilies' , 20 );
    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'wpautop' );
    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'shortcode_unautop' );
    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'prepend_attachment' );

    if( version_compare( $wp_version , '5.7.0' , '>=' ) ) {

      add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'wp_replace_insecure_home_url' );

    }

    add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'do_shortcode' , 11 );

    if( version_compare( $wp_version , '5.5.0' , '>=' ) ) {

      add_filter( 'mywp_controller_admin_general_custom_footer_text' , 'wp_filter_content_tags' , 12 );

    }

    $custom_footer_text = apply_filters( 'mywp_controller_admin_general_custom_footer_text' , $setting_data['custom_footer_text'] );

    ?>

    <style>
    body.wp-admin #wpfooter { position: relative; }
    </style>

    <div class="clear"></div>

    <div id="mywp-custom-footer-text">
      <?php echo $custom_footer_text; ?>
    </div>

    <?php

    $post = $restore_post;

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminGeneral::init();

endif;
