jQuery(document).ready(function($) {
    // Integrating line diagram
    var wrapper = $('.scales.diagram');
    if(typeof wpt_line_diagram === 'undefined' || wrapper.length == 0) {
        return;
    }

    if (!Raphael.type) {
        wrapper.html(wpt_line_diagram.warningIncompatibleBrowser).addClass('wpt_warning');
        return;
    }

    var holder  = $('<div/>').appendTo(wrapper).attr('id', 'holder-' + Raphael.createUUID()),
        diagram = new WptLineDiagram(wrapper[0], holder[0], wpt_line_diagram.scales, $);
});
