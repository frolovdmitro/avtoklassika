// @import include/codemirror/lib/codemirror.js
// @import include/codemirror/mode/css/css.js
// @import include/codemirror/mode/htmlmixed/htmlmixed.js

// @import include/uwinTable.js
// @import include/jquery.form.js
// @import include/spin.min.js
// @import include/shortcut.js
// @import include/jquery.contenteditable.js

/**
 * Точка входа
 */
$(document).ready(function() {
    if (!$.browser.webkit) {
        $('body').html('<div class="browser-not-suuport"><h1>Ваш браузер мы&nbsp;не&nbsp;поддерживаем</h1><p>На&nbsp;данный момент панель управления поддерживает браузеры <a target="_blank" href="http://www.google.com/chrome?hl=ru">Google Chrome</a> и&nbsp;<a target="_blank" href="http://www.apple.com/ru/safari/download/">Apple Safari</a>.</p><p>Вы&nbsp;можете скачать один из&nbsp;этих браузеров по&nbsp;ссылкам ниже и&nbsp;приступить к&nbsp;работе в&nbsp;панели управления вашим сайтом.</p><ul><li><a target="_blank" class="chrome-128-ico" href="http://www.google.com/chrome?hl=ru" title="Загрузить Google Chrome"></a></li><li><a target="_blank" class="safari-128-ico" href="http://www.apple.com/ru/safari/download/" title="Загрузить Apple Safari"></a></li></ul></div>');
    }

    if($('#sign-in-form').length) {
        loginAdmin();
    }

    $('.visible-module-submenu').each(function() {
        var id = $(this).attr('id');
        if ( 'true' == localStorage.getItem(id) ) {
            getStateExpandSubModule(id, false);
        }
    });

    // Эффекты =================================================================
    setContentHeight();
    $(window).resize(function() {
        (setContentHeight())();
    });

    $('.visible-module-submenu').click(function() {
        expandSubModule($(this).attr('id'));

        return false;
    });

    // Смена блока с контентом с помощью AJAX и HistoryAPI
    var moduleMenuLink = $('.module-menu .caption, ' +
            '.module-submenu a, .mainmenu a');
    moduleMenuLink.click(function(e) {
        var moduleMenuLink = $('.module-menu a, .mainmenu a');

        moduleMenuLink.removeClass('active');

        $(this).addClass('active')
               .parentsUntil('.module-menu')
               .children('.caption')
               .addClass('active');

        var url = $(this).attr("href");

        history.pushState(null, null, url);
        getContentModule(url);

        e.preventDefault();
    });

    getContentModule(location.pathname);
    var getContent = true;
    window.setTimeout(function() {
        $(window).bind("popstate", function(e) {
            if (false === getContent) {
                getContentModule(location.pathname);
            }
            getContent = false;

            e.preventDefault();
        });
    }, 1);

    // Сохраняем адрес текущей страницы, чтобы при логине открыть ее
    $(window).unload(function() {
        localStorage.setItem('last-admin-page', window.location.href);
    });
});


function blockForm(spin, form) {
    form.find('INPUT, TEXTAREA, SELECT, LABEL, BUTTON, SUBMIT, H3').attr('disabled', 'disabled');

    var errors = $('.error');
    var inputs = $('.input-error');
    var languages = $('#form-languages');
    errors.css('opacity', 0);
    inputs.removeClass('input-error');
    languages.find('A').removeClass('lang-error');

    var opts = {
        lines: 10, // The number of lines to draw
        length: 4, // The length of each line
        width: 2, // The line thickness
        radius: 4, // The radius of the inner circle
        color: '#000', // #rgb or #rrggbb
        speed: 0.9, // Rounds per second
        trail: 79, // Afterglow percentage
        shadow: false // Whether to render a shadow
    };

    new Spinner(opts).spin(spin[0]);
}

function unblockForm(spin, form) {
    spin.remove();
    form.find('INPUT, TEXTAREA, SELECT, LABEL, BUTTON, SUBMIT, H3').removeAttr('disabled');
}

