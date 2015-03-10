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

        list.find('li').addClass('wpt-sortable').each(function() {
            var hasChildren = $(this).find('> ul').length > 0;
            if (hasChildren) {
                $(this).addClass('wpt-sortable-container');
            }
        });

        list.sortable({
            forcePlaceholderSize    : true,
            placeholder             : 'sortable-placeholder',
            items                   : '.wpt-sortable',
            cursor                  : 'move',
            axis                    : 'y',
            containment             : all
        });
    });
});
