jQuery(document).ready(function($) {
    var box     = $('#wpt_quick_fill_questions'),
        toggle  = box.find('.toggle'),
        panel   = box.find('.wp-hidden-child'),
        ok      = box.find('.button'),
        text    = box.find('textarea'),
        table   = $('#wpt_add_questions table.wpt_questions');

    toggle.click(function() {
        panel.toggle();
        return false;
    });

    ok.click(function() {
        var source    = text.val(),
            questions = parseQuestions(source);

        fillQuestions(questions);

        text.val('');
        panel.toggle();
    });

    function parseQuestions(source) {
        var result = [];

        source = $.trim(source);
        if (source == '') {
            return result;
        }

        result = source.split(/[\r\n]+/);

        jQuery.each(result, function(i, row) {
            result[i] = $.trim(row.replace(/^\w{1,3}[^\w\s]\s+/, ''));
        });

        return result;
    };

    function fillQuestions(newQuestions) {
        if (newQuestions.length == 0) {
            return;
        }

        // Merge existing questions with new
        var questions = [];
        table.find('input:text').each(function() {
            if ($(this).val() != '') {
                questions.push($(this).val());
            }
        });
        $(newQuestions).each(function(i, newQuestion) {
            questions.push(newQuestion);
        });

        // Redraw table
        var startFrom = table.data('startFrom'),
            firstRow  = table.find('tr:first').removeClass('bar').remove();

        table.find('tr').remove();

        $(questions).each(function(i, question) {
            var inputNameKey = 'wpt_question_title[' + JSON.stringify({"q": startFrom + i, "id": ''}) + ']';
            firstRow
                .clone()
                .addClass(i % 2 ? 'alternate' : 'bar')
                .appendTo(table)
                .find('input')
                    .val(question)
                    .attr('name', inputNameKey)
                    .attr('id',   'wpt_question_title_' + (startFrom + i));
        });

    };

});
