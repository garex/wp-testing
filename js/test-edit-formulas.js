jQuery(document).ready(function($) {
    var box             = $('#wpt_edit_formulas'),
        formulas        = box.find('.wpt_formulas input[type=text]'),
        activeFormula   = formulas.length > 0 ? $(formulas[0]) : $('<input/>'),
        helpers         = box.find('.wpt_formulas_helper').find('[type=button]');

    formulas.focus(function() {
        activeFormula = $(this);
    });

    helpers.click(function() {
        var helper       = $(this),
            selection    = activeFormula.fieldSelection(),
            insertValue  = helper.data('source').replace('{selection}', selection.text),
            isSelected   = selection.length > 0;

        if (isSelected) {
            activeFormula.fieldSelection(insertValue);
        } else {
            var value       = activeFormula.val(),
                isEmpty     = value.length == 0,
                endsOnSpace = value.substring(value.length - 1) == ' ',
                prefix      = (isEmpty || endsOnSpace) ? '' : ' ';

            activeFormula.val(value + prefix + insertValue + ' ');
        }

        activeFormula.focus();
    });

});

// Setup question/answer helper source
jQuery(document).ready(function($) {
    var helper   = $('#wpt_edit_formulas .wpt_formulas_helper .question-answer'),
        template = helper.data('sourceTemplate'),
        inputs   = helper.find('input');

    inputs.change(function() {
        var source = template;

        inputs.each(function() {
            source = source.replace($(this).data('replaceFrom'), $(this).val());
        });

        helper.data('source', source);
    }).click(function() {
        return false;
    }).change();

});
