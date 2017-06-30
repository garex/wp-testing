jQuery(document).ready(function($) {
    $('a.wpt_rateus').click(function() {
        $.post(ajaxurl, {
            action: 'wpt_rateus',
            _ajax_nonce: Wpt.nonce.feedbackRateUs
        });
    });
});
