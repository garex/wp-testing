jQuery(document).ready(function($) {
    var form = $('#wpt-test-form');

    if (0 == form.length) {
        return;
    }

    var button = form.find('.button');
    button.addClass('disabled').attr('disabled', 'disabled');

    var questions = [];
    form.find('input:radio').each(function() {
        questions.push($(this).attr('name'));
    });
    questions = _.uniq(questions);

    form.find('input:radio').change(function() {
        if (form.find('input:radio:checked').length < questions.length) {
            return;
        }
        button.removeAttr('disabled').removeClass('disabled');
    }).first().change();

});