/**
 *
 * @param id
 */
function expandSubModule(id) {
    var switcher = $('#'+id);
    var submenu = switcher.next();

    submenu.slideToggle(250, function() {
        setContentHeight();
        if (submenu.css("display") == "none") {
            localStorage.setItem(switcher.attr('id'), true);
            switcher.html('Показать');
        } else {
            localStorage.removeItem(switcher.attr('id'));
            switcher.html('Скрыть');
        }
    });
}

/**
 *
 * @param id
 * @param expand
 */
function getStateExpandSubModule(id, expand) {
    var switcher = $('#'+id);
    var submenu = switcher.next();

    setContentHeight();
    if (expand === false) {
        switcher.html('Показать');
        submenu.css("display", 'none');
    } else {
        switcher.html('Скрыть');
        submenu.css("display", '');
    }
}

/**
 * Функция устанавливает высоту контейнера админки равную высоту окна, если
 * она меньше высоты окна
 */
function setContentHeight() {
    var wrap = $('#wrap');
    wrap.css("height", '');

    // выставление высоты контентной части
    var mainContent = $('#main-content');
    mainContent.css("height", '');

    wrap.css("height", $(window).height());

    var mainContentMargin =
            parseInt(mainContent.css("margin-top").replace("px", "")) +
            parseInt(mainContent.css("margin-bottom").replace("px", ""));

    var mainContentHeight = $('.main').height() - mainContentMargin
            - $('.mainmenu-wrap').height();

    if (mainContent.height() < mainContentHeight) {
        mainContent.css("height", mainContentHeight);
    }
}

/**
 *
 * @param url
 * @param type
 */
function getUrlNewType(url, type) {
    // Удаляем первый и последние слеши
    if (url.charAt(0) == "/") {
        url = url.substr(1);
    }
    if (url.charAt(url.length - 1) == "/") {
        url = url.substr(0, url.length - 1);
    }

    url = url.split('/');
    url.splice(1, 0, type);
    url = "/" + url.join('/');

    if ( -1 == url.indexOf("?") ) {
        url += "/";
    }

    return url;
}

function getModuleParams() {
    var tablesParam  = null;
    var orderColumns = "&";
    var orderTypes   = "&";
    var rowsOnPage   = "&";
    var currentPage  = "&";
    var quickFilter  = "&";
    var language     = "&";

    var getVars = "&";
    if ( -1 != window.location.href.indexOf("?") ) {
        getVars = '&' + window.location.href.slice(
                window.location.href.indexOf('?') + 1);
    }

    if ( localStorage.getItem('uws/' + location.pathname) != undefined) {
        tablesParam = JSON.parse(localStorage.getItem('uws/' + location.pathname) );

        if (undefined != tablesParam['language']) {
            language += 'language=' + tablesParam['language'] + '&';
        } else {
            for (var tableName in tablesParam) {
                orderColumns += tableName + '_orderColumn=' + tablesParam[tableName]["column"] + "&";
                orderTypes   += tableName + '_orderType=' + tablesParam[tableName]["type"] + "&";
                rowsOnPage   += tableName + '_rowsOnPage=' + tablesParam[tableName]["rowsOnPage"] + "&";
                currentPage  += tableName + '_currentPage=' + tablesParam[tableName]["currentPage"] + "&";
                quickFilter  += tableName + '_quickFilter=' + tablesParam[tableName]["quickFilter"] + "&";
                if (undefined != tablesParam[tableName]["language"]) {
                    language += tableName + '_language=' + tablesParam[tableName]["language"] + "&";
                } else {
                    language += tableName + '_language=&';
                }
            }
        }
    }
    if (orderColumns.charAt(orderColumns.length - 1) == "&") {
        orderColumns = orderColumns.substr(0, orderColumns.length - 1);
    }
    if (orderTypes.charAt(orderTypes.length - 1) == "&") {
        orderTypes = orderTypes.substr(0, orderTypes.length - 1);
    }
    if (rowsOnPage.charAt(rowsOnPage.length - 1) == "&") {
        rowsOnPage = rowsOnPage.substr(0, rowsOnPage.length - 1);
    }
    if (currentPage.charAt(currentPage.length - 1) == "&") {
        currentPage = currentPage.substr(0, currentPage.length - 1);
    }
    if (quickFilter.charAt(quickFilter.length - 1) == "&") {
        quickFilter = quickFilter.substr(0, quickFilter.length - 1);
    }
    if (language.charAt(language.length - 1) == "&") {
        language = language.substr(0, language.length - 1);
    }
    if (getVars.charAt(getVars.length - 1) == "&") {
        getVars = getVars.substr(0, getVars.length - 1);
    }

    return orderColumns + orderTypes + rowsOnPage + currentPage
            + quickFilter + language + getVars;
}

