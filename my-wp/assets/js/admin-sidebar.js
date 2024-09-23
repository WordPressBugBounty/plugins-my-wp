jQuery(function( $ ) {

  function getViewportWidth() {

    let viewportWidth = false;

    if ( window.innerWidth ) {

      // On phones, window.innerWidth is affected by zooming.
      viewportWidth = Math.max( window.innerWidth, document.documentElement.clientWidth );

    }

    return viewportWidth;

  }

  $('#adminmenu > li#sidebar-collapse').on('click', function() {

    let $document = $(document);
    let $body = $document.find('body');

    let viewportWidth = getViewportWidth() || 961;

    $('#adminmenu div.wp-submenu').css('margin-top', '');

    if ( viewportWidth < 960 ) {

      if ( $body.hasClass('auto-fold') ) {

        $body.removeClass('auto-fold').removeClass('folded');

        setUserSetting('unfold', 1);

        setUserSetting('mfold', 'o');

        menuState = 'open';

      } else {

        $body.addClass('auto-fold');

        setUserSetting('unfold', 0);

        menuState = 'folded';

      }

    } else {

      if ( $body.hasClass('folded') ) {

        $body.removeClass('folded');

        setUserSetting('mfold', 'o');

        menuState = 'open';

      } else {

        $body.addClass('folded');

        setUserSetting('mfold', 'f');

        menuState = 'folded';

      }

    }

    $document.trigger( 'wp-menu-state-set', { state: menuState } );

  });

});
