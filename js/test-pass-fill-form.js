jQuery(document).ready(function($) {
    var form = $('#wpt-test-form'),
        _    = lodash;

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
