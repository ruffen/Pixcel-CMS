var checking = false;
$(window).load(function () {
    $('#signupform').submit(function () {
        var allok = true;
        if ($('.errorvalidation.hidden').length != $('.errorvalidation').length || checking) {
            allok = false;
        }
        if (!$('#Licenseagreement').attr('checked')) {
            $('#Licenseagreement').siblings('.errorvalidation').removeClass('hidden');
            allok = false;
        } else {
            $('#Licenseagreement').siblings('.errorvalidation').addClass('hidden');
        }
        if ($('input[value=]').length > 0) {
            $('input[value=]').siblings('.errorvalidation').removeClass('hidden');
            allok = false;
        }
        if (!allok) {
            return allok;
        }
        $.post(
            $(this).attr('action'),
            $(this).serialize(),
            function (data) {
                $('#rightcol').html(data);
            }
        );
        return false;
    });
    var TypewatchOptions = {
        callback: CheckUniqueField,
        wait: 300,
        highlight: true,
        captureLength: 2
    }
    $('#Subdomain').typeWatch(TypewatchOptions);
    TypewatchOptions.callback = CheckEmail;
    TypewatchOptions.wait = 100;
    $('#Email').typeWatch(TypewatchOptions);
    TypewatchOptions.callback = HasValue;

    $('input[type=text]').bind('keyup', HasValue);
    $('input[type=text]').blur(HasValue);

});
HasValue = function () {
    if ($(this).val().trim() == "") {
        $(this).siblings('.errorvalidation').removeClass('hidden');
    } else if($(this).attr('id') != "Email") {
        $(this).siblings('.errorvalidation').addClass('hidden');
    }
}
function CheckEmail(value) {
    if (!ValidateEmail(value)) {
        $(this.el).siblings('.errorvalidation').removeClass('hidden');
    } else {
        $(this.el).siblings('.errorvalidation').addClass('hidden');
    }
}
var req;
function CheckUniqueField(value) {
    checking = true;
    if (isset(req)) {
        req.abort();
    }
    var element = $(this.el);
    req = doRequest('signup/CheckUniqueFields',
        function (data) {
            if (!data.unique) {
                $(element).siblings('.errorvalidation').removeClass('hidden');
            } else {
                $(element).siblings('.errorvalidation').addClass('hidden');
            }
            checking = false;
        }, { 'fieldname': $(this.el).attr('id'), 'value': value });
}
