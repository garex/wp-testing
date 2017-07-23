jQuery(document).ready(function($) {
    var wrapper = $('.asap-wrap');

    $('#Asap').change(function() {
        if ($(this).prop('checked')) {
            wrapper.removeClass('asap-0').addClass('asap-1');
        } else {
            wrapper.removeClass('asap-1').addClass('asap-0');
        }
    });
});
