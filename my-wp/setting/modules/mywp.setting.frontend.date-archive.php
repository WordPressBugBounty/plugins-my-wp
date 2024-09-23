<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenFrontendDateArchive' ) ) :

final class MywpSettingScreenFrontendDateArchive extends MywpAbstractSettingModule {

  static protected $id = 'frontend_date_archive';

  static protected $priority = 50;

  static private $menu = 'frontend';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Date Archive' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'frontend_date_archive',
      'document_url' => self::get_document_url( 'document/frontend-date-archive/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $posts = get_posts( array( 'post_type' => 'post' , 'numberposts' => 1 ) );

    $date_post = false;

    if( ! empty( $posts[0] ) ) {

      $date_post = $posts[0];

      $date_post_timestamp = strtotime( $date_post->post_date );

    }

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Date Archive' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_archive]" class="disable_archive" value="1" <?php checked( $setting_data['disable_archive'] , true ); ?> />
              <?php _e( 'Disable' , 'my-wp' ); ?>
            </label>
            &nbsp;
            <?php if( ! empty( $date_post ) ) : ?>
              <?php $year = date( 'Y' , $date_post_timestamp ); ?>
              <?php $month = date( 'm' , $date_post_timestamp ); ?>
              <?php $day = date( 'd' , $date_post_timestamp ); ?>
              <a target="_blank" href="<?php echo get_year_link( $year ); ?>"><?php printf( __( 'Year: %s' , 'my-wp' ) , get_the_date( _x( 'Y' , 'yearly archives date format' ) , $date_post->ID ) ); ?></a>
              &nbsp;
              <a target="_blank" href="<?php echo get_month_link( $year , $month ); ?>"><?php printf( __( 'Month: %s' , 'my-wp' ) , get_the_date( _x( 'F Y' , 'monthly archives date format' ) , $date_post->ID ) ); ?></a>
              &nbsp;
              <a target="_blank" href="<?php echo get_day_link( $year , $month , $day ); ?>"><?php printf( __( 'Day: %s' , 'my-wp' ) , get_the_date( _x( 'F j, Y' , 'daily archives date format' ) , $date_post->ID ) ); ?></a>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['disable_archive'] ) ) {

      $new_formatted_data['disable_archive'] = true;

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenFrontendDateArchive::init();

endif;
