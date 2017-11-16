/* jQuery.contentEditable Plugin
Copyright © 2011 FreshCode
http://www.freshcode.co.za/

DHTML text editor jQuery plugin that uses contentEditable attribute in modern browsers for in-place editing.

Dependencies
------------
 - jQuery core
 - shortcut.js for keyboard hotkeys
 
Issues
------
 - no image support
 - no <code> or <blockquote> buttons (use Tab key for quotes)
 - no text alignment support

License
-------
Let's keep it simple:
 1. You may use this code however you wish, including for commercial projects.
 2. You may not sell it or charge for it without my written permission.
 3. You muse retain the license information in this file.
 4. You are encouraged to contribute to the plugin on bitbucket (https://bitbucket.org/freshcode/jquery.contenteditable)
 5. You are encouraged to link back to www.freshcode.co.za if you publish something about it so everyone can benefit from future updates.

Best regards
Petrus Theron
contenteditable@freshcode.co.za
FreshCode Software Development
 
*/
(function ($) {
	var inited = false,
		// helper function that builds the toolbar
		toolbar = function(options) {
			var bar = $('<div />').addClass('fresheditor-toolbar clearfix').css('display', 'block');
			bar.append($('<div />').addClass('buttons'));
			$.each(options.enabledCommands, function(groupName, group)
			{
				var groupEl = $('<ul />').addClass('toolbarSection').addClass(groupName);
				$.each(group, function(v, command) {
					groupEl.append(
						$('<li />').append(
							$('<a />')
								.addClass('toolbar_' + command)
								.attr({ title: options.i18n[command] + " (" + options.commands[command].shortcut + ")", href: "#" })
								.html(options.commands[command].toolbarHtml ? options.commands[command].toolbarHtml : "&nbsp;")
						)
					);
				});
				bar.find('.buttons').append(groupEl);
			});
			return bar;
		};
	var methods = {
		edit: function (isEditing) {
			var focus = function() {
				var t = $(this);
				t.data('before', t.html());
				return t;
			}, blur = function() {
				var t = $(this);
				if (t.data('before') != t.html())
				{
					t.data('before', t.html());
					t.trigger('change');
				}
			};
			if (isEditing === true)
			{
				$(this).bind('focus', focus);
				$(this).bind('blur keyup paste', blur);
			}
			else
			{
				$(this).unbind('focus', focus);
				$(this).unbind('blur keyup paste', blur);
			}

			return this.each(function () {
				$(this).attr("contentEditable", (isEditing === true) ? true : false);
			});
		},
		copy: function () {
			document.execCommand("Copy", false, null);
		},
		paste: function () {
			document.execCommand("Paste", false, null);
		},
		save: function (callback) {
			return this.each(function () {
				(callback)($(this).attr("id"), $(this).html());
			});
		},
		init: function (options) {
			options = $.extend({}, $.fn.fresheditor.defaults, options);
			if (typeof options === 'object' && typeof options.onchange == 'function')
			{
				$(this).bind('change', options.onchange);
			}
			var $toolbar = toolbar(options),
			on_scroll = function () {
				var docTop = $(window).scrollTop();

				var toolbarTop = $toolbar.offset().top;
				if (docTop > toolbarTop) {
					$("div.buttons", $toolbar).css({ "position": "fixed", "top": "0" });
				} else {
					$("div.buttons", $toolbar).css("position", "relative");
				}
			}, toolbar_reset = function() {
				$(window).unbind('scroll', on_scroll);
				$("div.buttons", $toolbar).css("position", "relative");
			}, toolbar_scroll = function() {
				$(window).bind('scroll', on_scroll);
				on_scroll();
			};

			$(this).first().before($toolbar);

			/* Bind Toolbar Clicks */

			var that = this;
			$.each(options.commands, function(command, opts) {
				// use self-invoking function to keep command and opts in scope after loop ends
				methods[command] = (function(command, opts) { 
					return function() {
                        function buildSrcEdit(iframe) {
                            var height = iframe.outerHeight();
                            if (teaxtarea.css('visibility') == 'hidden') {
                                teaxtarea.val( $('.fresheditable').html() );

                                teaxtarea.css({
                                    'visibility': 'visible',
                                    'z-index': 120,
                                    'margin-top': 34,
                                    'background': '#fff',
                                    'top': '',
                                    'left': '',
                                    'bottom': '',
                                    'right': '',
                                    'min-height': '',
                                    'min-width': ''
                                });

                                if ( 'fixed' != iframe.css('position') ) {
                                    teaxtarea.css({
                                        'position': 'relative',
                                        'height': height -34
                                    });
                                } else {
                                    teaxtarea.css({
                                        'position': 'fixed',
                                        'top': 0,
                                        'left': 0,
                                        'bottom': 0,
                                        'right': 0,
                                        'min-width': '100%',
                                        'min-height': '100%'
                                    });
                                }
                            } else {
                                $('.fresheditable').html( teaxtarea.val() );
                                teaxtarea.css({
                                    'visibility': 'hidden',
                                    'position': '',
                                    'z-index': 0,
                                    'margin-top': 0,
                                    'height': height
                                });
                            }
                        }

                        var id = $('BODY').attr('id');
                        var teaxtarea = parent.$(parent.document).find('#' + id);
                        var iframe = parent.$(parent.document).find('#ifr-' + id);
                        var height = null;
						var $toolbar = $(this).data('fresheditor').toolbar;
						// prevent triggering items that are not enabled
						if ($toolbar.find('a.toolbar_' + command).size() == 0) { 
							return false; 
						}

                        if (opts.execCommand == 'fullscreen') {
                            if ( 'fixed' != iframe.css('position') ) {
                                parent.$(parent.document).find('.richedit').css({
                                    'left': -9999
                                });

                                iframe.css({
                                    'position': 'fixed',
                                    'top': 0,
                                    'left': 0,
                                    'bottom': 0,
                                    'right': 0,
                                    'z-index': 100,
                                    'min-height': '100%',
                                    'min-width': '100%'
                                });

                                iframe.closest('DD').css('position', 'static');
                                iframe.closest('#actionform').css('position', 'static');
                                iframe.closest('.modalform').css('position', 'static');
                                iframe.closest('#overlay').css('position', 'static');

                                teaxtarea.css({
                                    'position': 'fixed',
                                    'top': 0,
                                    'left': 0,
                                    'bottom': 0,
                                    'right': 0,
                                    'min-width': '100%',
                                    'min-height': '100%'
                                });

//                                height = iframe.outerHeight();

//                                teaxtarea.css({
//                                    'height': height-34
//                                });
                            } else {
                                parent.$(parent.document).find('.richedit').css({
                                    'left': ''
                                });

                                iframe.css({
                                    'position': '',
                                    'top': '1px',
                                    'left': '1px',
                                    'bottom': '',
                                    'right': '',
                                    'z-index': '',
                                    'min-height': '',
                                    'min-width': ''
                                });

                                iframe.closest('DD').css('position', '');
                                iframe.closest('#actionform').css('position', '');
                                iframe.closest('.modalform').css('position', '');
                                iframe.closest('#overlay').css('position', '');

                                if ('hidden' != teaxtarea.css('visibility')) {
                                    height = iframe.outerHeight()-34;
                                } else {
                                    height = iframe.outerHeight();
                                }
                                teaxtarea.css({
                                    'height': height,
                                    'position': '',
                                    'top': '',
                                    'left': '',
                                    'bottom': '',
                                    'right': '',
                                    'min-height': '',
                                    'min-width': ''
                                });
                            }
                            return false;
                        }
                        
                        if (opts.execCommand == 'typograph') {
                            $('HTML').css('background', '#eee');
                            $('.fresheditable').css('background', '#eee')
                                    .attr('contenteditable', false);
                            $('.fresheditor-toolbar .toolbarSection').css('opacity', '0.5')
                                    .find('A').css('pointer-events', 'none');

                            if ('hidden' != teaxtarea.css('visibility')) {
                                $('.fresheditable').html( teaxtarea.val() );
                            } else {
                                teaxtarea.val( $('.fresheditable').html() );
                            }

                            $.ajax({
                                type: "POST",
                                url: '/administrator/typografy/',
                                data: 'text=' + encodeURIComponent( $('.fresheditable').html() ),
                                dataType: 'json',
                                success: function(response) {
                                    teaxtarea.val(response['result']);
                                    $('.fresheditable').html( response['result'] );

                                    $('HTML').css('background', '');
                                    $('.fresheditable').css('background', '')
                                            .attr('contenteditable', true);
                                    $('.fresheditor-toolbar .toolbarSection').css('opacity', '')
                                            .find('A').css('pointer-events', '');
                                }
                            });
                        }

                        if (opts.execCommand == 'html') {
                            buildSrcEdit(iframe);

                            return false;
                        }

						// we allow multiple commands to be executed as one action
						// typically, this only happens with removeFormat + unlink
						if (typeof opts.execCommand == 'string') {
							opts.execCommand = [opts.execCommand];
							opts.execCommandValue = [opts.execCommandValue]
						}

						// since execCommand is now an array, loop
						$.each(opts.execCommand, function(i, execCommand) {
							var execCommandValue = typeof opts.execCommandValue == 'object' ? opts.execCommandValue[i] : undefined;
							// if the command wants to provide a value to the execCommand, allow
							// it to using a callback
							if (typeof execCommandValue === "function") {
								execCommandValue(function(value) {
									// allow command to be aborted by returning false
									if (value === false) {
										return false; 
									}
								
									document.execCommand(execCommand, false, value);
								});
							}
							else
							{
								document.execCommand(execCommand, false, execCommandValue);
							}
						});
						return false;
					}
				})(command, opts);

				// put contenteditable as this when commands are run, so commands can
				// examine the plugin if it wants to
				var scopedThis = function() { return methods[command].apply(that); };

				$toolbar.find('a.toolbar_' + command).click(scopedThis);

				if (!inited)
				{
					if (typeof opts.shortcut === 'string')
					{
						opts.shortcut = [opts.shortcut];
					}
					$.each(opts.shortcut || [], function(i, key) {
						shortcut.add(key, scopedThis, { 'type': 'keydown', 'propagate': false });
					});
				}
			});

			inited = true;

			return this.each(function () {

				var $this = $(this), data = $this.data('fresheditor'),
					tooltip = $('<div />', {
						text: $this.attr('title')
					});
				$this.blur(toolbar_reset);
				$this.focus(toolbar_scroll);

				// If the plugin hasn't been initialized yet
				if (!data) {
					/* Do more setup stuff here */

					$(this).data('fresheditor', {
						target: $this,
						tooltip: tooltip,
						options: options,
						toolbar: $toolbar
					});
				}
			});
		}
	};

	$.fn.fresheditor = function (method) {
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.contentEditable');
		}

		return this;
	};

	$.fn.fresheditor.defaults = {
		/*
		 * Change this option to remove some options. Note that the shortcut keys
		 * will still be registered, but disabled for the options not present.
		 * The sub-arrays are groups of buttons, useful for styling.
		 */
		enabledCommands: {
			// fullscren
			fullscren: ["fullscreen", "html"],
			// history
			history: ["undo", "redo"],
			// clear
			clear: ["removeFormat"],
			// character formatting
            character: ["bold", "italic", "underline", "strike"],
			// align
			align: ["justifyLeft", "justifyCenter", "justifyRight", "justify"],
			// external stuff
			external: ["createLink", "insertImage"],
			// multiple-paragraph formatting
			multipleParagrah: ["ol", "ul", "blockquote"],
			// headers and paragraphs
			paragraph: ["p", "h1", "h2", "h3"],
            // typograph
            typograph: ["typograph"]
		},
		/*
		 * Strings for translation
		 */
		i18n: {
            fullscren: 'Полноекранный режим',
            html: 'HTML код',
			bold: "Полужирный",
			italic: "Курсив",
			underline: "Подчеркнутый",
            strike: "Зачеркнутый",
			undo: "Отменить",
            redo: "Вернуть",
			removeFormat: "Очистить форматирование",
			createLink: "Вставить ссылку",
			insertImage: "Вставить изображение",
			blockquote: "Цитата",
			code: "Код",
			ol: "Нумерованый список",
			ul: "Не нумерованный список",
			sup: "Надстрочный",
			sub: "Подстрочный",
			p: "Параграф",
			h1: "Заголовок 1",
			h2: "Заголовок 2",
			h3: "Заголовок 3",
			h4: "Заголовок 4",
			h5: "Заголовок 5",
			h6: "Заголовок 6",
			indent: "Indent",
			outdent: "Outdent"
		},
		/*
		 * Actual commands to run. You probably shouldn't override this completely,
		 * but you can safely change the shortcut and toolbarHtml values.
		 * 
		 * shortcut: the shortcut combination (can also be an array of multiple shortcuts)
		 * execCommand: the command to send to document.execCommand
		 * execCommandValue: optional value to send to document.execCommand, but can
		 * 	also be a function that calls the provided callback when the value is
		 * 	ready.
		 * toolbarHtml: HTML to put inside the buttons (very short)
		 */
		commands: {
            bold: { shortcut: "Ctrl+b", execCommand: "bold", toolbarHtml: "B" },
			italic: { shortcut: "Ctrl+i", execCommand: "italic", toolbarHtml: "I" },
			underline: { shortcut: "Ctrl+u", execCommand: "underline", toolbarHtml: "U" },
            strike: { shortcut: "Ctrl+u", execCommand: "strikeThrough", toolbarHtml: "U" },
			removeFormat: { shortcut: "Ctrl+m", execCommand: ["removeFormat", "unlink", "formatBlock"],
				execCommandValue: [null, null, ["<P>"]],
				toolbarHtml: "&minus;" },
			createLink: { 
				shortcut: "Ctrl+l", 
				execCommand: "createLink",
				execCommandValue: function(callback) {
					callback(prompt("Enter URL:", "http://"));
				},
				toolbarHtml: "@"
			},
			insertImage: {
				shortcut: "Ctrl+g",
				execCommand: "insertImage",
				execCommandValue: function(callback) {
					callback(prompt("Enter image URL:", "http://"));
				}
			},
			blockquote: { shortcut: "Ctrl+q", execCommand: "formatBlock", execCommandValue: ["<BLOCKQUOTE>"], toolbarHtml: "&ldquo;&bdquo;" },
			code: { shortcut: "Ctrl+Alt+k", execCommand: "formatBlock", execCommandValue: ["<PRE>"], toolbarHtml: "{&nbsp;}" },
			ol: { shortcut: "Ctrl+Alt+o", execCommand: "insertOrderedList" },
			ul: { shortcut: "Ctrl+Alt+u", execCommand: "insertUnorderedList" },
			sup: { shortcut: "Ctrl+.", execCommand: "superscript", toolbarHtml: "x<sup>2</sup>" },
			sub: { shortcut: "Ctrl+Shift+.", execCommand: "subscript", toolbarHtml: "x<sub>2</sub>" },
			p: { shortcut: "Ctrl+Alt+0", execCommand: "formatBlock", execCommandValue: ["<P>"], toolbarHtml: "P" },
			h1: { shortcut: "Ctrl+Alt+1", execCommand: "formatBlock", execCommandValue: ["<H1>"], toolbarHtml: "H<sub>1</sub>" },
			h2: { shortcut: "Ctrl+Alt+2", execCommand: "formatBlock", execCommandValue: ["<H2>"], toolbarHtml: "H<sub>2</sub>" },
			h3: { shortcut: "Ctrl+Alt+3", execCommand: "formatBlock", execCommandValue: ["<H3>"], toolbarHtml: "H<sub>3</sub>" },
			h4: { shortcut: "Ctrl+Alt+4", execCommand: "formatBlock", execCommandValue: ["<H4>"], toolbarHtml: "H<sub>4</sub>" },
			h5: { shortcut: "Ctrl+Alt+5", execCommand: "formatBlock", execCommandValue: ["<H5>"], toolbarHtml: "H<sub>5</sub>" },
			h6: { shortcut: "Ctrl+Alt+6", execCommand: "formatBlock", execCommandValue: ["<H6>"], toolbarHtml: "H<sub>6</sub>" },
            fullscreen: { shortcut: "Ctrl+Alt+f", execCommand: "fullscreen", toolbarHtml: "&rArr;" },
            html: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "html", toolbarHtml: "HTML" },
			indent: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "indent", toolbarHtml: "&rArr;" },
			justifyLeft: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "justifyLeft", toolbarHtml: "&lArr;" },
			justifyCenter: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "justifyCenter", toolbarHtml: "&rArr;" },
            justifyRight: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "justifyRight", toolbarHtml: "&rArr;" },
            justify: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "justify", toolbarHtml: "&rArr;" },
            undo: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "undo", toolbarHtml: "&lArr;" },
            redo: { shortcut: ["Tab", "Ctrl+Tab"], execCommand: "redo", toolbarHtml: "&rArr;" },
			outdent: { shortcut: "Shift+Tab", execCommand: "outdent", toolbarHtml: "&lArr;" },
            typograph: { shortcut: "Ctrl+t", execCommand: "typograph", toolbarHtml: "&lArr;" }
		},
		brOnReturn: false
	};
})(jQuery);