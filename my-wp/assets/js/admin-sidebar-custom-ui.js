jQuery(function( $ ) {

  $('#adminmenu li').not('#sidebar-collapse').off('click');

  $('#adminmenu > li > a').on('click', function() {

    let $body = $('body');
    let $menu_item_link = $(this);
    let $menu_item = $menu_item_link.parent();
    let $submenu_items = $menu_item.find('ul.wp-submenu');

    if( $body.hasClass('folded') ) {

      $body.removeClass('folded');

    }

    if( $submenu_items.length > 0 ) {

      $menu_item.toggleClass('selected');

      return false;

    }

    return true;

  });

  $('#sidebar-custom-menu-ui-mask').on('click', function() {

    $('#wpwrap').removeClass('wp-responsive-open');

  });

  $(document).on('wp-menu-state-set', function() {

    $('#adminmenu').data( 'wp-responsive', 1 );

  });

});
