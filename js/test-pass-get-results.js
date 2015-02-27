jQuery(document).ready(function($) {
    // Integrating line diagram
    var wrapper = $('.scales.diagram');
    if(typeof Wpt === 'undefined' || wrapper.length == 0) {
        return;
    }

    if (!Raphael.type) {
        wrapper.html(Wpt.warningIncompatibleBrowser).addClass('wpt_warning');
        return;
    }

    if (!$.isArray(Wpt.scales) || Wpt.scales.length == 0) {
        return;
    }

    var holder  = $('<div/>').appendTo(wrapper).attr('id', 'holder-' + Raphael.createUUID()),
        diagram = new WptLineDiagram(wrapper[0], holder[0], Wpt.scales, $);
});
