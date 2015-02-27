jQuery(document).ready(function($) {
    $('.wpt_text_with_more').each(function() {
        var content = $(this).find('.text_under_more');
        $(this).find('.more_link').click(function() {
            $(this).toggleClass('open');
            content.toggle();
            return false;
        });
    });
});