$.fn.wait = function(time, type) {
       time = time || 1000;
       type = type || "fx";
       return this.queue(type, function() {
           var self = this;
           setTimeout(function() {
               $(self).dequeue();
           }, time);
       });
   };

/**
 *
 * @param url
 */
function getContentModule(url) {
    var clearUrl = url;
    url = getUrlNewType(url, "content");
    var params = getModuleParams();

    $.get(
        url + "?t=1" + params,
        function(data) {
            $('#main-content').html(data);

            $('.languageform TEXTAREA').each(function() {
                var editor = CodeMirror.fromTextArea(this, {
                        mode: {name: "xml", alignCDATA: true},
                        lineNumbers: true,
                        lineWrapping: true,
                        indentUnit: 4,
                        tabSize: 4,
                        autofocus: true,
                        indentWithTabs: true
                      });
                CodeMirror.commands["selectAll"](editor);
                editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
                CodeMirror.commands["goDocStart"](editor);
            });

            if ( localStorage.getItem('uws/' + location.pathname) != undefined) {
                var tablesParam = JSON.parse(localStorage.getItem('uws/' + location.pathname) );

                if (undefined != tablesParam["language"]) {
                    $('#settings-languages').find('.active').removeClass('active');
                    $('#settings-languages').find('[data-lang=' + tablesParam["language"] + ']')
                        .addClass('active');
                } else {
                    for (var tableName in tablesParam) {
                        if ( 0 == $('#' + tableName).length) {
                            continue;
                        }

                        if (undefined != tablesParam[tableName]["language"]) {
                            $('#module-languages').find('.active').removeClass('active');
                            $('#module-languages').find('[data-lang=' + tablesParam[tableName]["language"] + ']')
                                .addClass('active');
                        }
                    }
                }
            }
            $('#module-languages A').click(function(){
                $(this).closest('UL').find('LI').removeClass('active');
                $(this).closest('LI').addClass('active');

                $('#main-content table').uwinTable('refresh');

                return false;
            });

            $('#settings-languages A').click(function(){
                $(this).closest('UL').find('LI').removeClass('active');
                $(this).closest('LI').addClass('active');

                var module_params = {};
                module_params.language = $(this).closest('LI').attr('data-lang');
                localStorage.setItem('uws/' + location.pathname,
                        JSON.stringify(module_params) );

                getContentModule(clearUrl);

                return false;
            });

            if ( '' != $('.search').val() ) {
                $('.search').parent().find('label').css('font-size', 0);
            }
            // Навешиваем на все таблицы контентной части плагин управления таблицей
            $('#main-content table').uwinTable({
                url: getUrlNewType(location.pathname, "tabledata")
            });

            $('#main-content table').find('tbody a:not(.direct-link, .row-action)').click(function() {
                var url = $(this).attr("href");

                $('.module-tabs a').removeClass('active');
                $('.module-tabs a[href$="' + url + '"]').addClass('active');

                history.pushState(null, null, url);

                getContentSubModule($(this), 'next');

                return false;
            });

            $('.module-tabs a').click(function(e) {
                var moduleTabs = $('.module-tabs a');

                moduleTabs.removeClass('active');

                $(this).addClass('active');

                var url = $(this).attr("href");

                history.pushState(null, null, url);
                getContentModule(url);

                e.preventDefault();
            });

            $('.crumbs a').click(function() {
                var url = $(this).attr("href");

                $('.module-tabs a').removeClass('active');
                $('.module-tabs a[href$="' + url + '"]').addClass('active');

                history.pushState(null, null, url);

                getContentSubModule($(this), 'prev');

                return false;
            });

            setContentHeight();
            // Навешиваем обработчики на действия, который отображают форму
            showForm();

            // Устанавливаем все плейсхолдеры
            var inputs = $('.pageform').find('INPUT[placeholder], TEXTAREA[placeholder]');
            createPlaceholder(inputs);

            buildContentEditable();

            var language = null;
            if ( 0 != $('#settings-languages').length ) {
                language = $('#settings-languages .active').attr('data-lang');
            }

            var options = {
                success: function(responseText, statusText, xhr, $form)  {
                    unblockForm($('#spin-wrap'), $('.pageform'));

                    if (undefined !== responseText["errors"]) {
                        var errors = $('.error');
                        var inputs = $('.input-error');
                        var languages = $('#form-languages');
                        errors.css('opacity', 0);
                        inputs.removeClass('input-error');
                        languages.find('A').removeClass('lang-error');


                        var error = '';
                        if ( responseText["errors"][0]['id'] == '' ) {
                            error = responseText["errors"][0];
                            $('#fatal-error').text(error['text']).css('opacity', 1);
                        } else {
                            $('#' + responseText["errors"][0]['id']).focus();
                            for ( var key in responseText["errors"] ) {
                                error = responseText["errors"][key];
                                $('#' + error['id']).addClass('input-error');
                                $('#error-' + error['id']).text(error['text']).css('opacity', 1);

                                if (null != error['language']) {
                                    languages.find('[data-lang="' + error['language'] + '"] A').addClass('lang-error');
                                }
                            }
                        }
                    } else {
                        $('.pageform .success').css('opacity', 1);
                        $('.pageform .footer').css('border-top', '4px solid #0a0');

                        var timer = setTimeout(function(){
                            $('.pageform .success').css('opacity', '');
                            $('.pageform .footer').css('border-top', '');

                            clearTimeout(timer);
                        },2000);
                    }

                    return false;
                },

                beforeSubmit: function(arr, $form, options) {
                    $('.pageform .footer .success').before('<div id="spin-wrap"></div>');
                    blockForm($('#spin-wrap'), $('.pageform'));
                },

                beforeSerialize: function($form, options) {
                    $('.richedit').each(function(){
                        $(this).html( $('#ifr-'+$(this).attr('id')).contents().find('.fresheditable').html() );
                    });
                },

                data: {'language': language},
                dataType: 'json',
                url: $('#actionform').attr("action") + document.location.search.replace('?', '&')
            };

            $('#actionform').ajaxForm(options);

            $('#actionform2').submit(function(){
                closeForm();
            });
        }
    );
}

