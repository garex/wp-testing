jQuery(document).ready(function($) {
    var AVERAGE_LETTERS_IN_WORD     = 9.5,
        AVERAGE_WORDS_IN_SENTENSE   = 17,
        MINIMUM_WIDTH               = 10,
        MAXIMUM_WIDTH               = 32;

    var box     = $('#wpt_edit_questions'),
        titles  = box.find('.wpt_answer input[type=text]');

    titles.keydown(function() {
        var length = $(this).val().length;
        if (length < MINIMUM_WIDTH) {
            length = MINIMUM_WIDTH;
        } else if (length > MAXIMUM_WIDTH) {
            length = MAXIMUM_WIDTH;
        }
        $(this).css('min-width', length + 'em');
    }).keydown();
});
