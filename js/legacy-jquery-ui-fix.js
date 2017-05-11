// Nothing here as it's an empty stub for deregistered javascripts.
if (typeof jQuery.curCSS === 'undefined') {
    jQuery.curCSS = function(element, prop, val) {
        return jQuery(element).css(prop, val);
    };
}
