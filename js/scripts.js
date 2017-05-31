jQuery(document).scroll(function() {

    if(jQuery(window).scrollTop() > 100) {

        jQuery('.fl-page-header').css('background-color', 'rgba(10,96,183,1)');

    } else {

        jQuery('.fl-page-header').css('background-color', 'rgba(10,96,183,0)');

    }

});
