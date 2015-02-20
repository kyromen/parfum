jQuery.noConflict();
(function($) {
    $(function() {
        $('.jcarousel').each(function () {
            $( this )
                .on('jcarousel:reload jcarousel:create', function () {
                    var width = $( this ).innerWidth();

                    if (width >= 600) {
                        width = width / 5;
                    } else if (width >= 350) {
                        width = width / 3;
                    }

                    $( this ).jcarousel('items').css('width', width + 'px');
                })
                .jcarousel({
                    wrap: 'circular'
                });
        });

        $('.jcarousel-control-prev').each( function() {
            $( this )
                .jcarouselControl({
                    target: '-=4'
                });
        });

        $('.jcarousel-control-next').each( function() {
            $( this )
                .jcarouselControl({
                    target: '+=4'
                });
        });

        $('.jcarousel-pagination').each( function() {
            $( this )
                .on('jcarouselpagination:active', 'a', function() {
                    $(this).addClass('active');
                })
                .on('jcarouselpagination:inactive', 'a', function() {
                    $(this).removeClass('active');
                })
                .on('click', function(e) {
                    e.preventDefault();
                })
                .jcarouselPagination({
                    perPage: 4,
                    item: function(page) {
                        return '<a href="#' + page + '">' + page + '</a>';
                    }
                });
        });
    });
})(jQuery);