function getContentSubModule(element, type) {
    var clearUrl = element.attr("href");

    var url = getUrlNewType(clearUrl, "subpage");

    var params = getModuleParams();

    var page = element.closest('.page-content');
    var width = page.width();

    $('#module-main-area').bind('webkitTransitionEnd', function() {
        $('.page-content.old').remove();
        $(this).addClass('no-transition').css('margin-left', 0).css('width', 'auto');
        $('.page-content').css('width', '').css('margin-left', 0);
        if ( '' != $('.search').val() ) {
            $('.search').parent().find('label').css('font-size', 0);
        }
    });

    page.css('width', width);
    $('#module-main-area').css('width', width*2);

    $.get(
        url + params,
        function(data) {
            var context = null;
            if ('next' == type) {
                context = page.parent().append(data);
                $('.page-content').css('width', width);
                page.addClass("old");
                $('#module-main-area').removeClass('no-transition').css("margin-left", width*-1);
            } else {
                data = data.replace(new RegExp ('class="page-content"', 'g'),
                        'class="page-content" style="margin-left:'+ (width*-1)+'px;"');
                context = page.parent().prepend(data);
                $('.page-content').css('width', width);
                page.addClass('old');
                $('#module-main-area').removeClass('no-transition').css("margin-left", width);
            }

            setContentHeight();


            // Навешиваем на все таблицы контентной части плагин управления таблицей
            $('#main-content table').uwinTable({
                url: getUrlNewType(location.pathname, "tabledata")
            });

            // Навешиваем обработчики на действия, который отображают форму
            showForm();

            $('#main-content table').find('tbody a:not(.direct-link, .row-action)').click(function() {
                var url = $(this).attr("href");

                $('.module-tabs a').removeClass('active');
                $('.module-tabs a[href$="' + url + '"]').addClass('active');

                history.pushState(null, null, url);

                getContentSubModule($(this), 'next');

                return false;
            });

            $('.crumbs a').click(function() {
                var url = $(this).attr("href");

                $('.module-tabs a').removeClass('active');
                $('.module-tabs a[href$="' + url + '"]').addClass('active');

                history.pushState(null, null, url);

                getContentSubModule($(this), 'prev');

                return false;
            });

            if ( localStorage.getItem('uws/' + location.pathname) != undefined) {
                var tablesParam = JSON.parse(localStorage.getItem('uws/' + location.pathname) );

                if (undefined != tablesParam["language"]) {
                    context.find('#settings-languages').find('.active').removeClass('active');
                    context.find('#settings-languages').find('[data-lang=' + tablesParam["language"] + ']')
                        .addClass('active');
                } else {
                    for (var tableName in tablesParam) {
                        if ( 0 == $('#' + tableName).length) {
                            continue;
                        }

                        if (undefined != tablesParam[tableName]["language"]) {
                            context.find('#module-languages').find('.active').removeClass('active');
                            context.find('#module-languages').find('[data-lang=' + tablesParam[tableName]["language"] + ']')
                                .addClass('active');
                        }
                    }
                }
            }

            $('#module-languages A').click(function(){
                $(this).closest('UL').find('LI').removeClass('active');
                $(this).closest('LI').addClass('active');

                $('#main-content table').uwinTable('refresh');

                return false;
            });

            $('#settings-languages A').click(function(){
                $(this).closest('UL').find('LI').removeClass('active');
                $(this).closest('LI').addClass('active');

                var module_params = {};
                module_params.language = $(this).closest('LI').attr('data-lang');
                localStorage.setItem('uws/' + location.pathname,
                        JSON.stringify(module_params) );

                getContentModule(clearUrl);

                return false;
            });
        }
    );

}

