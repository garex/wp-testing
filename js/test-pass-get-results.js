Wpt.initWebshim = function(baseUrl) {
    if (this.initialized || typeof webshim === 'undefined') {
        return;
    }
    this.initialized = true;
    webshim.setOptions({
        waitReady : true,
        basePath  : baseUrl
    });
    webshims.polyfill('es5');
};

if (Wpt.webshimBaseurl) {
    Wpt.initWebshim(Wpt.webshimBaseurl);
}

jQuery(document).ready(function($) {
	Wpt.initWebshim(Wpt.webshimBaseurl);

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
        scale.abbrTitle  = scale.title.substring(0, 3);
        scale.ratioTitle = '';
        scales.push(scale);
    });
    maximums.sort();

    var isScalesLengthSame = (maximums[0] == maximums[maximums.length - 1]),
        isSwitchToPercents = !isScalesLengthSame;
    if (isSwitchToPercents) {
        $.each(scales, function(i, scale) {
            scale.value   = Math.round(scale.ratio * 100);
            scale.ratioTitle = scale.value + '%';
            scale.minimum = 0;
            scale.maximum = 100;
        });
        options.setValueAxisTemplate('{value}%');
    }

    var holder  = $('<div/>').appendTo(wrapper).attr('id', 'holder-' + Raphael.createUUID()),
        diagram = new WptLineDiagram(wrapper[0], holder[0], scales, $, options);
});
