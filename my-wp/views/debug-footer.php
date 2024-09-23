<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloper' ) ) {
  return false;
}

$debug_types = MywpDeveloper::get_debug_types();

if( empty( $debug_types ) ) {

  return false;

}

$debug_renders = MywpDeveloper::get_debug_renders();

if( empty( $debug_renders ) ) {

  return false;

}

foreach( $debug_types as $debug_type => $type_label ) {

  $active = $debug_type;

  break;

}

wp_enqueue_script( 'jquery' );

?>
<div class="clear"></div>

<div id="mywp-debug-wrap">

  <div id="mywp-debug">

    <p class="main-title"><?php _e( 'My WP Debug' , 'my-wp' ); ?></p>

    <p class="debug-types">

      <?php foreach( $debug_types as $debug_type => $type_label ) : ?>

        <?php $add_class = ''; ?>

        <?php if( (string) $debug_type === (string) $active ) : ?>

          <?php $add_class = 'active '; ?>

        <?php endif; ?>

        <a href="javascript:void(0);" class="type-select type-<?php echo sanitize_html_class( $debug_type ); ?> <?php echo sanitize_html_class( $add_class ); ?>" data-type="<?php echo esc_attr( $debug_type ); ?>">
          <?php echo $type_label; ?>
        </a>

      <?php endforeach; ?>

    </p>

    <div class="debug-renders">

      <?php foreach( $debug_renders as $render_id => $render ) : ?>

        <?php $debug_type = $render['debug_type']; ?>

        <?php $add_class = ''; ?>

        <?php if( (string) $debug_type === (string) $active ) : ?>

          <?php $add_class = 'active '; ?>

        <?php endif; ?>

        <div class="render render-<?php echo sanitize_html_class( $render_id ); ?> type-<?php echo sanitize_html_class( $debug_type ); ?> <?php echo sanitize_html_class( $add_class ); ?>">

          <div class="render-content-wrap" id="<?php echo sanitize_html_class( $render_id ); ?>">

            <?php if( ! empty( $render['title'] ) ) : ?>

              <p class="render-title"><?php echo $render['title']; ?></p>

            <?php endif; ?>

            <div class="render-content">

              <?php do_action( "mywp_debug_render_{$render_id}" ); ?>
              <?php do_action( 'mywp_debug_render' , $render_id ); ?>

              <!-- do_action( "mywp_debug_render_<?php echo $render_id; ?>" ); -->

            </div><!-- .render-content -->

          </div><!-- .render-content-wrap -->

        </div><!-- .render -->

      <?php endforeach; ?>

    </div><!-- .debug-renders -->

    <?php do_action( 'mywp_debug_render_footer' ); ?>

  </div><!-- #mywp-debug -->

</div><!-- #mywp-debug-wrap -->