/**
 * Функция входа в панель управления сайтом
 */
function loginAdmin() {
    // Получаем элементы инпутов и меток к ним
    var form   = $('#sign-in-form');
    var inputs = $('.field-wrap input');
    var labels = $('.field-wrap label');

    // Устанавливаем фокус на поле с логином
    inputs[0].focus();

    // Скрываем/отображаем плейсхолдер инпутов
    inputs.keyup(function(){
        var label = $(this).parent().find('label');

        if ( '' !== $(this).val()) {
            label.animate({"font-size": "0"}, 200);
        } else {
            label.animate({"font-size": "20"}, 200);
        }
    });

    // Если нажата кнопка входа в панель управления
    form.submit(function(){
        // Получаем элемент подсказки, кнопки и лоадера
        var tooltip = $(".tooltip");
        var button  = $('#sign-in');
        var loader  = $('.admin-login-loader');

        $('#last-admin-page').remove();
        var lastAdminPage = localStorage.getItem('last-admin-page');
        form.append('<input type="hidden" id="last-admin-page" name="last-admin-page", value="' + lastAdminPage + '" />');

        var serializeData = form.serialize();

        // Блокируем все элементы, метки, кнопку входа и отображаем лоадер
        tooltip.css('opacity', 0);
        inputs.attr('disabled', 'disabled');
        labels.attr('disabled', 'disabled');
        button.attr('disabled', 'disabled');
        loader.css('visibility', 'visible');

        // Убераем классы подсветки неверныз инпутов
        inputs.removeClass("input-error");

        // Отправка POST запроса для авторизации
        $.post($(this).attr("action"), serializeData, function(data) {
            // Парсим полученные данные в массив
            data = JSON.parse(data);
            // Если есть ошибка авторизации
            if (undefined !== data["error"]) {
                data = data["error"];

                // Записываем полученный текст ошибки в тултип
                tooltip.find('span').html(data["text"]);

                // Подсвечиваем ошибочный инпут и выставляем позиции tooltip
                var input = $('#'+data["id"]);
                input.addClass("input-error");
                tooltip.css("top", input.position().top-3);

                // Делаем активными инпуты, метки, кнопку входа, скрываем лоадер
                // и устанавливаем фокус на ошибочном инпуте
                button.removeAttr("disabled");
                inputs.removeAttr("disabled");
                labels.removeAttr("disabled");
                loader.css('visibility', 'hidden');
                input.focus();

                // Мотаем формой, что ошибка и выводим ее
                $('#lg-main')
                    .animate({"marginLeft": "+=30px"}, 100)
                    .animate({"marginLeft": "-=30px"}, 100)
                    .animate({"marginLeft": "+=30px"}, 100)
                    .animate({"marginLeft": "-=30px"}, 100)
                    .queue(function () {
                        tooltip.fadeTo("fast",1);
                        $(this).dequeue();
                    });
            } else {
                // Если ошибки авторизации нет, делаем редирект на полученный
                // адрес
                window.location.replace(data['address']);
            }
        });

        return false;
    });
}


