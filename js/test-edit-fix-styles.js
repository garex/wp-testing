jQuery(document).ready(function($) {
    // Fix hardcoded manual "*-last" classes, leaving only last
    $('#misc-publishing-actions .misc-pub-section')
        .removeClass('misc-pub-section-last')
        .last().addClass('misc-pub-section-last');
});
