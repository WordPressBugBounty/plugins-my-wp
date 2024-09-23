<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleLoginGeneral' ) ) :

final class MywpControllerModuleLoginGeneral extends MywpControllerAbstractModule {

  static protected $id = 'login_general';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['logo_link_url'] = '';
    $initial_data['logo_image_path'] = '';
    $initial_data['logo_title'] = '';
    $initial_data['hidden_select_language'] = '';
    $initial_data['input_css'] = '';
    $initial_data['include_css_file'] = '';
    $initial_data['custom_footer_text'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['logo_link_url'] = '';
    $default_data['logo_image_path'] = '';
    $default_data['logo_title'] = '';
    $default_data['hidden_select_language'] = '';
    $default_data['input_css'] = '';
    $default_data['include_css_file'] = '';
    $default_data['custom_footer_text'] = '';

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    global $wp_version;

    if( is_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'login_enqueue_scripts' , array( __CLASS__ , 'include_css' ) );

    add_action( 'wp_print_scripts' , array( __CLASS__ , 'input_css' ) );

    add_action( 'login_head' , array( __CLASS__ , 'logo_image_path' ) );

    add_filter( 'login_title' , array( __CLASS__ , 'login_title' ) );

    add_filter( 'login_headerurl' , array( __CLASS__ , 'logo_link_url' ) );

    if( version_compare( $wp_version , '5.2.0' , '>=' ) ) {

      add_filter( 'login_headertext' , array( __CLASS__ , 'logo_title' ) );

    } else {

      add_filter( 'login_headertext' , array( __CLASS__ , 'logo_title' ) );

      add_filter( 'login_headertitle' , array( __CLASS__ , 'logo_title' ) );

    }

    add_filter( 'login_display_language_dropdown' , array( __CLASS__ , 'hidden_select_language' ) );

    add_action( 'login_footer' , array( __CLASS__ , 'custom_footer_text' ) );

  }

  public static function include_css() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['include_css_file'] ) ) {

      return false;

    }

    $include_css_file = do_shortcode( $setting_data['include_css_file'] );

    if( ! empty( $include_css_file ) ) {

      wp_enqueue_style( 'mywp_login_include' , $include_css_file , array() , MYWP_VERSION );

    }

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

  public static function logo_image_path() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['logo_image_path'] ) ) {

      return false;

    }

    $logo_image_path = do_shortcode( $setting_data['logo_image_path'] );

    echo '<style>';

    if( ! empty( $logo_image_path ) ) {

      printf( '.login h1 a { background-image: url(%s); }' , esc_attr( $logo_image_path ) );

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function login_title( $login_title ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $login_title;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['logo_title'] ) ) {

      return $login_title;

    }

    $login_title = do_shortcode( strip_tags( $setting_data['logo_title'] ) );

    self::after_do_function( __FUNCTION__ );

    return $login_title;

  }

  public static function logo_link_url( $login_header_url ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $login_header_url;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['logo_link_url'] ) ) {

      return $login_header_url;

    }

    $login_header_url = do_shortcode( $setting_data['logo_link_url'] );

    self::after_do_function( __FUNCTION__ );

    return $login_header_url;

  }

  public static function logo_title( $login_header_title ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $login_header_title;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['logo_title'] ) ) {

      return $login_header_title;

    }

    $login_header_title = do_shortcode( strip_tags( $setting_data['logo_title'] ) );

    self::after_do_function( __FUNCTION__ );

    return $login_header_title;

  }

  public static function hidden_select_language( $true ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $true;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hidden_select_language'] ) ) {

      return $true;

    }

    $true = false;

    self::after_do_function( __FUNCTION__ );

    return $true;

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

    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'wptexturize' );
    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'convert_smilies' , 20 );
    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'wpautop' );
    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'shortcode_unautop' );
    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'prepend_attachment' );

    if( version_compare( $wp_version , '5.7.0' , '>=' ) ) {

      add_filter( 'mywp_controller_login_general_custom_footer_text' , 'wp_replace_insecure_home_url' );

    }

    add_filter( 'mywp_controller_login_general_custom_footer_text' , 'do_shortcode' , 11 );

    if( version_compare( $wp_version , '5.5.0' , '>=' ) ) {

      add_filter( 'mywp_controller_login_general_custom_footer_text' , 'wp_filter_content_tags' , 12 );

    }

    $custom_footer_text = apply_filters( 'mywp_controller_login_general_custom_footer_text' , $setting_data['custom_footer_text'] );

    ?>

    <div id="mywp-custom-footer-text">
      <?php echo $custom_footer_text; ?>
    </div>

    <?php

    $post = $restore_post;

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleLoginGeneral::init();

endif;
