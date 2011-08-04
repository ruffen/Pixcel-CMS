$(document).ready(function () {
    setMenuEvents();
    getfolderlist()

    $('#fileuploader').fancybox({
        titleShow: false,
        ajax: {
            complete: function (data) {
                getFileuploadElement();
                $('#uploadfiles').bind('click', function () {
                    doUpload($('#file_upload_form'));
                });
                $('#moreuploads').bind('click', function () {
                    getFileuploadElement();
                });
                $('#cancelfiles').bind('click', function () {
                    $.fancybox.close();
                });
            }
        }
    });
    if ($('#folderId').attr('value') != null) {
        getFilelist($('#folderId').attr('value'));
    }
    $('#deletefolder').bind('click', function () {
        getMessage('confirm_delete_folder', requestDelete);
        return false;
    });
    $('#createfolder, #renamefolder').bind('click', function () {
        if ($(this).attr('id') == 'createfolder') {
            $('#folderId').attr('value', 'new');
        }
        doRequest('filesystem/foldername', function (html) {
            $.fancybox(
			    html,
			    {
			        onComplete: function () {
			            $('#saveFoldername').bind('click', function () {
			                doRequest('filesystem/savefolder', function (result) {
			                    getMessage(result);
			                    if (isset(result.id)) {
			                        getFolder(result.id);
			                        getfolderlist();
			                    }
			                }, { 'id': $('#folderId').attr('value'), 'name': $('#changeFoldername').attr('value') });
			                $.fancybox.close();
			                return false;
			            });
			            $('#cancelFoldername').bind('click', function () {
			                $.fancybox.close();
			                return false;
			            });
			            if ($('#cancelmessage').length > 0) {
			                $('#cancelmessage').bind('click', function () {
			                    $.fancybox.close();
			                    return false;
			                });
			            }
			        }
			    }
             );
        }, { 'id': $('#folderId').attr('value') }, 'html');
        return false;
    });
    $('#changestructure').bind('click', function () {
        if ($('#foldertreeChanges').hasClass('hidden')) {
            $('#foldertreeChanges').removeClass('hidden')
            getfolderlist(true);
        } else {
            $('#foldertreeChanges').addClass('hidden')
            getfolderlist(false);
        }
        return false;
    });
    $('#saveChanges').bind('click', function () {
        postData = $('#dhtmlgoodies_tree2').serializeTree("href", "folders");

        doRequest('filesystem/savefolders', function (result) {
            $('#foldertreeChanges').addClass('hidden')
            getfolderlist(false);
        }, postData);
        return false;
    });
    $('#cancelChanges').bind('click', function () {
        $('#changestructure').trigger('click');
        return false;
    });
    $('.fileactionbox').bind('check', function (e) {
        var action = $('#fileoptionsone').attr('value');
        var files = $('input[name=files]:checked');
        var numfiles = files.length;
        if (numfiles == 0 || action == 'Select') {
            $('.fileactionbox').attr('checked', false);
            return false;
        }
        loadinggif($('.filetable tbody'));
        files.each(function (index, element) {
            doRequest('filesystem/' + action, function (result) {
                if (index + 1 == numfiles) {
                    $('.fileactionbox').attr('checked', false);
                    getFilelist($('#folderId').attr('value'), true);
                }
            }, { 'fileId': $(this).attr('value') });
        });
    });
    $('#fileoptionstwo').bind('change', function () {
        $('#fileoptionsone').attr('selectedIndex', $(this).attr('selectedIndex'));
    });
    $('#fileoptionsone').bind('change', function () {
        $('#fileoptionstwo').attr('selectedIndex', $(this).attr('selectedIndex'));
    });
});
getfolderlist = function (draggable) {
    loadinggif($('.file_tree'));
    var data = {};
    if (isset(draggable) && draggable) {
        data.drag = 'true';
    } else {
        data.drag = 'false';
    }
    doRequest('filesystem/foldermenu', function (list) {
        $('.file_tree').html(list);
        setMenuEvents();
    }, data, 'html');
}
getFileuploadElement = function () {
    var index = $('.fileuploadcontainer').length;
    //reset indexes
    $('#uploadcontainer input[type=file]').each(function (index, element) {
        $(element).attr('name', 'file_' + index);
        $(element).attr('id', 'file_' + index);
    });
    $('#uploadcontainer span.removeupload').each(function (index, element) {
        $(element).attr('id', 'removeupload_' + index);
    });
    doRequest('filesystem/uploadelement', function (element) {
        $('#uploadcontainer').append(element);
        $('#removeupload_' + index).bind('click', function () {

            $(this).parent('div.fileuploadcontainer').remove();
            $.fancybox.resize();
            return false;
        });
        $.fancybox.resize();
    }, { 'index': index }, 'html');
}

