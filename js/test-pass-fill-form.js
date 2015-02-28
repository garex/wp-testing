jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');

    var button = form.find('.button');

    form.bind('test_filled.wpt', function() {
        button.removeAttr('disabled').removeClass('disabled');
    }).bind('test_unfilled.wpt', function() {
        button.addClass('disabled').attr('disabled', 'disabled');
    });

    form.bind('question_answered_initially.wpt', function(event, question) {
        question.addClass('answered');
    }).bind('question_unanswered_initially.wpt', function(event, question) {
        question.removeClass('answered');
    });

    var ec = new evercookie({
        tests           : 3,
        baseurl         : Wpt.evercookieBaseurl,
        history         : false,
        silverlight     : false,
        java            : false,
        pngCookieName   : 'wpt_ec_png_device_uuid',
        etagCookieName  : 'wpt_ec_etag_device_uuid',
        cacheCookieName : 'wpt_ec_cache_device_uuid'
    });
    ec.get('device_uuid', function(best) {
        var uuid = UUIDjs.fromURN(best) || UUIDjs.create(4);
        ec.set('device_uuid', uuid.toString());
    }, 1);
});

jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');
    if (!Wpt.isResetAnswersOnBack) {
        return;
    }
    form.bind('init_answers.wpt', function(event, answersInputs) {
        answersInputs.attr('checked', false);
    });
});

jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');
    if (!Wpt.isShowProgressMeter) {
        return;
    }

    var initialTitle = document.title,
        separator    = Wpt.titleSeparator,
        template     = Wpt.percentsAnswered;

    $(document).bind('percentage_change.wpt', function(event, percent) {
        document.title = template.replace('{percentage}', percent) +  ' ' + separator + ' ' + initialTitle;
    });
});

jQuery(document).ready(function ($) {
    var form               = $('#wpt-test-form'),
        questionsAnswered  = 0,
        questions          = form.find('.question'),
        questionsTotal     = questions.length;

    var answersInputs = form.find('input:radio,input:checkbox');
    form.trigger('init_answers.wpt', [answersInputs])
        .trigger('test_unfilled.wpt');

    form.find('.question').each(function () {
        var question = $(this),
            title    = question.find('.title .title');

        title.html(title.html().replace(/(_{2,})/, '<span class="placeholder">$1</span>'));
        var placeholder = title.find('.placeholder');

        question.data('isAnswered', false);
        var questionAnswersInputs = question.find('.answer input');
        question.find('.answer').each(function () {
            var answer = $(this);
            answer.find('input').bind('change', function () {
                if (!$(this).attr('checked')) {
                    var isAllCheckboxesEmpty = (0 == questionAnswersInputs.filter(':checked').length);
                    if (isAllCheckboxesEmpty) {
                        question.data('isAnswered', false);
                        questionsAnswered--;
                        form.trigger('question_unanswered_initially.wpt', [question, questionsAnswered, questionsTotal]);
                        form.trigger('question_unanswered.wpt', [question, answer, placeholder]);
                    }
                    return;
                }
                if (!question.data('isAnswered')) {
                    question.data('isAnswered', true);
                    questionsAnswered++;
                    form.trigger('question_answered_initially.wpt', [question, questionsAnswered, questionsTotal]);
                }
                form.trigger('question_answered.wpt', [question, answer, placeholder]);
            });
        });
    });

    function calculateAnswersPercentage(event, question, answered, total) {
        var percent = Math.round(100 * (answered / total));
        $(document).trigger('percentage_change.wpt', [percent]);
        if (answered == total) {
            form.trigger('test_filled.wpt');
        } else {
            form.trigger('test_unfilled.wpt');
        }
    };
    form.bind('question_answered_initially.wpt',   calculateAnswersPercentage)
        .bind('question_unanswered_initially.wpt', calculateAnswersPercentage);

    answersInputs.filter(':checked').change();
});
