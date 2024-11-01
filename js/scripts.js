jQuery(document).ready(function($){
    "use strict";

    /* MASONRY ITEMS */
    var $container = $('.srp-masonry');
    var has_masonry = false;

    // initialize
    function start_masonry(){
        if( $(window).width() < 600 && has_masonry ){
            $container.masonry('destroy');
            has_masonry = false;
            
        } else if( $(window).width() >= 600 && !has_masonry ){
            $container.masonry({
                itemSelector: '.masonry-item',
                columnWidth: '.masonry-item',
            });
            has_masonry = true;
        }
    }

    start_masonry();
    $(window).resize(function(){
        setTimeout( function(){
            start_masonry();
        }, 500);
    });

});
