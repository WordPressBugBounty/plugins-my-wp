<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugFindOption' ) ) :

final class MywpSettingScreenDebugFindOption extends MywpAbstractSettingModule {

  static protected $id = 'network_debug_find_option';

  static protected $priority = 100;

  static private $menu = 'network_debug';

  static private $current_find_option_name = 'siteurl';

  public static function mywp_setting_screens( $setting_screens ) {

    if( is_multisite() ) {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Find Option' , 'my-wp' ),
        'menu' => self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  private static function get_option( $site_id ) {

    switch_to_blog( $site_id );

    $option = get_option( self::$current_find_option_name );

    restore_current_blog();

    return $option;

  }

  public static function mywp_current_admin_print_footer_scripts() {

    $current_url = add_query_arg( array( 'page' => 'mywp_' . self::$menu , 'setting_screen' => self::$id ) , network_admin_url( 'admin.php' ) );

    ?>
    <script>
    jQuery(function( $ ) {

      $('#find-option').on('click', function() {

        let current_url = '<?php echo esc_js( $current_url ); ?>';

        let find_option_name = $('#request-find-option-name').val();

        request_url = current_url + '&setting_option_name=' + find_option_name;

        $(location).attr('href', request_url);

      });

      let found_total = 0;

      $('#sites-find-options tr').each( function( index , el ) {

        let $tr = $(el);

        if( $tr.find('.found').length ) {

          found_total++;

        }

      });

      $('#found-total').text( found_total );

    });
    </script>
    <?php

  }

  public static function mywp_current_setting_screen_header() {

    if( is_multisite() ) {

      if( ! is_network_admin() ) {

        return false;

      }

    } else {

      return false;

    }

    if( ! empty( $_GET['setting_option_name'] ) ) {

      self::$current_find_option_name = strip_tags( $_GET['setting_option_name'] );

    }

    ?>

    <div id="find-request">
      <input type="text" class="regular-text" id="request-find-option-name" value="<?php echo esc_attr( self::$current_find_option_name ); ?>" />
      <input type="button" class="button button-primary" id="find-option" value="<?php echo esc_attr( __( 'Search' ) ); ?>" />
    </div>

    <?php

  }

  public static function mywp_current_setting_screen_content() {

    if( is_multisite() ) {

      if( ! is_network_admin() ) {

        return false;

      }

    } else {

      return false;

    }

    $all_sites = MywpHelper::get_all_sites();

    if( empty( $all_sites ) ) {

      return false;

    }

    $current_blog_id = get_current_blog_id();

    ?>

    <p><?php _e( 'Count' , 'my-wp' ); ?>: <span id="found-total"></span>/ <?php echo count( $all_sites ); ?></p>
    <table class="form-table" id="sites-find-options">
      <thead>
        <tr>
          <th>Site ID</th>
          <td>option_value</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $all_sites as $site ) : ?>
          <tr>
            <th>
              <?php echo $site->blog_id; ?>
            </th>
            <td>
              <?php $option = self::get_option( $site->blog_id ); ?>
              <?php if( is_array( $option ) or is_object( $option ) ) : ?>
                <textarea readonly="readonly" class="large-text" style="height: 200px;"><?php print_r( $option ); ?></textarea>
              <?php else : ?>
                <?php echo esc_html( $option ); ?>
              <?php endif; ?>
              <?php if( $option !== false ) : ?>
                <span class="found"></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php switch_to_blog( $current_blog_id ); ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugFindOption::init();

endif;
