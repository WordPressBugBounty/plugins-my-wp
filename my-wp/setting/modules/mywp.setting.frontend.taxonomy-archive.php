<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenFrontendTermArchive' ) ) :

final class MywpSettingScreenFrontendTermArchive extends MywpAbstractSettingModule {

  static protected $id = 'frontend_taxonomy_archive';

  static protected $priority = 40;

  static private $menu = 'frontend';

  static private $taxonomy = '';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Taxonomy Archive' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'frontend_taxonomy_archive',
      'document_url' => self::get_document_url( 'document/frontend-taxonomy-archive/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_load_setting_screen() {

    $current_setting_taxonomy_name = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( ! empty( $current_setting_taxonomy_name ) ) {

      self::$taxonomy = $current_setting_taxonomy_name;

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$taxonomy ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$taxonomy;

    return $option_key;

  }

  public static function mywp_current_setting_screen_header() {

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-select-taxonomy.php' );

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $current_setting_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();
    $current_setting_taxonomy = MywpSettingTaxonomy::get_current_taxonomy();

    if( empty( $current_setting_taxonomy ) ) {

      printf( __( '%1$s: %2$s is not found.' , 'my-wp' ) , __( 'Invalid Taxonomy' , 'my-wp' ) , $current_setting_taxonomy_id );

      return false;

    }

    $one_term = MywpSettingTaxonomy::get_one_term( $current_setting_taxonomy_id );
    $term_archive_link = MywpSettingTaxonomy::get_one_term_archive_link( $current_setting_taxonomy_id );

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php printf( __( 'Taxonomy archive of %s' , 'my-wp' ) , $current_setting_taxonomy->label ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_archive]" class="disable_archive" value="1" <?php checked( $setting_data['disable_archive'] , true ); ?> />
              <?php _e( 'Disable' , 'my-wp' ); ?>
            </label>
            &nbsp;
            <?php if( ! empty( $one_term ) ) : ?>
              <a target="_blank" href="<?php echo esc_url( $term_archive_link ); ?>"><?php printf( __( '%1$s: %2$s' ) , $current_setting_taxonomy->label , $one_term->name ); ?></a>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_remove_form() {

    $current_setting_taxonomy_id = MywpSettingTaxonomy::get_current_taxonomy_id();

    if( empty( $current_setting_taxonomy_id ) ) {

      return false;

    }

    ?>

    <input type="hidden" name="mywp[data][taxonomy]" value="<?php echo esc_attr( $current_setting_taxonomy_id ); ?>" />

    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['taxonomy'] ) ) {

      $new_formatted_data['taxonomy'] = strip_tags( $formatted_data['taxonomy'] );

    }

    if( ! empty( $formatted_data['disable_archive'] ) ) {

      $new_formatted_data['disable_archive'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_post_data_format_remove( $formatted_data ) {

    if( ! empty( $formatted_data['taxonomy'] ) ) {

      $formatted_data['taxonomy'] = strip_tags( $formatted_data['taxonomy'] );

    }

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_validate_update( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['taxonomy'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'taxonomy' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_post_data_validate_remove( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['taxonomy'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'taxonomy' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_before_post_data_action_update( $validated_data ) {

    if( ! empty( $validated_data['taxonomy'] ) ) {

      self::$taxonomy = $validated_data['taxonomy'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    if( ! empty( $validated_data['taxonomy'] ) ) {

      self::$taxonomy = $validated_data['taxonomy'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

}

MywpSettingScreenFrontendTermArchive::init();

endif;
