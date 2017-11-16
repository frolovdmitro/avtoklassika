(function($) {
    $.fn.uwinTable = function(method) {

        var methods = {
            init : function(options) {
                this.uwinTable.settings = $.extend({}, this.uwinTable.defaults, options);

                return this.each(function() {
                    //noinspection JSUnusedLocalSymbols
                    var $element = $(this),
                         element = this;

                    // Плейсхолдер для поля поиска по таблице
                    $('.search').keyup(function(){
                        var label = $(this).parent().find('label');

                        if ( '' !== $(this).val()) {
                            label.css('font-size', 0);
                        } else {
                            label.css('font-size', 13);
                        }
                    });

                    $('.draggable-area').dragndrop({parentDragElement: 'tr'}, function(){
                        helpers.movedRows($element, this);
                    });

                    // Навешивае событие на клик по заглавию столбца таблицы,
                    // который можно соритироват
                    $element.find('.ordered').click(function() {
                        helpers.getRows($element, $(this), true);
                    });

                    $element.next().find("#rowsOnPage").change(function() {
                        $('#numPage').val(1);
                        $('.numPage').html(1);
                        helpers.getRows($element, $element.find('.order'), false);
                    });
                    
                    $element.next().find("#numPage").blur(function() {
                        $('.numPage').html( $(this).val() );
                        helpers.getRows($element, $element.find('.order'), false);
                    });

                    $element.next().find("#numPage").keypress(function(e) {
                        if (e.keyCode == 13) {
                            $('.numPage').html( $(this).val() );
                            helpers.getRows($element, $element.find('.order'), false);
                        }
                    });

                    $(".page-control .prev").click(function() {
                        if ( $('#numPage').val() <= 1 ) {
                            $('#numPage').val(parseInt($('.countPages').html()));
                        } else {
                            $('#numPage').val( parseInt($('#numPage').val()) - 1 );
                        }
                        $('.numPage').html( $('#numPage').val() );

                        helpers.getRows($element, $element.find('.order'), false);
                    });

                    $(".page-control .next").click(function() {
                        if ( $('#numPage').val() >= parseInt($('.countPages').html()) ) {
                            $('#numPage').val(1);
                        } else {
                            $('#numPage').val( parseInt($('#numPage').val()) + 1 );
                        }
                        $('.numPage').html( $('#numPage').val() );
                        
                        helpers.getRows($element, $element.find('.order'), false);
                    });

                    $element.prev().find(".search").keypress(function(e) {
                        if (e.keyCode == 13) {
                            $('#numPage').val(1);
                            $('.numPage').html( $('#numPage').val() );
                            helpers.getRows($element, $element.find('.order'), false);
                        }
                    });

                    $element.prev().find(".search-btn").click(function() {
                        $('#numPage').val(1);
                        $('.numPage').html( $('#numPage').val() );
                        helpers.getRows($element, $element.find('.order'), false);
                    });
                });
            },

            refresh: function() {
                helpers.getRows($(this), $(this).find('.order'), false);
            }
        };

        var helpers = {
            movedRows: function(element, dragged) {
                var url = $.fn.uwinTable.settings.url.replace('/tabledata/', '/moverow/') + document.location.search;
                var table = element.attr("id");
                var pk = element.attr("data-pk-column");
                var orderColumn = element.attr("data-order-column");
                var sourceId = dragged.sourceId;
                var movedId = dragged.movedId;
                $.post(url, {
                    table: table,
                    pk: pk,
                    orderColumn: orderColumn,
                    sourceId: sourceId,
                    movedId: movedId
                }, function(){
                    var sourceRow = element.find('[data-id='+ sourceId +']');
                    var movedRow = element.find('[data-id='+ movedId +']');

                    sourceRow.attr('id', movedId).attr('data-id', movedId);
                    movedRow.attr('id', sourceId).attr('data-id', sourceId);
                });
            },

            getRows: function(element, column, changeOrderType) {
                var url = $.fn.uwinTable.settings.url;
                var table = element.attr("id");
                var orderColumn = column.attr("id");
                if (undefined == orderColumn) {
                    orderColumn = '';
                }
                
                var orderType   = "desc";
                if ( column.hasClass("desc") ) {
                    if (changeOrderType) {
                        orderType   = "asc";
                    } else {
                        orderType   = "desc";
                    }
                } else
                if ( column.hasClass("asc") ) {
                    if (changeOrderType) {
                        orderType   = "desc";
                    } else {
                        orderType   = "asc";
                    }
                }

                var rowsOnPage = '';
                var rowsOnPageUrl = '&rowsOnPage=';
                if( $('#rowsOnPage').length ) {
                    rowsOnPage += $('#rowsOnPage').val();
                    rowsOnPageUrl += rowsOnPage;
                }

                var numPage = $('#numPage').val();
                var numPageUrl = '&numPage=';
                if( $('#numPage').length ) {
                    numPageUrl += numPage;
                }

                var quickFilter = $('.search').val();
                var quickFilterUrl = '&quickFilter=' + decodeURI(quickFilter);

                var language = $('#module-languages .active').attr('data-lang');
                var languageUrl = '&language=';
                if (undefined != language) {
                    languageUrl = '&language=' + decodeURI(language);
                }

                var getVars = window.location.href.slice(
                        window.location.href.indexOf('?') + 1);

                if (null != getVars) {
                    getVars = '&' + getVars;
                }
                // Посылаем ajax-запрос на сервер с переменными: имя
                // таблицы, по какому полю сортировать, в каком порядке,
                // какая активная страница, сколько записей на одной
                // странице
                $.get(
                    url + "?table=" + table
                        + '&orderColumn=' + orderColumn
                        + '&orderType=' + orderType + rowsOnPageUrl
                        + numPageUrl + languageUrl + quickFilterUrl + getVars,
                    function(data) {
                        data = JSON.parse(data);

                        $('.countPages').html(data['count_rows']);

                        element.find("th").removeClass("order")
                                .removeClass("desc")
                                .removeClass("asc");

                        column.addClass("order").addClass(orderType);
                        element.find('tbody').html(data['rows']);

                        $('.draggable-area').dragndrop({parentDragElement: 'tr'}, function(){
                            console.log(this);
                        });

                        $('#main-content table').find('tbody a :not(.direct-link)').click(function() {
                            var url = $(this).attr("href");

                            $('.module-tabs a').removeClass('active');
                            $('.module-tabs a[href$="' + url + '"]').addClass('active');

                            history.pushState(null, null, url);

                            getContentSubModule($(this), 'next');

                            return false;
                        });

                        // Навешиваем обработчики на действия, который отображают форму
                        showForm();

                        // Сохраняю данные о том по какой колонке и в
                        // каком направлении идет сортировка таблицы в
                        // localStorage

                        var tablesParams = {};
                        var saveTables = localStorage.getItem('uws/' + location.pathname);
                        if (saveTables != null) {
                            tablesParams = JSON.parse(saveTables);
                        }


                        tablesParams[table] =
                            {
                                "column"      : orderColumn,
                                "type"        : orderType,
                                "rowsOnPage"  : rowsOnPage,
                                "currentPage" : numPage,
                                "quickFilter" : quickFilter,
                                "language"    : language
                            };

                        localStorage.setItem('uws/' + location.pathname,
                                JSON.stringify(tablesParams) );
                    }
                );
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in pluginName plugin!');
        }
    };

    $.fn.uwinTable.defaults = {
        url: null
    };

    $.fn.uwinTable.settings = {}
})(jQuery);