function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars[hash[0]] = hash[1];
    }

    return vars;
}

function createPlaceholder(inputs)
{
    inputs.each(function(){
        $(this).before('<span class="placeholder">' + $(this).attr('placeholder') + '</span>');
        var ph = $(this).prev('.placeholder');
//        ph.css('height', $(this).outerHeight());
        if ( '' !== $(this).val() ) {
            ph.css('color', ph.css('background-color'));
        } else {
            ph.css('color', '');
        }
        $(this).keyup(function(e){
            if (e.keyCode != 13 && e.keyCode != 9 && e.keyCode != 16
                    && e.keyCode != 17 && e.keyCode != 18
                    && e.keyCode != 91 && e.keyCode != 37
                    && e.keyCode != 38 && e.keyCode != 39
                    && e.keyCode != 40) {
                var error = $(this).next('.error');
                error.css('opacity', 0);
                $(this).removeClass('input-error');


                $('#form-languages LI[data-lang="' + $(this).closest('DD').attr('data-lang') +'"] A')
                    .removeClass('lang-error');

            }

            if ( '' !== $(this).val() ) {
                ph.css('color', ph.css('background-color'));
            } else {
                ph.css('color', '');
            }
        });
    });
    inputs.attr('placeholder', null);

    return true;
}

function buildContentEditable() {
    // Подключаем где нужно визивиг
    $('.richedit').each(function(){
        var richedit = $(this);

        richedit.parent().append('<iframe id="ifr-'+richedit.attr('id')+'"></iframe>');
        richedit.css('visibility', 'hidden');
        $('#ifr-'+richedit.attr('id')).css({
            'height': richedit.outerHeight()-2,
            'width': richedit.outerWidth()-2,
            'top': 1,
            'left': 1
        });

        var richclassdynamic = '';
        if (richedit.attr('richclassdynamic') != undefined && richedit.attr('richclassdynamic') != '') {
            richclassdynamic = $('#'+richedit.attr('richclassdynamic')).val()
        }

        var add_file_style = '<link rel="stylesheet" href="/css/backend/contenteditable.css?' + new Date().getTime() + '">';
        if (richedit.attr('richnostyle') != undefined && richedit.attr('richnostyle') == 'true') {
            add_file_style = '';
        }

        var d = $('#ifr-'+richedit.attr('id'), top.document)[0].contentWindow.document;
        var richeditHeight = richedit.outerHeight()-52;
        d.open();
        d.write(
                '<!doctype html>'+
                '<html>'+
                '<head> <meta charset="UTF-8">'+
                '<link rel="stylesheet" href="/css/backend/fresheditor.css?' + new Date().getTime() + '">' +
                add_file_style +
                '</head>'+
                '<body id="'+richedit.attr('id')+'" style="height: ' + richeditHeight + 'px;"><div class="' + richclassdynamic + ' ' +richedit.attr('richclass') + ' fresheditable">' + richedit.text() + '</div>' +
                '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>'+
                '<script src="/js/backend/libs/shortcut.js?' + new Date().getTime() + '"></script>'+
                '<script src="/js/backend/libs/jquery.contenteditable.js?' + new Date().getTime() + '"></script>' +
                '<script>$(\'.fresheditable\').fresheditor();$(\'.fresheditable\').fresheditor("edit", true);</script>'+
                '</body>'+
                '</html>'
        );
        d.close();
    });
}

