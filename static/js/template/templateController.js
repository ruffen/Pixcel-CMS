$(document).ready(function () {
    $('.siteSelectList').addClass('hidden');
    getTemplatemenu();
    getSpots();
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
            var button = replaceButtonwithloading($(this));
            saveTemplate(button);
        }
        return false;
    });
    $('#canceltemplate').bind('click', function () {
        if ($('templateId').attr('value') == 'new') {
            getTemplatemenu(true);
        } else {
            var button = replaceButtonwithloading($(this));
            $('#tpl_' + $('#templateId').attr('value')).trigger('click');
        }
        return false;
    });
    $('#createTemplate').bind('click', function () {
        $('#templateId').attr('value', 'new');
        $('input[type=text]').attr('value', '');
        $('#templateheader').text('Unnamed');
        $('#spottable tr').remove();
        $("select#site").get(0).selectedIndex = 0;
        $('#resourceFolderId').attr('value', 0);
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
    $('#resourcefolder').bind('click', function () {
        filebrowser($('#resourceFolderId').attr('value').replace('current_', ''));
        return false;
    });
    $('#site').bind('change', function () {
        $('#resourcefoldername').attr('value', '');
        $('#resourceFolderId').attr('value', 0);
    });
});
filebrowser = function (parent, direction) {
    loadinggif($('#ftpbrowser_inner'));
    data = {};
    data.id = parent;
    data.type = 'folder';
    data.siteId = $('#site').attr('value');
    if (isset(direction)) {
        data.direction = 'up';
    }
    doRequest('filesystem/getfilebrowser', function (data) {
        $.fancybox(
			data,
			{
			    onComplete: function () {
			        if ($('#currentroot').attr('value') == 'current_' + 0) {
			            $('#ftp_up').parent('li.up_one_level').addClass('hidden');
			        } else {
			            $('#ftp_up').bind('click', function () {
			                filebrowser(parent, 'up');
			                return false;
			            });
			        }
			        $('#cancelbrowse').bind('click', function () {
			            $.fancybox.close();
			            return false;
			        });
			        $('#selectfolder').bind('click', function () {
			            $('#resourcefoldername').attr('value', $('#currentrootName').attr('value'));
			            $('#resourceFolderId').attr('value', $('#currentroot').attr('value'));
			            $.fancybox.close();
			            return false;
			        });
			        $('.directory  a').bind('click', function () {
			            filebrowser($(this).attr('href').replace('folder_', ''));
			            return false;
			        });
			    }
			});
    }, data, 'html');
}
getTemplatemenu = function (trigger) {
    $('.templatemenu').each(function (index, element) {
        doRequest('template/templatemenu', function (menu) {
            $(element).html(menu);
            $('#templatemenu_' + $(element).attr('rel') + ' li a').unbind();
            $('#templatemenu_' + $(element).attr('rel') + ' li a').bind('click', function () {
                getTemplate($(this));
                return false;
            });
            if (isset(trigger) && trigger) {
                $('#templatemenu_' + $(element).attr('rel') + ' li:first a').trigger('click');
            }
        }, { 'siteId': $(element).attr('rel') }, 'html');
    });
}
getTemplate = function (element) {
    $('#templateId').attr('value', $(element).attr('id').replace('tpl_', ''));
    $('#templatename').attr('value', $(element).text());
    $('#templateheader').text($(element).text());
    doRequest('template/getTemplateDetails', function (template) {
        if (isset(template.siteId)) {
            $('#site').attr('value', template.siteId);
            $('#resourcefoldername').attr('value', template.foldername);
            $('#resourceFolderId').attr('value', template.folderId);
            replaceButtonWithCancel($('#canceltemplate'));
        }
    }, { 'id': $('#templateId').attr('value') });
    getSpots();
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
			            getSpot(spotId, $('tr[rel="' + tplSpotId + '"]').attr('id').replace('spot_', ''), tplSpotId);
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
    }, {
        'path': uploadData.filepath
    });
}
saveTemplate = function (button) {
    var spotArray = new Array();
    $('#spottable tr').each(function (index, element) {
        spotArray[index] = $(element).attr('rel') + '-' + $(element).children('td:nth-child(2)').children('span').children('select').attr('value') + '-' + $(element).children('td:nth-child(1)').children('span.spotname').text();
    });
    var data = {
        'name': $('#templatename').attr('value'),
        'path': $('#filename').text(),
        'spots[]': spotArray,
        'id': $('#templateId').attr('value'),
        'folderId': $('#resourceFolderId').attr('value').replace('current_', ''),
        'siteId': $('#site').attr('value')
    };
    doRequest('template/savetemplate', function (data) {
        getMessage(data);
        if (isset(data.elements)) {
            validate(data.elements);
        }
        if (isset(data.id) && isset(data.success)) {
            $('#templateId').attr('value', data.id)
            getTemplatemenu();
            getSpots();
        }
        if (isset(button)) {
            $('#saveTemplate').html(button);
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
    if ($('tr[rel="' + tplSpotId + '"]').length > 0) {
        loadinggif($('tr[rel="' + tplSpotId + '"]'));
    }
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
            if ($('tr[rel="' + tplSpotId + '"]').length > 0) {
                $('tr[rel="' + tplSpotId + '"]').replaceWith(tr);
            } else {
                $('#spottable').append(tr);
            }
            var orderedList = $('#spottable tr').sort(sortId);
            $('#spottable').html(orderedList);
            setSpotEvents();
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
}
getSpots = function () {
    loadinggif($('#spottable'));
    doRequest('template/getspots', function (templates) {
        $('#spottable').html(templates);
        setSpotEvents();
    }, { 'id': $('#templateId').attr('value') }, 'html');
}
setSpotEvents = function () {
    $('.spotconfig').unbind();
    $('.spotconfig').bind('click', function () {
        var tplSpotId = $(this).parent('td').parent('tr').attr('rel');
        showSpotConfig($('select[rel="' + tplSpotId + '"]').attr('value'), tplSpotId);
        return false;
    });
    $('.spottype').unbind();
    $('.spottype').bind('change', function () {
        var spotId = $(this).attr('value');
        var spotTplId = $(this).attr('rel');
        doRequest('template/spothasconfig', function (result) {
            if (isset(result.spotconfig) && result.spotconfig) {
                $('td[rel="' + spotTplId + '"].configcontainer').children('a').removeClass('hidden');
            } else {
                $('td[rel="' + spotTplId + '"].configcontainer').children('a').addClass('hidden');
            }
        }, { 'spotId': spotId });
    });
}
setText = function(data) {
    $('#filename').text(data.filepath);
}
ajaxFileUpload = function (success) {
    $.ajaxFileUpload({
        url: '/?rt=files/upload',
        secureuri: false,
        fileElementId: 'file',
        dataType: 'json',
        success: function (data, status) {
            if (isset(data.error)) {
                getMessage(data);
            } else {
                success(data);
            }
        },
        error: function (data, status, e) {
            alert('data: ' + data.responseText + 'url: ' + loadUrl + '&index=' + index);
            getMessage(data);
        }
    });
}