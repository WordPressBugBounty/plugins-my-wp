<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpSetting' ) ) {
  return false;
}

if( is_network_admin() ) {

  if( ! MywpApi::is_network_manager() ) {

    return false;

  }

} else {

  if( ! MywpApi::is_manager() ) {

    return false;

  }

}

$current_file_name = basename( __FILE__ );

$current_setting_menu = MywpSettingMenu::get_current_menu();

if( empty( $current_setting_menu ) ) {

  MywpHelper::error_not_found_message( '$current_setting_menu' , $current_file_name );

  return false;

}

$current_setting_menu_id = MywpSettingMenu::get_current_menu_id();

$setting_screens = MywpSettingScreen::get_setting_screens_by_menu_id( $current_setting_menu_id );

if( empty( $setting_screens ) ) {

  MywpHelper::error_not_found_message( '$setting_screens' , $current_file_name );

}

$current_setting_screen = MywpSettingScreen::get_current_screen();

if( empty( $current_setting_screen ) ) {

  MywpHelper::error_not_found_message( '$current_setting_screen' , $current_file_name );

}

$current_setting_screen_id = MywpSettingScreen::get_current_screen_id();

$multiple_screens = false;

if( ! empty( $current_setting_menu['multiple_screens'] ) ) {

  $multiple_screens = true;

}

$use_form = false;

if( ! empty( $current_setting_screen['use_form'] ) ) {

  $use_form = true;

}

$use_advance = false;

if( ! empty( $current_setting_screen['use_advance'] ) ) {

  $use_advance = true;

}

$mywp_model = MywpSetting::get_model( $current_setting_screen_id );

$active_advance = 0;

if(  ! empty( $mywp_model ) ) {

  $setting_data = $mywp_model->get_setting_data();

  if( ! empty( $setting_data['advance'] ) ) {

    $active_advance = 1;

  }

}

if( ! empty( $current_setting_screen_id ) ) {

  $active_advance = apply_filters( "mywp_setting_screen_active_advance_{$current_setting_screen_id}" , $active_advance );

}

