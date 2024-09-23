<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreeMainGeneral' ) ) :

final class MywpSettingScreeMainGeneral extends MywpAbstractSettingModule {

  static protected $id = 'main_general';

  static private $menu = 'main';

  static protected $priority = 1;

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'My WP' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $plugin_info = MywpApi::plugin_info();

    $schema = 'http://';

    if( is_ssl() ) {

      $schema = 'https://';

    }

    ?>

    <p>&nbsp;</p>

    <div class="about-column column-main-right">
      <p class="right"><img src="<?php echo esc_url( MywpApi::get_plugin_url( 'assets' ) ); ?>img/admin-customize.png" /></p>
      <div class="left">
        <p class="about-column-title"><?php _e( 'Admin Customize' , 'my-wp' ); ?></p>
        <p class="about-column-description"><?php _e( 'Add the custom link menu to Sidebar, change the metabox title, order to list of Posts and more.' , 'my-wp' ); ?></p>
        <p>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=mywp_admin' ) ); ?>" class="button button-primary"><?php _e( 'Admin Customize' , 'my-wp' ); ?></a>
          <a href="<?php echo esc_url( $plugin_info['website_url'] ); ?>document/admin-general/" class="button" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'Documentation' , 'my-wp' ); ?></a>
        </p>
      </div>
    </div>

    <div class="about-column column-main-left">
      <div class="right">
        <p class="about-column-title"><?php _e( 'Frontend Customize' , 'my-wp' ); ?></p>
        <p class="about-column-description"><?php _e( 'Hide the some tags, custom meta fields, disabled archive pages and more.' , 'my-wp' ); ?></p>
        <p>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=mywp_frontend' ) ); ?>" class="button button-primary"> <?php _e( 'Frontend Customize' , 'my-wp' ); ?></a>
          <a href="<?php echo esc_url( $plugin_info['website_url'] ); ?>document/frontend-general/" class="button" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'Documentation' , 'my-wp' ); ?></a>
        </p>
      </div>
      <p class="left"><img src="<?php echo esc_url( MywpApi::get_plugin_url( 'assets' ) ); ?>img/frontend-customize.png" /></p>
    </div>

    <div class="about-column column-main-right">
      <p class="right"><img src="<?php echo esc_url( MywpApi::get_plugin_url( 'assets' ) ); ?>img/developer-tools.png" /></p>
      <div class="left">
        <p class="about-column-title"><?php _e( 'For Developer' , 'my-wp' ); ?></p>
        <p class="about-column-description"><?php _e( 'Debugging tools that are useful for developing plugin or theme.' , 'my-wp' ); ?></p>
        <p>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=mywp_debug' ) ); ?>" class="button button-primary"><?php _e( 'Debug' , 'my-wp' ); ?></a>
          <a href="<?php echo esc_url( $plugin_info['document_url'] ); ?>" class="button" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'Documentation' , 'my-wp' ); ?></a>
        </p>
      </div>
    </div>

    <div class="about-column column-main-left">
      <div class="right">
        <p class="about-column-title"><?php _e( 'Powerful add-ons' , 'my-wp' ); ?></p>
        <p class="about-column-description"><?php _e( 'Customize only specific user roles, conditional lockout and notifications on WP and more.' , 'my-wp' ); ?></p>
        <p>
          <a href="<?php echo esc_url( $plugin_info['website_url'] ); ?>add-ons/" class="button" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'Add-ons' , 'my-wp' ); ?></a>
        </p>
      </div>
      <p class="left"><img src="<?php echo esc_url( MywpApi::get_plugin_url( 'assets' ) ); ?>img/add-ons.png" /></p>
    </div>


    <hr />

    <h3><?php printf( __( 'About %s' ) , MYWP_NAME ); ?></h3>

    <table>
      <tr>
        <th><?php _e( 'Version' , 'my-wp' ); ?></th>
        <td><?php echo MYWP_VERSION; ?></td>
      </tr>
      <tr>
        <th><?php _e( 'Plugin Website' , 'my-wp' ); ?></th>
        <td><a href="<?php echo esc_url( $plugin_info['website_url'] ); ?>" target="_blank"><?php echo $plugin_info['website_url']; ?></a></td>
      </tr>
      <tr>
        <th><?php _e( 'Developer' , 'my-wp' ); ?></th>
        <td>
          <img src="<?php echo $schema; ?>www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=20" width="20" />
          <a href="<?php echo esc_url( 'http://gqevu6bsiz.chicappa.jp/' ); ?>" target="_blank">gqevu6bsiz</a>
        </td>
      </tr>
      <tr>
        <th><?php _e( 'Reviews' ); ?></th>
        <td><a href="<?php echo esc_url( $plugin_info['review_url'] ); ?>" target="_blank"><?php _e( 'Reviews' ); ?></a></td>
      </tr>
      <tr>
        <th><?php _e( 'Contact' , 'my-wp' ); ?></th>
        <td>
          <a href="<?php echo esc_url( $plugin_info['forum_url'] ); ?>" target="_blank"><?php _e( 'Support Forums' , 'my-wp' ); ?></a>
          <a href="<?php echo esc_url( $plugin_info['website_url'] ); ?>contact/" target="_blank"><?php _e( 'Contact Form' , 'my-wp' ); ?></a>
          <span class="dashicons dashicons-smiley"></span> <?php _e( 'If you find a bug, please contact me' , 'my-wp' ); ?>
        </td>
      </tr>
    </table>

    <?php

  }

  public static function mywp_current_admin_print_footer_scripts() {

    ?>
    <style>
    body.mywp-setting .wrap {
      padding: 14px 0 0 0;
    }
    body.mywp-setting .wrap h1 {
      font-size: 3em;
      line-height: 0.6em;
    }
    body.mywp-setting .wrap .page-description {
      font-size: 1.2em;
      color: #777;
    }
    body.mywp-setting .wrap .about-column {
      margin: 0 2% 86px 2%;
    }
    body.mywp-setting .wrap .about-column:after {
      content: "";
      clear: both;
      display: block;
    }
    body.mywp-setting .wrap .about-column img {
      max-width: 100%;
    }
    body.mywp-setting .wrap .about-column .right {
      float: right;
      text-align: left;
    }
    body.mywp-setting .wrap .about-column .left {
      float: left;
      text-align: left;
    }
    body.mywp-setting .wrap .about-column.column-main-right .right {
      width: 60%;
    }
    body.mywp-setting .wrap .about-column.column-main-right .left {
      width: 36%;
    }
    body.mywp-setting .wrap .about-column.column-main-left .right {
      width: 36%;
    }
    body.mywp-setting .wrap .about-column.column-main-left .left {
      width: 60%;
    }
    body.mywp-setting .wrap .about-column-title {
      font-size: 4.0em;
      line-height: 1;
      margin: 126px 0 18px 0;
      color: #F49C31;
    }
    body.mywp-setting .wrap .about-column-description {
      font-size: 1.8em;
      line-height: 1.6em;
      margin: 0;
      color: #666;
    }
    body.mywp-setting .wrap .about-column a {
      margin-bottom: 12px;
    }
    body.mywp-setting .wrap table th,
    body.mywp-setting .wrap table td {
      text-align: left;
    }
    @media screen and (max-width: 1300px) {

      body.mywp-setting .wrap .about-column-title {
        font-size: 2.6em;
        margin: 60px 0 18px 0;
      }
      body.mywp-setting .wrap .about-column-description {
        font-size: 1.4em;
        line-height: 1.6em;
      }

    }
    </style>
    <script>
    jQuery(function( $ ) {

      $('.mywp-setting-screen-menu-main h1').append( '<span style="color: #888; font-size: 0.6em; margin-left: 12px;">ver <?php echo esc_js( MYWP_VERSION ); ?></span>' );

    });
    </script>
    <?php

  }

}

MywpSettingScreeMainGeneral::init();

endif;
