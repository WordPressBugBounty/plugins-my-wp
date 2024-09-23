<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugTerms' ) ) :

final class MywpSettingScreenDebugTerms extends MywpAbstractSettingModule {

  static protected $id = 'debug_terms';

  static protected $priority = 40;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Terms' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $all_taxonomies = MywpApi::get_all_taxonomies();

    if( empty( $all_taxonomies ) ) {

      return false;

    }

    $all_terms = array();

    $args = array(
      'hide_empty' => false,
    );

    foreach( $all_taxonomies as $taxonomy ) {

      $terms = get_terms( $taxonomy->name , $args );

      if( empty( $terms ) or is_wp_error( $terms ) ) {

        continue;

      }

      foreach( $terms as $term ) {

        $all_terms[] = $term;

      }

    }

    ?>
    <p><?php _e( 'Count' , 'my-wp' ); ?>: <?php echo count( $all_terms ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_terms as $term ) : ?>
          <tr>
            <th>
              [<?php echo $term->slug; ?>] <?php echo $term->name; ?><br />
              <a href="<?php echo esc_url( add_query_arg( array( 'taxonomy' => $term->taxonomy , 'tag_ID' => $term->term_id ) , admin_url( 'term.php' ) ) ); ?>"><?php _e( 'Edit' ); ?></a>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $term ); ?></textarea>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugTerms::init();

endif;
