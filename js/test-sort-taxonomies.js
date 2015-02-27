// inspired by the-taxonomy-sort plugin
jQuery(document).ready(function($) {
    var taxonomies = [
        'wpt_answer',
        'wpt_result',
        'wpt_scale'
    ];

    $.each(taxonomies, function(i, taxonomy) {
        var all  = $('#' + taxonomy + 'div'),
            list = $('#' + taxonomy + 'checklist');

        list.sortable({
            forcePlaceholderSize    : true,
            placeholder             : 'sortable-placeholder',
            items                   : '> li',
            cursor                  : 'move',
            axis                    : 'y',
            containment             : all
        });
    });
});
