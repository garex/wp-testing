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

    var options  = new WptLineDiagramOptions(),
        scales   = [],
        maximums = [];
    $.each(Wpt.scales, function(i, scale) {
        maximums.push(scale.maximum);
        scale.valueTitle = scale.outOf;
        scales.push(scale);
    });
    maximums.sort();

    var isScalesLengthSame = (maximums[0] == maximums[maximums.length - 1]),
        isSwitchToPercents = !isScalesLengthSame;
    if (isSwitchToPercents) {
        $.each(scales, function(i, scale) {
            var newValue  = Math.round(scale.ratio * 100);
            scale.valueTitle = newValue + '%\n' + scale.outOf;
            scale.value   = newValue;
            scale.minimum = 0;
            scale.maximum = 100;
        });
        options.setValueAxisTemplate('{value}%');
    }

    if (scales.length > 5) {
        options.setTextAxisAngle(45);
    }

    var holder  = $('<div/>').appendTo(wrapper).attr('id', 'holder-' + Raphael.createUUID()),
        diagram = new WptLineDiagram(wrapper[0], holder[0], scales, $, options);
});
