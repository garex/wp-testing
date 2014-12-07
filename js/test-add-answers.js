jQuery(document).ready(function($) {
    var superBox                = $('#wpt_edit_questions'),
        globalAnswerLink        = superBox.find('a[href=#wpt_answerdiv]'),
        globalAnswer            = $('#wpt_answerdiv'),
        globalAnswerHandler     = globalAnswer.find('.hndle'),
        individualAnswers       = superBox.find('.wpt-add-individual-answers'),
        individualAnswersPanels = individualAnswers.find('.wp-hidden-child'),
        toggledPanels           = [],
        isAllToggled            = false;

    individualAnswers.each(function () {
        var box     = $(this),
            toggle  = box.find('.toggle'),
            panel   = box.find('.wp-hidden-child'),
            id      = toggle.prop('href');

        box.addClass('wp-hidden-children');
        toggle.click(function() {
            panel.toggle();

            toggledPanels.push(id);
            toggledPanels = _.uniq(toggledPanels);
            if (!isAllToggled && toggledPanels.length >= 2) {
                setTimeout(function() {
                    individualAnswersPanels.show();
                }, 500);
                isAllToggled = true;
            }

            return false;
        });
    });

    globalAnswerLink.click(function() {
        var isAnimated = false;
        $('html, body').animate({scrollTop: globalAnswer.offset().top - 32}, {
            duration: 800,
            complete: function() {
                if (isAnimated) {
                    return;
                }
                setTimeout(function() {
                    globalAnswerHandler.click();
                }, 200);
                setTimeout(function() {
                    globalAnswerHandler.click();
                }, 200 + 400);
                isAnimated = true;
            }
        });
        return false;
    });
});
