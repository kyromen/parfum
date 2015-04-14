/* Author:

*/


jQuery.noConflict();
(function( $ ) {
    $(function() {
        function setFastView(ident) {
            $(''+ ident +' .product button.modal, .vmgroup_mainPage .center button.modal').click(function(){
                var url = $(this).attr('href');
                $.fancybox({
                    type: 'iframe',
                    href: url,
                    //width: $(window).width() < 980 ? 980:$(window).width()-200,
                    width: 750,
                    height: 1000,
                    autoScale: false,
                    titlePosition: 'over',
                    onComplete: function(){
                        /*var cssLink = document.createElement("link")
                         cssLink.href = "/templates/zvv/css/iframe.css";
                         cssLink.rel = "stylesheet";
                         cssLink.type = "text/css";
                         $('iframe#fancybox-frame').load(function() {
                         $(this).contents().find('head').append(cssLink);
                         });*/

                        $('iframe#fancybox-frame').load(function() {
                            $(this).parent().height($(this).contents().find('body').height() + 20);
                        });

                        jQuery(".additional-images .product-image").click(function() {
                            jQuery(".main-image img").attr("src",this.src );
                            jQuery(".main-image img").attr("alt",this.alt );
                            jQuery(".main-image a").attr("href",this.src );
                            jQuery(".main-image a").attr("title",this.alt );
                            jQuery(".mainImg").removeClass("mainImg");
                            jQuery("#fancybox-content").css('padding', '0');
                        });


                    }
                })
            });
            if( !(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ) {
                $(''+ ident +' .product .productImg, .vmgroup_mainPage .center').hover(
                    function(){
                        $(this).parent().find('button.modal').css('visibility', 'visible');
                    },
                    function() {
                        $(this).parent().find('button.modal').css('visibility', 'hidden');
                    }
                );
                $(''+ ident +' button.modal').hover(
                    function(){
                        $(this).css('visibility', 'visible');
                        $(this).parent().find('.productImg').addClass('hover');
                    },
                    function() {
                        $(this).css('visibility', 'hidden');
                        $(this).parent().find('.productImg').removeClass('hover');
                    }
                );
            }
        }
        setFastView('.row');
        $('.chooseColour').each(function() {
            if($(this).find('label').length > 8) {
                $(this).css('width', '135px');
                $(this).css('margin', 'auto');
            }
        });

        var pagination = $('.vm-pagination span.pagenav').parent();
        pagination.each(function(i, e) {
            if($(e).attr('class') == null) {
                $(e).attr('id', 'current-page');
            }
        });

        // pagination.hide();
        pagination.parent().before('<a id="itemsUpload" href="#load-more">Показать ещё</a><div class="horizontal-separator"></div>');
        var pageNum = 0;
        $('#itemsUpload').click(function() {
            pageNum += 1;
            if($('#current-page').next().find('a').attr('href') != undefined) {
                var request = $.ajax({
                    url: $('#current-page').next().find('a').attr('href'),
                    dataType: "html"
                })
                    .fail(function( msg ) {
                        console.log(msg);
                    })
                    .done(function( data ) {
                        if($('#current-page').next().attr('class') == null) {
                            $('#current-page').removeAttr('id').next().attr('id', 'current-page');
                            if($('#current-page').next().attr('class') != null) $('#itemsUpload').hide();
                            //$('.itemsUpload').hide();
                        }

                        var rowItems = $('.browse-view .row', data);
                        var loadedItems = $(rowItems).html();
                        for(var i = 0; i < rowItems.length; i++) {
                            console.log(rowItems[i]);
                            $(rowItems[i]).addClass('loaded'+pageNum).insertBefore('.browse-view .vm-pagination');
                        }
                        setFastView('.row.loaded'+pageNum);
                        $('.row.loaded'+pageNum).hide();
                        $('.row.loaded'+pageNum).fadeIn(1500);
                    });
            }
            return false;
        });

        if ( $( ".brands > div" ).length == 1 ) {
            $( ".brands > div").css( { "display": "block" } );
        }
        // Select
        $('.slct').click(function(){
            /* Заносим выпадающий список в переменную */
            var dropBlock = $(this).parent().find('.drop');

            /* Делаем проверку: Если выпадающий блок скрыт то делаем его видимым*/
            if( dropBlock.is(':hidden') ) {
                dropBlock.slideDown();

                /* Выделяем ссылку открывающую select */
                $(this).addClass('active');

                /* Работаем с событием клика по элементам выпадающего списка */
                $('.drop').find('li').click(function(){

                    /* Заносим в переменную HTML код элемента
                     списка по которому кликнули */
                    var selectResult = $(this).html();

                    /* Находим наш скрытый инпут и передаем в него
                     значение из переменной selectResult */
                    $(this).parent().parent().find('input').val(selectResult);

                    /* Передаем значение переменной selectResult в ссылку которая
                     открывает наш выпадающий список и удаляем активность */
                    $(this).parent().parent().find('.slct').removeClass('active').html(selectResult);

                    /* Скрываем выпадающий блок */
                    dropBlock.slideUp();
                });

                /* Продолжаем проверку: Если выпадающий блок не скрыт то скрываем его */
            } else {
                $(this).removeClass('active');
                dropBlock.slideUp();
            }

            /* Предотвращаем обычное поведение ссылки при клике */
            return false;
        });

        $('.brand-index > div').click(function() {
            $('.brand-index > div').removeClass('active');
            $( this ).toggleClass('active');
            var ind = $( '.brand-index > div' ).index( this );
            $( '.brands > div' ).each( function() { $( this ).css( { "display": "none" } ) });
            $( '.brands > div' ).eq( ind ).css( { "display": "block" } );
        });

        $('.telAddr .callBack').colorbox({
            onComplete : function() {
                $(this).colorbox.resize();
            }
        });

        $('a.fast-order').click( function() {
            $( this ).colorbox({
                onComplete: function () {
                    $(this).colorbox.resize();
                    $('#chronoform-FastOrder').find('#prname').val($('.productdetails-view h1').text());
                    $('#chronoform-FastOrder').find('#prvolume').val($( this).parent().parent().parent().find('.pr_volume').text());
                    $('#chronoform-FastOrder').find('#prtype').val($( this ).parent().parent().parent().find('.pr_type').text());
                    $('#chronoform-FastOrder').find('#prsku').val($('.pr_sku').text());
                }
            });
        })

        $( '#contentCenter' ).on('change', "select.custom-volume", function() {
            var ind = this.selectedIndex;

            $( this ).parent().find( '.vm3pr-2' ).css( { "display": "none" } );
            $( this ).parent().find( '.vm3pr-2' ).eq( ind ).css( { "display": "block" } );

            $( this ).parent().find( '.vm3pr-0' ).css( { "display": "none" } );
            $( this ).parent().find( '.vm3pr-0' ).eq( ind ).css( { "display": "block" } );
        });
    });
})(jQuery);