function setSizeContentEditable() {
    $('.richedit').each(function() {
        if ( 'hidden' != $(this).css('visibility') ) {
            $(this).css({
                'visibility': 'hidden',
                'position': '',
                'z-index': 0,
                'margin-top': 0,
                'height': $(this).outerHeight() + 34
            });
        }

        $('#ifr-'+$(this).attr('id')).css({
            'height': $(this).outerHeight()-2,
            'width': $(this).outerWidth()-2,
            'top': 1,
            'left': 1
        });

        $('#ifr-'+$(this).attr('id')).contents().find('BODY')
                .css('height', $(this).outerHeight()-57);
    });
}

function showForm()
{
    $('.row-action, .table-action').click(function(e){
        var element = $(this);
        var url = element.attr("href");
        var tableName = element.parent().nextAll('TABLE').attr('id');
        if (undefined == tableName){
            tableName = element.closest('TABLE').attr('id');
        }

        var language = $('#module-languages .active').attr('data-lang');
        var languageUrl = '&language=';
        if (undefined != language) {
            languageUrl = '&language=' + decodeURI(language);
        }

        url = getUrlNewType(url, "modalform") + '?tableName=' + tableName + languageUrl + '&' + window.location.search.substring(1);

        // Посылаю GET запрос, чтобы получить форму
        $.get(
            url,
            function(data) {
                $('HTML, BODY').css('overflow', 'hidden');
                $('#overlay').addClass('invisible').html(data);

                $('#overlay').removeClass('invisible transparent');
                $('#overlay .content-area').css('height', $('#overlay .content-area').height());

                $('#overlay .content-area IFRAME').each(function() {
                    var textarea = $(this).attr('id').replace('-iframe', '');
                    $(this).css('height', $('#'+textarea).outerHeight());
                    $(this).contents().find('BODY').append($('#'+textarea).text());
                });

                buildContentEditable();

                var codeeditors = [];
                var i = 0;
                $('.codepress').each(function() {
                    codeeditors[i] = CodeMirror.fromTextArea(this);
                    codeeditors[i].setOption('theme', 'default');
                    codeeditors[i].setOption('mode', 'css');
                    codeeditors[i].setOption('lineNumbers', true);
//                    codeeditors[i].setOption('lineWrapping', true);
                    codeeditors[i].refresh();
                    $(this).parent().find('.CodeMirror-scroll').css('height', $(this).outerHeight()-2);
                    i++;
                });

                // Устанавливаем все плейсхолдеры
                var inputs = $('#actionform, #actionform2').find('INPUT[placeholder], TEXTAREA[placeholder]');
                createPlaceholder(inputs);

                $(document).keydown(function(e){
                    if (e.keyCode == 27) {
                        if ( !$('#overlay').hasClass('invisible') ) {
                            closeForm();
                        }
                    }
                });

                var focus_element = $('#actionform, #actionform2').find('[autofocus]').focus();

                if (focus_element.length == 0) {
                    $('.modalform .submit').focus();

                    if ( $('.modalform .submit').length ==0 ) {
                        $('.modalform .cancel').focus();
                    }
                }

                // Организовываем работу табов
                $('.modalform .tabs A').click(function(){
                    if ( !$(this).hasClass('active') ) {
                        var id = $(this).attr('id');
                        var id_prev = $('.modalform .tabs A.active').attr('id');
                        $('#' + id_prev + '-tab').fadeOut('fast');
                        $('#' + id + '-tab').css({'position': 'absolute', 'top': 0, 'left': 0}).fadeIn('fast', function(){
                            $(this).css('position', '');
                        });

                        $('.modalform .tabs A').removeClass('active');
                        $(this).addClass('active');

                        setSizeContentEditable();

                        for (i = 0; i < codeeditors.length; i++) {
                            var textarea = $(codeeditors[i].getWrapperElement()).parent().find('TEXTAREA');
                            textarea.parent().find('.CodeMirror-scroll').css('height', textarea.outerHeight()-2);

//                            codeeditors[i].setOption('lineNumbers', false);
////                            codeeditors[i].setOption('lineNumbers', true);
                            codeeditors[i].refresh();
                        }

                    } else {
                        $('#actionform, #actionform2').find('[autofocus]').focus();
                    }

                    return false;
                });

                // Навешиваем обработчики событий на переключение чзыка в форме
                $('#form-languages A').click(function(){
                    $(this).closest('UL').find('LI').removeClass('active');
                    var lang = $(this).closest('LI').addClass('active').attr('data-lang');

                    $('.modalform DT[data-lang], .modalform DD[data-lang]').hide();
                    $('.modalform DT[data-lang="' + lang + '"], .modalform DD[data-lang="' + lang + '"]').show();

                    setSizeContentEditable();

                    return false;
                });

                var options = {
                    success: function(responseText, statusText, xhr, $form)  {
                        unblockForm($('#spin-wrap'), $('.modalform'));

                        // Если есть ошибка авторизации
                        if (undefined !== responseText["errors"]) {
                            var errors = $('.error');
                            var inputs = $('.input-error');
                            var languages = $('#form-languages');
                            errors.css('opacity', 0);
                            inputs.removeClass('input-error');
                            languages.find('A').removeClass('lang-error');

                            var error = '';
                            if ( responseText["errors"][0]['id'] == '' ) {
                                error = responseText["errors"][0];
                                $('#fatal-error').text(error['text']).css('opacity', 1);
                            } else {
                                $('#' + responseText["errors"][0]['id']).focus();
                                for ( var key in responseText["errors"] ) {
                                    error = responseText["errors"][key];
                                    $('#' + error['id']).addClass('input-error');
                                    $('#error-' + error['id']).text(error['text']).css('opacity', 1);

                                    if (null != error['language']) {
                                        languages.find('[data-lang="' + error['language'] + '"] A').addClass('lang-error');
                                    }
                                }
                            }
                        } else {
                        // Обновляем таблицу и закрываем форму
                            $('#main-content table').uwinTable('refresh');
                            closeForm();
                        }

                        return false;
                    },

                    beforeSubmit: function(arr, $form, options) {
                        $('.modalform .footer').prepend('<div id="spin-wrap"></div>');
                        blockForm($('#spin-wrap'), $('.modalform'));
                    },

                    beforeSerialize: function($form, options) {
                        $('.richedit').each(function(){
                            if ( 'visible' != $(this).css('visibility') ) {
                                $(this).val( $('#ifr-'+$(this).attr('id')).contents().find('.fresheditable').html() );
                            }
                        });
                    },

                    dataType: 'json',
                    url: $('#actionform').attr("action") + document.location.search.replace('?', '&')
                };

                $('#actionform').ajaxForm(options);

                $('.modalform .cancel').click(function(){
                    closeForm();
                });

                $('#actionform2').submit(function(){
                    closeForm();
                });
            }
        );

        return false;
    });

    function closeForm() {
        $('#overlay').addClass('transparent invisible').html('');
        $('HTML, BODY').css('overflow', '');
        $('#actionform, #actionform2').css('position', '');
        $('.modalform').css('position', '');
        $('#overlay').css('position', '');
    }
}
