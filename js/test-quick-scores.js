jQuery(document).ready(function($) {
    var box         = $('#wpt_quick_fill_scores'),
        sourceRows  = box.find('table tr.quick-score'),
        targetTable = $('#wpt_edit_questions table.wpt_questions'),
        toggle      = box.find('.toggle'),
        panel       = box.find('.wp-hidden-child'),
        ok          = box.find('.button');

    toggle.click(function() {
        fillUpSourceTableFromTarget();
        panel.toggle();
        return false;
    });

    ok.click(function() {
        sourceRows.each(function() {
            var quickClass = $(this).data('quickScoreClass'),
                score      = $(this).find('.score input').val(),
                questions  = $(this).find('.questions input').val().split(/[^\d]+/),
                target     = targetTable.find('.' + quickClass);

            target.val('');
            $(questions).each(function(i, questionNumber) {
                if ('' == questionNumber) {
                    return;
                }
                target.filter('.question-' + questionNumber).val(score);
            });
        });

        panel.toggle();
    });

    function fillUpSourceTableFromTarget() {
        sourceRows.each(function() {
            var quickClass = $(this).data('quickScoreClass'),
                target     = targetTable.find('.' + quickClass),
                questions  = [];
                scores     = [];

            target.each(function() {
                var value = $(this).val();
                if ('' == value) {
                    return;
                }
                scores.push(value);
                questions.push($(this).data('questionNumber'));
            });
            $(this).find('.questions input').val(questions.join(', '));

            scores = _.uniq(scores);
            var score = 1;
            if (scores.length == 1) {
                score = scores[0];
            } else if (scores.length > 1) {
                score = '';
            }
            $(this).find('.score input').val(score);
        });
    };
});
