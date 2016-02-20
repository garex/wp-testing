(function ($) {

    function maximizeItem(maximizable, settings) {
        var container    = $('body:first'),
            scrollable   = maximizable.find('.inside:first'),
            toggleButton = $('<button/>'),
            buttonText   = $('<span/>').addClass('screen-reader-text').prependTo(toggleButton),
            isMaximized  = false;

        function changeLabel() {
            var label = isMaximized ? settings.minimizeLabel : settings.maximizeLabel;
            toggleButton.attr('title', label);
            buttonText.text(label);
        };

        function toggleMaximize() {
            isMaximized = !isMaximized;

            changeLabel();

            container.toggleClass('wpt_maximize', isMaximized);
            maximizable.toggleClass('wpt_maximized', isMaximized);
            toggleButton.toggleClass('active', isMaximized);

            return false;
        };
        changeLabel();

        maximizable.addClass('wpt_maximizable');
        scrollable.addClass('wpt_scroll');

        toggleButton
            .bind('click', toggleMaximize)
            .attr('type', 'button').addClass('handlediv button-link qt-dfw')
            .insertBefore(maximizable.find('.handlediv:first'))
        ;

        /* Old WP versions */
        if (typeof tinyMCE !== 'undefined') {
            toggleButton.addClass('wp_themeSkin')
            $('<span/>').addClass('mceIcon mce_fullscreen').prependTo(toggleButton)
        }
    };

    $.fn.wptMaximize = function(options) {
        var settings = $.extend({
            maximizeLabel: 'Maximize',
            minimizeLabel: 'Minimize'
        }, options);

        return this.each(function(i, item) {
            maximizeItem($(item), settings);
        });
    };

}(jQuery));
