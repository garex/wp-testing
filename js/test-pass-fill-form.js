jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');

    var button = form.find('.button');
    button.addClass('disabled').attr('disabled', 'disabled');
    form.on('test_filled.wpt', function() {
        button.removeAttr('disabled').removeClass('disabled');
    });

    form.on('question_answered.wpt', function(event, question) {
        question.addClass('answered');
    });

    var ec = new evercookie({
        tests           : 3,
        baseurl         : wpt_evercookie.baseurl,
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
    if (!form.data('wpt').isResetAnswersOnBack) {
        return;
    }
    form.on('init_answers.wpt', function(event, answersInputs) {
        answersInputs.attr('checked', false);
    });
});

jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');
    if (!form.data('wpt').isShowProgressMeter) {
        return;
    }

    var initialTitle = document.title,
        separator    = form.data('wpt').titleSeparator,
        template     = form.data('wpt').percentsAnswered;

    $(document).on('percentage_change.wpt', function(event, percent) {
        document.title = template.replace('{percentage}', percent) +  ' ' + separator + ' ' + initialTitle;
    });
});

jQuery(document).ready(function ($) {
    var form               = $('#wpt-test-form'),
        questionsAnswered  = 0,
        questions          = form.find('.question'),
        questionsTotal     = questions.length;

    var answersInputs = form.find('input:radio');
    form.trigger('init_answers.wpt', [answersInputs]);

    form.find('.question').each(function () {
        var question = $(this);
        question.data('isAnswered', false);
        question.find('.answer').each(function () {
            var answer = $(this);
            answer.find('input').on('change', function () {
                if (!$(this).attr('checked') || question.data('isAnswered')) {
                    return;
                }
                question.data('isAnswered', true);
                questionsAnswered++;
                form.trigger('question_answered.wpt', [question, questionsAnswered, questionsTotal]);
            });
        });
    });

    form.on('question_answered.wpt', function(event, question, answered, total) {
        var percent = Math.round(100 * (answered / total));
        $(document).trigger('percentage_change.wpt', [percent]);
        if (answered == total) {
            form.trigger('test_filled.wpt');
        }
    });

    answersInputs.filter(':checked').change();
});
