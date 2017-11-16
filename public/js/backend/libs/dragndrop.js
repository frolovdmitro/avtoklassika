(function($) {
    $.fn.dragndrop = function(method) {
        // Храним элемент который начали перемещать
        var dragSrcElement = null;
        var callbackFunction = null;

        var methods = {
            init : function(options, callbackFn) {
                this.dragndrop.settings = $.extend({}, this.dragndrop.defaults, options);
                callbackFunction = callbackFn;

                $(this).closest('TABLE')
                    .find('TD').not('.draggable-cell')
                    .append('<div class="draggable-invisible-area"></div>')
                    .find('.draggable-invisible-area')
                    .attr('draggable', true)
                    .bind('dragstart', helpers.handleDragStart)
                    .bind('dragenter', helpers.handleDragEnter)
                    .bind('dragover', helpers.handleDragOver)
                    .bind('dragleave', helpers.handleDragLeave)
                    .bind('drop', helpers.handleDrop)
                    .bind('dragend', helpers.handleDragEnd);

                return this.each(function() {
                    $(this).attr('draggable', 'true')
                           .bind('dragstart', helpers.handleDragStart)
                           .bind('dragenter', helpers.handleDragEnter)
                           .bind('dragover', helpers.handleDragOver)
                           .bind('dragleave', helpers.handleDragLeave)
                           .bind('drop', helpers.handleDrop)
                           .bind('dragend', helpers.handleDragEnd);
                });
            }
        };

        var helpers = {
            getDragElement: function(e) {
                if (null == e.dragndrop.settings.parentDragElement) {
                    return e;
                }

                return e.closest($(this).dragndrop.settings.parentDragElement);
            },

            handleDragStart: function(e) {
                var parent = helpers.getDragElement($(this));

                parent.closest('TABLE').find('.draggable-invisible-area')
                    .css('display', 'block');

                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setDragImage(parent.get(0),-5,-5);
                e.originalEvent.dataTransfer.setData('text/plain', parent.html());

                dragSrcElement = this;

                parent.addClass($(this).dragndrop.settings.movingClassName);
            },

            handleDragOver: function(e) {
                if (e.preventDefault) {
                    e.preventDefault(); // Allows us to drop.
                }

                e.originalEvent.dataTransfer.dropEffect = 'move';

                return false;
            },

            handleDragEnter: function(e) {
                var parent = helpers.getDragElement($(this));
                parent.addClass($(this).dragndrop.settings.overClassName);
            },

            handleDragLeave: function(e) {
                var parent = helpers.getDragElement($(this));
                parent.removeClass($(this).dragndrop.settings.overClassName);
            },

            handleDrop: function(e) {
                if (e.stopPropagation) {
                    e.stopPropagation(); // stops the browser from redirecting.
                }

                var overClassName = $(this).dragndrop.settings.overClassName;
                var movingClassName = $(this).dragndrop.settings.movingClassName;
                var parent = helpers.getDragElement($(this));
                var parentSrc = helpers.getDragElement($(dragSrcElement));

                parent.removeClass(overClassName)
                       .removeClass(movingClassName);
                parentSrc.removeClass(overClassName)
                       .removeClass(movingClassName);

                if (dragSrcElement != this) {
                    parentSrc.html( parent.html() )
                        .find('[draggable]')
                        .bind('dragstart', helpers.handleDragStart)
                        .bind('dragenter', helpers.handleDragEnter)
                        .bind('dragover', helpers.handleDragOver)
                        .bind('dragleave', helpers.handleDragLeave)
                        .bind('drop', helpers.handleDrop)
                        .bind('dragend', helpers.handleDragEnd);

                    parent.html( e.originalEvent.dataTransfer.getData('text/plain') )
                        .find('[draggable]')
                        .bind('dragstart', helpers.handleDragStart)
                        .bind('dragenter', helpers.handleDragEnter)
                        .bind('dragover', helpers.handleDragOver)
                        .bind('dragleave', helpers.handleDragLeave)
                        .bind('drop', helpers.handleDrop)
                        .bind('dragend', helpers.handleDragEnd);

                    if(typeof callbackFunction == 'function'){
                        $(this).dragndrop.settings.sourceId = parentSrc
                            .closest( $(this).dragndrop.settings.parentDragElement )
                            .attr('data-id');
                        $(this).dragndrop.settings.movedId = parent
                            .closest( $(this).dragndrop.settings.parentDragElement )
                            .attr('data-id');

                        callbackFunction.call($(this).dragndrop.settings);
                    }
                }

                parent.closest('TABLE').find('.draggable-invisible-area')
                    .css('display', 'none');

                return false;
            },

            handleDragEnd: function(e) {
                var overClassName = $(this).dragndrop.settings.overClassName;
                var movingClassName = $(this).dragndrop.settings.movingClassName;
                var parent = helpers.getDragElement($(this));
                var parentSrc = helpers.getDragElement($(dragSrcElement));

                parent.removeClass(overClassName)
                       .removeClass(movingClassName);
                parentSrc.removeClass(overClassName)
                       .removeClass(movingClassName);

                parent.closest('TABLE').find('.draggable-invisible-area')
                    .css('display', 'none');
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

    $.fn.dragndrop.defaults = {
        movingClassName:  'moving',
        overClassName:    'over',
        parentDragElement: null
    };

    $.fn.dragndrop.settings = {}
})(jQuery);