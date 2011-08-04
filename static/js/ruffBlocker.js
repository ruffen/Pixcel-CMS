var RuffBlocker = Class.extend({
    height: 0,
    width: 0,
    unblockbutton: '',
    loadFunction: null,
    init: function(pathToContent, unBlockId, loadFunction, values) {
        this.setContent(pathToContent, unBlockId, loadFunction, values);
        this.setHeight();
        this.placeMiddle();
    },
    setHeight: function() {
        this.height = $(document).height();
        $('#ruffBlocker').height(this.height);
    },
    placeMiddle: function() {
        var windowWidth = $(window).width();
        var objectWidth = $('#ruffBlockerContent').width();
        var left = Math.ceil(windowWidth / 2) - Math.ceil(objectWidth / 2);
        left = Math.ceil(left);
        $('#ruffBlockerContent').css('left', left);
    },
    block: function() {
        $('#ruffBlocker').css('display', 'block');
        $('#ruffBlockerContent').css('display', 'block');
    },
    setContent: function(path, unBlockId, loadFunction, values) {
        if (values === null || values === undefined) {
            values = {};
        }
        $.ajax({
            type: "POST",
            url: path,
            data: (values),
            dataType: 'html',
            success: function(data) {
                $('#ruffBlockerContent').html(data);
                if (loadFunction !== null && loadFunction !== undefined) {
                    loadFunction();
                }
                $('#' + unBlockId).bind('click', function() {
                    $('#ruffBlocker').css('display', 'none');
                    $('#ruffBlockerContent').css('display', 'none');
                    $('#ruffBlockerContent').html('');
                    return false;
                });
            }
        });
    }
});