requestDelete = function () {
    doRequest('filesystem/deletefolder', function (result) {
        getMessage(result);
        getFolder('');
        getfolderlist();
    }, { 'id': $('#folderId').attr('value') });
}
setMenuEvents = function () {
    if ($('#dhtmlgoodies_tree2 img').length == 0) {
        treeObj = new JSDragDropTree();
        treeObj.setTreeId('dhtmlgoodies_tree2');
        treeObj.setMaximumDepth(7);
        treeObj.initTree();
        treeObj.expandAll();
    }
    $('#dhtmlgoodies_tree2 a').bind('click', function () {
        getFolder($(this).attr('href').replace('folder_', ''));
        return false;
    });
}
getFilelist = function (folderId, publish) {
    var postdata = {};
    postdata.id = folderId;
    if (isset(publish) && publish == true) {
        postdata.publish = 'publish';
    }
    $.ajax({
        type: 'POST',
        url: '/?rt=filesystem/filelist',
        data: postdata,
        dataType: 'html',
        success: function (filelist) {
            $('.filetable tbody').html(filelist);
            $('#folderId').attr('value', folderId);
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
}
getFolder = function (folderId) {
    loadinggif($('.filetable tbody'));
    $.ajax({
        type: 'POST',
        url: '/?rt=filesystem/changefolder',
        data: ({
            'id': folderId
        }),
        dataType: 'json',
        success: function (folder) {
            $('.foldername').html(folder.foldername);
            $('#folderId').attr('value', folderId);
            getFilelist($('#folderId').attr('value'));
            if (folder.resourcefolder) {
                $('#deletefolder').parent('li').addClass('hidden');
            } else {
                $('#deletefolder').parent('li').removeClass('hidden');
            }
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
}
doUpload = function (form) {
    var loadUrl = form.attr('action');

    $('div.fileuploadcontainer').each(function (index, element) {
        $.ajaxFileUpload({
            url: loadUrl + '&fileindex=' + index,
            secureuri: false,
            fileElementId: 'file_' + index,
            dataType: 'json',
            success: function (data, status) {
                if (typeof (data.error) != 'undefined') {
                    alert(data.error + 'error top');
                    getMessage(data);
                } else {
                    if (index + 1 == $('div.fileuploadcontainer').length) {
                        fileUploaded(data, true);
                    } else {
                        fileUploaded(data, false);
                    }

                }
            },
            error: function (data, status, e) {
                alert('data: ' + data.responseText + 'url: ' + loadUrl + '&index=' + index);
                getMessage(data);
            }
        });
    });
}
fileUploaded = function (data, done) {
    $.ajax({
        type: 'POST',
        url: '/?rt=filesystem/savefile',
        data: ({
            'id': $('#folderId').attr('value'),
            'filepath': data.filepath
        }),
        dataType: 'json',
        success: function (data) {
            if (done) {
                getMessage(data);
                getFilelist($('#folderId').attr('value'), true);
            }
        },
        error: function (data) {
            getMessage(data);
        }
    });
}