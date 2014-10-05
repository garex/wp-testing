jQuery(document).ready(function($) {
    var box             = $('#wpt_edit_formulas'),
        formulas        = box.find('.wpt_formulas input[type=text]'),
        activeFormula   = formulas.length > 0 ? $(formulas[0]) : $('<input/>'),
        helpers         = box.find('.wpt_formulas_helper input[type=button]');

    formulas.focus(function() {
        activeFormula = $(this);
    });

    helpers.click(function() {
        var helper       = $(this),
            insertValue  = helper.data('source'),
            isSelected   = activeFormula.fieldSelection().length > 0;

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
