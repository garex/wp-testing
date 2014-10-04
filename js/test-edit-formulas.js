jQuery(document).ready(function($) {
    var box      		= $('#wpt_edit_formulas'),
        formulas 		= box.find('.wpt_formulas input'),
        activeFormula	= formulas.length > 0 ? $(formulas[0]) : $('<input/>'),
        helpers         = box.find('.wpt_formulas_helper input[type=button]');

    formulas.focus(function() {
        activeFormula = $(this);
    });

    helpers.click(function() {
        var currentValue = activeFormula.val(),
            newValue     = currentValue,
            helper       = $(this);

        if (newValue.length > 0 && newValue.substring(newValue.length - 1) != ' ') {
            newValue += ' ';
        }
        if (typeof helper.data == 'undefined') {
            newValue += helper.val();
        } else {
            newValue += helper.data('source');
        }
        newValue += ' ';

        activeFormula.val(newValue);
        activeFormula.focus();
    });

});
