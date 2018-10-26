var Wpt = Wpt || {};
Wpt.form = Wpt.form || {};

Wpt.initWebshim = function(baseUrl) {
    if (this.initialized || typeof webshim === 'undefined') {
        return;
    }
    this.initialized = true;
    webshim.setOptions({
        waitReady : true,
        basePath  : baseUrl,
        forms     : {
            replaceValidationUI: true,
            messagePopover: {
                position: {
                    at: 'top',
                    my: 'bottom',
                    collision: 'flipfit'
                }
            }
        }
    });
    webshims.polyfill('forms forms-ext');
};

if (Wpt.webshimBaseurl) {
    Wpt.initWebshim(Wpt.webshimBaseurl);
}

jQuery(document).ready(function($) {
    Wpt.initWebshim(Wpt.webshimBaseurl);
    Wpt.initEvercookie();

    $('.wpt_test_form').each(function(i, formEl) {
        var form = $(formEl);

        Wpt.form.initQuestionAnswered(form);
        Wpt.form.setupSubmitDisable(form);
        Wpt.form.setupResetAnswers(form);
        Wpt.form.setupProgressMeter($, form);
        Wpt.form.setupQuestionsAnswered($, form);
    });
});

Wpt.form.initQuestionAnswered = function(form) {
    form.bind('question_answered_initially.wpt', function(event, question) {
        question.addClass('answered');
        question.find('.answer input:first').removeAttr('required').removeAttr('aria-required');
    }).bind('question_unanswered_initially.wpt', function(event, question) {
        question.removeClass('answered');
        question.find('.answer input:first').attr('required', 'required').attr('aria-required', 'true');
    }).bind('answer_selected.wpt', function (event, answer) {
        answer.addClass('selected');
    }).bind('answer_unselected.wpt', function (event, answer) {
        answer.removeClass('selected');
    });
};

Wpt.initEvercookie = function() {
    if (typeof evercookie === 'undefined') {
        return;
    }

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
        ec.set('device_uuid', best || uuid.v4());
    }, 1);
};

Wpt.form.setupSubmitDisable = function(form) {
    var button = form.find('.button');

    form.bind('test_filled.wpt', function() {
        button.removeClass('disabled');
    }).bind('test_unfilled.wpt', function() {
        button.addClass('disabled');
    });
};

Wpt.form.setupResetAnswers = function(form) {
    if (!form.data('settings').isResetAnswersOnBack) {
        return;
    }
    form.bind('init_answers.wpt', function(event, answersInputs) {
        answersInputs.attr('checked', false);
    });
};

Wpt.form.setupProgressMeter = function($, form) {
    if (!form.data('settings').isShowProgressMeter) {
        return;
    }

    var initialTitle = document.title,
        separator    = Wpt.titleSeparator,
        template     = Wpt.percentsAnswered;

    $(document).bind('percentage_change.wpt', function(event, percent) {
        document.title = template.replace('{percentage}', percent) +  ' ' + separator + ' ' + initialTitle;
    });
};

Wpt.form.setupQuestionsAnswered = function($, form) {
    var questionsAnswered  = form.data('questions').answered,
        questions          = form.find('.question'),
        questionsMinFilled = questionsAnswered + questions.length,
        questionsTotal     = form.data('questions').total;

    var answersInputs = form.find('input:radio,input:checkbox');
    form.trigger('init_answers.wpt', [answersInputs])
        .trigger('test_unfilled.wpt');

    function replacePlaceholdersIn(el) {
        var NODE_TEXT_NODE = 3,
            RE_PLACEHOLDER = /(_{2,})/g;

        el.add(el.children()).contents().filter(function() {
            var isText = (this.nodeType == NODE_TEXT_NODE);

            if (!isText) {
                return false;
            }

            return RE_PLACEHOLDER.test($(this).text());
        }).replaceWith(function() {
            return $(this).text().replace(RE_PLACEHOLDER, '<span class="placeholder">$1</span>');
        });

        return el;
    };

    form.find('.question').each(function () {
        var question = $(this),
            title    = question.find('.title .title');

        replacePlaceholdersIn(title);
        var placeholder = title.find('.placeholder');

        question.data('isAnswered', false);
        var questionAnswersInputs = question.find('.answer input');
        question.find('.answer').each(function () {
            var answer = $(this);
            answer.find('input').bind('change', function () {
                answer.data('isSelected', !!$(this).attr('checked'));
                if (answer.data('isSelected')) {
                    form.trigger('answer_selected.wpt', [answer]);
                    questionAnswersInputs.each(function (i, otherInput) {
                        var $el = $(otherInput);
                        if ($el.closest('.answer').data('isSelected') != !!$(otherInput).attr('checked')) {
                            $el.change();
                        }
                    });
                } else {
                    form.trigger('answer_unselected.wpt', [answer]);
                }
                if (!answer.data('isSelected')) {
                    var isAllCheckboxesEmpty = (0 == questionAnswersInputs.filter(':checked').length);
                    if (isAllCheckboxesEmpty) {
                        question.data('isAnswered', false);
                        questionsAnswered--;
                        form.trigger('question_unanswered_initially.wpt', [question, questionsAnswered, questionsTotal, questionsMinFilled]);
                        form.trigger('question_unanswered.wpt', [question, answer, placeholder]);
                    }
                    return;
                }
                if (!question.data('isAnswered')) {
                    question.data('isAnswered', true);
                    questionsAnswered++;
                    form.trigger('question_answered_initially.wpt', [question, questionsAnswered, questionsTotal, questionsMinFilled]);
                }
                form.trigger('question_answered.wpt', [question, answer, placeholder]);
            });
        });
    });

    function calculateAnswersPercentage(event, question, answered, total, minFilled) {
        var percent = Math.round(100 * (answered / total));
        $(document).trigger('percentage_change.wpt', [percent]);
        if (answered == minFilled) {
            form.trigger('test_filled.wpt');
        } else {
            form.trigger('test_unfilled.wpt');
        }
    };
    form.bind('question_answered_initially.wpt',   calculateAnswersPercentage)
        .bind('question_unanswered_initially.wpt', calculateAnswersPercentage);

    if (questionsAnswered > 0) {
        calculateAnswersPercentage({}, form.find('.question:first'), questionsAnswered, questionsTotal, questionsMinFilled);
    }
    answersInputs.filter(':checked').change();
};