?>
<div class="wrap mywp mywp-setting-screen-menu-<?php echo sanitize_html_class( $current_setting_menu_id ); ?>">

  <h1><?php echo $current_setting_menu['page_title']; ?></h1>

  <?php if( ! empty( $current_setting_screen_id ) ) : ?>

    <?php do_action( "mywp_setting_screen_before_header_{$current_setting_menu_id}" ); ?>
    <?php do_action( "mywp_setting_screen_before_header_{$current_setting_screen_id}" ); ?>
    <?php do_action( 'mywp_setting_screen_before_header' , $current_setting_screen_id , $current_setting_menu_id ); ?>

    <?php $add_class = ''; ?>

    <?php if( $multiple_screens ) : ?>

      <?php $add_class = 'multiple'; ?>

    <?php endif; ?>

    <div id="setting-screen" class="<?php echo sanitize_html_class( $add_class ); ?>">

      <?php if( $multiple_screens ) : ?>

        <div id="setting-screen-select-screens">

          <ul>

            <?php foreach( $setting_screens as $setting_screen_id => $setting_screen ) : ?>

              <?php $add_class = ''; ?>
              <?php $setting_screen_id = $setting_screen['id']; ?>

              <?php if( $setting_screen_id === $current_setting_screen_id ) : ?>

                <?php $add_class = 'active'; ?>

              <?php endif; ?>

              <?php $current_url = remove_query_arg( 'setting_screen' ); ?>

              <?php $url = add_query_arg( array( 'page' => $current_setting_menu['slug'] , 'setting_screen' => $setting_screen_id ) , $current_url ); ?>

              <li class="setting-screen-select-screen <?php echo sanitize_html_class( $add_class ); ?>">
                <a href="<?php echo esc_url( $url ); ?>" data-setting_screen_id="<?php echo esc_attr( $setting_screen_id ); ?>"><?php echo $setting_screen['title']; ?></a>
              </li>

            <?php endforeach; ?>

          </ul>

        </div><!-- #setting-screen-select-screens -->

      <?php endif; ?>

      <div id="setting-screen-section-wrap">

        <div id="setting-screen-section">

          <?php if( ! empty( $multiple_screens ) && ! empty( $current_setting_screen ) ) : ?>

            <h2 class="setting-screen-title">

              <?php echo $current_setting_screen['title']; ?>

              <?php if( ! empty( $current_setting_screen['document_url'] ) ) : ?>

                <a id="setting-document-link" href="<?php echo esc_url( $current_setting_screen['document_url'] ); ?>" target="_blank" class="button button-secondary"><span class="dashicons dashicons-welcome-learn-more"></span> <?php printf( __( '%s Documentation' , 'my-wp' ) , $current_setting_screen['title'] ); ?></a>

              <?php endif; ?>

            </h2>

          <?php else : ?>

            <?php if( ! empty( $current_setting_screen['document_url'] ) ) : ?>

              <p style="text-align: right;">
                <a id="setting-document-link" href="<?php echo esc_url( $current_setting_screen['document_url'] ); ?>" target="_blank" class="button"><span class="dashicons dashicons-welcome-learn-more"></span> <?php printf( __( '%s Documentation' , 'my-wp' ) , $current_setting_screen['title'] ); ?></a>
              </p>

            <?php endif; ?>

          <?php endif; ?>

          <form id="mywp_form_<?php echo esc_attr( $current_setting_screen_id ); ?>_update" class="mywp_form" method="post" action="">

            <?php if( $use_form ) : ?>

              <?php MywpSetting::print_form_item_must( $current_setting_screen_id , 'update' ); ?>

              <input type="hidden" name="mywp[data][dummy]" value="1" />

            <?php endif; ?>

            <div id="setting-screen-content" class="setting-screen-<?php echo sanitize_html_class( $current_setting_screen_id ); ?>">

              <?php do_action( "mywp_setting_screen_header_{$current_setting_screen_id}" ); ?>
              <?php do_action( 'mywp_setting_screen_header' , $current_setting_screen_id , $current_setting_menu_id ); ?>

              <?php do_action( "mywp_setting_screen_content_{$current_setting_screen_id}" ); ?>
              <?php do_action( 'mywp_setting_screen_content' , $current_setting_screen_id , $current_setting_menu_id ); ?>

              <?php do_action( "mywp_setting_screen_footer_{$current_setting_screen_id}" ); ?>
              <?php do_action( 'mywp_setting_screen_footer' , $current_setting_screen_id , $current_setting_menu_id ); ?>

            </div>

            <?php if( ! empty( $use_advance ) ) : ?>

              <?php $class = ''; ?>

              <?php if( ! empty( $active_advance ) ) $class = 'active'; ?>

              <div id="setting-screen-advance" class="<?php echo sanitize_html_class( $class ); ?>">

                <p id="select-advance-setting">
                  <input type="hidden" name="mywp[data][advance]" id="select-advance-setting-check" value="<?php echo esc_attr( $active_advance ); ?>" />
                  <a href="javascript:void(0);" id="select-advance-setting-toggle"><?php _e( 'Advanced settings' , 'my-wp' ); ?></a>
                </p>

                <div id="setting-screen-advance-content">

                  <?php do_action( "mywp_setting_screen_advance_header_{$current_setting_screen_id}" ); ?>
                  <?php do_action( 'mywp_setting_screen_advance_header' , $current_setting_screen_id , $current_setting_menu_id ); ?>

                  <?php do_action( "mywp_setting_screen_advance_content_{$current_setting_screen_id}" ); ?>
                  <?php do_action( 'mywp_setting_screen_advance_content' , $current_setting_screen_id , $current_setting_menu_id ); ?>

                  <?php do_action( "mywp_setting_screen_advance_footer_{$current_setting_screen_id}" ); ?>
                  <?php do_action( 'mywp_setting_screen_advance_footer' , $current_setting_screen_id , $current_setting_menu_id ); ?>

                </div><!-- #setting-screen-advance-content -->

              </div><!-- #setting-screen-advance -->

            <?php endif; ?>

            <?php if( $use_form ) : ?>

              <p class="submit">
                <button type="submit" class="button button-primary">
                  <?php _e( 'Save' ); ?>
                </button>
                <span class="spinner"></span>
              </p>

            <?php endif; ?>

          </form>

          <?php if( $use_form ) : ?>

            <p>&nbsp;</p>

            <form id="mywp_form_<?php echo esc_attr( $current_setting_screen_id ); ?>_remove" class="mywp_form mywp_form_remove" method="post" action="">

            <?php MywpSetting::print_form_item_must( $current_setting_screen_id , 'remove' ); ?>

            <input type="hidden" name="mywp[data][remove]" value="1" />

            <?php do_action( "mywp_setting_screen_remove_form_{$current_setting_screen_id}" ); ?>
            <?php do_action( 'mywp_setting_screen_remove_form' , $current_setting_screen_id , $current_setting_menu_id ); ?>

            <p class="submit">
              <button type="submit" class="button button-secondary button-caution">
                <span class="dashicons dashicons-trash"></span>
                <?php echo $current_setting_menu['page_title']; ?>
                &gt;
                <?php echo esc_attr( sprintf( __( 'Remove the %s settings data' , 'my-wp' ) , strip_tags( $current_setting_screen['title'] ) ) ); ?>
              </button>
              <span class="spinner"></span>
            </p>

            </form>

          <?php endif; ?>

        </div><!-- #setting-screen-section -->

      </div><!-- #setting-screen-section-wrap -->

      <div class="clear"></div>

    </div><!-- #setting-screen -->

    <?php do_action( "mywp_setting_screen_after_footer_{$current_setting_menu_id}" ); ?>
    <?php do_action( "mywp_setting_screen_after_footer_{$current_setting_screen_id}" ); ?>
    <?php do_action( 'mywp_setting_screen_after_footer' , $current_setting_screen_id , $current_setting_menu_id ); ?>

  <?php endif; ?>

</div>

<div id="mywp-popup">

  <div id="mywp-popup-container">

    <div id="mywp-popup-close-right"><a href="javascript:void(0);" class="mywp-popup-close"><span class="dashicons dashicons-no"></span></a></div>

    <div id="mywp-popup-content">

      <div id="mywp-popup-content-inner"></div>

    </div>

    <div id="mywp-popup-close-bottom"><buttom type="button" class="mywp-popup-close button button-secondary"><span class="dashicons dashicons-no"></span> <?php _e( 'Close' ); ?></button></div>

  </div>

  <div id="mywp-popup-bg"></div>

</div>
