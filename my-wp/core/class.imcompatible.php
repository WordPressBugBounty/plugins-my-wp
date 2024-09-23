<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpIncompatible' ) ) :

final class MywpIncompatible {

  public static function init() {

    add_action( 'admin_notices' , array( __CLASS__ , 'admin_notices' ) );

    add_action( 'network_admin_notices' , array( __CLASS__ , 'admin_notices' ) );

  }

  public static function admin_notices() {

    if( is_multisite() ) {

      if( ! MywpApi::is_network_manager() ) {

        return false;

      }

    } else {

      if( ! MywpApi::is_manager() ) {

        return false;

      }

    }

    ?>

    <div class="error">
      <p>
        <?php printf( __( 'Sorry, My WP is <strong>Incompatible</strong> with your version of WordPress. Require version  %s.' , 'my-wp' ) , MYWP_REQUIRED_WP_VERSION ); ?>
      </p>
    </div>

    <?php

  }

}

endif;
