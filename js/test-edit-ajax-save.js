jQuery(document).ready(function($) {
    var form = $('form#post').on('submit.wpt', ajaxSubmit),
        markerClass = 'wpt-ajax-save',
        currentSubmit = null;

    form.find(':submit').click(function() {
        currentSubmit = {name: this.name, value: this.value};
    });

    function defaultSubmit() {
        form.off('submit.wpt').removeClass(markerClass).submit();
    };

    function isPreview() {
        return /^wp-preview/.test(form.attr('target'));
    };

    function ajaxSubmit() {
        if (isPreview()) {
            currentSubmit = null;
            return true;
        }
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
            tinyMCE.activeEditor.save();
        }

        var data = form.serializeArray();
        if (currentSubmit !== null) {
            data.push(currentSubmit);
        }

        form.addClass(markerClass);
        $.post(form.attr('action'), data, function(response) {
            currentSubmit = null;
            if (typeof response.success == 'undefined') {
                defaultSubmit();
                return;
            }

            if (!response.success) {
                showError(response.error.title, response.error.content);
                return;
            }

            redirectTo(response.redirectTo);
        }).fail(function(response) {
            currentSubmit = null;
            defaultSubmit();
        });

        return false;
    };

    function redirectTo(url) {
        $(window).unbind('beforeunload.edit-post');
        window.onbeforeunload = null;
        window.location.href = url;
    };

    function showError(title, content) {
        $('<div class="error wpt_test_editor">' + content + '</div>').dialog({
            modal: true,
            title: title,
            dialogClass: 'wp-dialog',
            width: 400,
            buttons: [{
                text: Wpt.locale.OK,
                click: function() {
                    $(this).dialog('destroy').remove();
                }
            }]
        });
    };
});
