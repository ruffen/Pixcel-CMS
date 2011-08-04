$(document).ready(function () {
    getTemplatemenu();
    getSpots();
    getTemplateresources();

    if ($('#templateId').attr('value') == 'new') {
        $('#resourcefilefields').addClass('hidden');
    }
    $('#fileSubmit').bind('click', function () {
        ajaxFileUpload(makeTemplate, 'file');
        return false;
    });
    $('#maketemplate').bind('click', function () {
        makeTemplate();
        return false;
    });
    $('#templatename').bind('keyup', function () {
        $('#templateheader').text($(this).attr('value'));
    });
    $('#saveTemplate').bind('click', function () {
        if ($('#assetlist ul li.error').length > 0) {
            alert('All assets need to be valid');
        } else {
            saveTemplate();
        }
        return false;
    });
    $('#createTemplate').bind('click', function () {
        $('#templateId').attr('value', 'new');
        $('input[type=text]').attr('value', '');
        $('#spottable tr, #resourcefiles tr').remove();
        $('#templateheader').text('Unnamed');
        return false;
    });
    $('#deleteTemplate').bind('click', function () {
        doRequest('template/deletetemplate', function (result) {
            getMessage(result);
            if (isset(result.success)) {
                getTemplatemenu(true);
            }
        }, { 'id': $('#templateId').attr('value') });
    });
    $('#tplFileSubmit').bind('click', function () {
        //$('#templatefiles_upload_form').submit();
        ajaxFileUpload(createTemplateFiles, 'templatefiles');
        return false;
    });
});
getTemplatemenu = function (trigger) {
    $('.templatemenu').each(function (index, element) {
        doRequest('template/templatemenu', function (menu) {
            $(element).html(menu);
            $('#site_' + $(element).attr('rel')).bind('click', function () {
                doRequest('sitemanagement/getsitedetails', function (site) {
                    getMessage(site);
                    //show the first template of each site
                    if ($('#templatemenu_' + $(element).attr('rel') + ' li:first a').length > 0) {
                        $('#templatemenu_' + $(element).attr('rel') + ' li:first a').trigger('click');
                    } else {
                        $('#createTemplate').trigger('click');
                    }

                }, { 'id': $(element).attr('rel') });
                return false;
            });
            $('#templatemenu_' + $(element).attr('rel') + ' li a').unbind();
            $('#templatemenu_' + $(element).attr('rel') + ' li a').bind('click', function () {
                $('#templateId').attr('value', $(this).attr('id').replace('tpl_', ''));
                $('#templatename').attr('value', $(this).text());
                $('#templateheader').text($(this).text());
                getSpots();
                getTemplateresources();
                return false;
            });
            if (isset(trigger) && trigger) {
                $('#templatemenu_' + $(element).attr('rel') + ' li:first a').trigger('click');
            }
        }, { 'siteId': $(element).attr('rel') }, 'html');
    });

}
showSpotConfig = function (spotId, tplSpotId) {
    doRequest('template/showspotconfig', function (data) {
        $.fancybox(
			data,
			{
			    onComplete: function () {
			        $('#cancelspotconfig').bind('click', function () {
			            $.fancybox.close();
			            return false;
			        });
			        $('#savespotconfig').bind('click', function () {
			            var data = {};
			            $('#configs input').each(function (index, element) {
			                var id = $(element).attr('id');
			                var value = $(element).attr('value');
			                data[id] = value;
			            });
			            data.id = $('#templateId').attr('value');
			            data.spotId = spotId;
			            data.tplSpotId = tplSpotId;
			            doRequest('template/saveSpotConfig', function (result) {
			                getMessage(result);
			            }, data);
			            $.fancybox.close();
			            return false;
			        });
			    }
			}
		);
    }, { 'id': $('#templateId').attr('value'), 'spotId': spotId, 'tplSpotId': tplSpotId }, 'html');
}
createTemplateFiles = function (uploadData) {
    doRequest('files/Unpack', function (filelist) {
        if (!isset(filelist.success)) {
            getMessage(filelist);
            return false;
        }
        getTemplateresources();
    }, {
        'path': uploadData.filepath
    });
}
getTemplateresources = function () {
    doRequest('template/getResourcefiles', function (data) {
        $('#resourcefiles').html(data);
    }, { 'id': $('#templateId').attr('value') }, 'html');
}
saveTemplate = function () {
    var spotArray = new Array();
    $('#spottable tr').each(function (index, element) {
        spotArray[index] = $(element).attr('rel') + '-' + $(element).children('td:nth-child(2)').children('span').children('select').attr('value');
    });
    var data = {
        'name': $('#templatename').attr('value'),
        'path': $('#filename').text(),
        'spots[]': spotArray,
        'id': $('#templateId').attr('value')
    };
    doRequest('template/savetemplate', function (data) {
        getMessage(data);
        if (isset(data.id) && isset(data.success)) {
            $('#templateId').attr('value', data.id)
            getTemplatemenu();
            getSpots();
            $('#resourcefilefields').removeClass('hidden');
        }
    }, data);
}
makeTemplate = function (uploadData) {
    doRequest('template/maketpl', function (template) {
        $('#templateName').attr('value', template.name);
        $.each(template.spots, function (index, spot) {
            if (typeof (spot.name) != 'undefined') {
                getSpot(0, index, spot.tplSpotId);
            } else {
                getSpot(spot.id, index, spot.tplSpotId);
            }
        });
        var orderedList = $('#spottable tr').sort(sortId);
        $('#spottable tr').html(orderedList);
    },
	{
	    'path': uploadData.filepath,
	    'name': $('#templatename').attr('value'),
	    'id': $('#templateId').attr('value')
	});
}
getSpot = function (spotId, index, tplSpotId) {
    $.ajax({
        type: 'POST',
        url: '/?rt=template/getspot',
        data: ({
            'spotId': spotId,
            'tplSpotId': tplSpotId,
            'index': index,
            'id': $('#templateId').attr('value')
        }),
        dataType: 'json',
        success: function (spot) {
            var tr = $(spot.html);
            $('#spottable').append(tr);
            var orderedList = $('#spottable tr').sort(sortId);
            $('#spottable').html(orderedList);
            $('.spotconfig').unbind();
            $('.spotconfig').bind('click', function () {
                showSpotConfig($(this).attr('rel'), $(this).parent('td').parent('tr').attr('rel'));
                return false;
            });
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
}
getSpots = function () {
    doRequest('template/getspots', function (templates) {
        $('#spottable').html(templates);
        $('.spotconfig').unbind();
        $('.spotconfig').bind('click', function () {
            showSpotConfig($(this).attr('rel'), $(this).parent('td').parent('tr').attr('rel'));
            return false;
        });
    }, { 'id': $('#templateId').attr('value') }, 'html');
}
setText = function(data) {
    $('#filename').text(data.filepath);
}

function ajaxFileUpload(doneFunction, uploadElement){
    $.ajaxFileUpload({
        url: '/?rt=files/upload',
        secureuri: false,
        fileElementId: uploadElement,
        dataType: 'json',
        success: function (data, status) {
            if (typeof (data.error) != 'undefined') {
                if (data.error != '') {
                    getMessage(data);
                } else {
                    doneFunction(data);
                }
            } else {
                doneFunction(data);
            }
        },
        error: function (data, status, e) {
            if (isset(data.success)) {
                doneFunction(data);
            } else if (isset(data.error)) {
                getMessage(data);
            } else {
                getMessage({ 'error': 'upload_failed' });
            }
        }
    });
	return false;
}  