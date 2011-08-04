var siteMapEdit = false;
var lockChecked = false;
var showMessage = true;
tinyMCE.init({
    mode: "none",
    theme: "simple"
});
window.onbeforeunload = function(event){
	if($('#statusunp').length > 0 && $('#pagename').attr('disabled') != 'disabled'){
		return "You have unsaved changes";
	}
}
$(window).unload(function(event){
	if($('#statusunp').length > 0 && $('#pagename').attr('disabled') != 'disabled'){
		unlockPage($('#pageId').attr('value'));
	}
});

$(document).ready(function () {
    $("#languages").msDropDown();
    $('input[rel=change], select[rel=change], textarea[rel=change]').bind('change keyup', lockpage);
    getPagemap();

    $('#newpage').fancybox({
        titleShow: false,
        ajax: {
            type: 'post',
            data: { 'id': 'new' },
            complete: function (data) {
                $('#basic_save').bind('click', function () {
                    var values = {
                        'published': $('#basic_statusp').attr('value'),
                        'pagename': $('#basic_pagename').attr('value'),
                        'template': $('#basic_template').attr('value'),
                        'keywords': $('#basic_keywords').attr('value'),
                        'description': $('#basic_description').attr('value'),
                        'id': 'new'
                    };
                    showMessage = false;
                    doRequest('pages/savePage', saveDone, values);
                    $.fancybox.close();
                });
                $('#basic_cancel').bind('click', function () {
                    $.fancybox.close();
                });
            }
        }
    });
    $('#save').bind('click', function () {
        if (siteMapEdit) {
            alert('Cant save page while in sitemap edit mode.');
            return false;
        }
        var values = getPageValues();
        doRequest('pages/savePage', saveDone, values);
        return false;
    });
    $('#deletepage').bind('click', function () {
        if (siteMapEdit) {
            getMessage('save_sitemapmode');
            return false;
        }
        getMessage('confirm_delete', requestDelete);

        return false;
    });
    $('#editpage').bind('click', function () {
        doRequest($(this).attr('href'), function (data) {
            if (isset(data.error)) {
                getMessage(data);
            } else {
                $.fancybox(
			        data.html,
			        {
			            onComplete: spotmenu
			        }
                );
            }
        }, { 'id': $('#pageId').attr('value'), 'tplId': $('#template').val() });
        return false;
    });
    $('#cancel').bind('click', function () {
        lockChecked = false;
        doRequest('pages/getPagedetails', setPageValues, { 'id': $('#pageId').attr('value'), 'cancel': 'true' });
        return false;
    });
    $('#publishpage, #expirepage').bind('click', function () {
        var values = {
            'id': $('#pageId').attr('value'),
            'action': $(this).attr('id').replace('page', '')
        };
        doRequest('pages/publishpage', published, values);
        return false;
    });
    getRevisionlist();
    $('#editSitemap').bind('click', function () {
        if ($(this).attr('rel') == 'false') {
            $(this).attr('rel', 'true');
            getPagemap(true);
        } else {
            $(this).attr('rel', 'false');
            getPagemap(false);
        }
        return false;
    });
    $('#cancel_sitemap').bind('click', function () {
        $('#editSitemap').attr('rel', 'false');
        getPagemap(false);
        return false;
    });
    $('#save_sitemap').bind('click', function () {
        $(this).attr('rel', 'false');
        saveSitemap();
        return false;
    });
    $('#setIndex').bind('click', setIndex);
});
setIndex = function () {
    var oldelement = loadinggifReplaceSmall($('#setIndex'));
    doRequest('pages/indexpage', function (result) {
        getMessage(result);
        if (isset(result.success)) {
            $('#isIndexText').text(' - (index)');
            $('#setIndex').replaceWith($(oldelement));
            $('#setIndex').bind('click', setIndex);
            $('#setIndex').addClass('hidden');
            getPagemap();
        }

    }, { 'id': $('#pageId').attr('value') });
    return false;
}
saveSitemap = function () {
    //get sitemap without drag and drop
    var postData;
    postData = $('#dhtmlgoodies_tree2').serializeTree("rel", "pages");
    var url = 'pages/savesitemap';
    doRequest(url, function (result) {
        getPagemap(false);
        getMessage(result);
    }, postData);
}

sitemapEvents = function () {
    if ($('#dhtmlgoodies_tree2 ul').css('display') != 'block') {
        treeObj = new JSDragDropTree();
        treeObj.setTreeId('dhtmlgoodies_tree2');
        treeObj.setMaximumDepth(2);
        treeObj.initTree();
        treeObj.expandAll();
    }
    $('#dhtmlgoodies_tree2 a').unbind();
    $('#dhtmlgoodies_tree2 a').bind('click', function () {
        doRequest('pages/getPagedetails', setPageValues, { 'id': $(this).attr('rel') });
        return false;
    });
}
getPagemap = function (drag) {
    if (!isset(drag)) {
        drag = false;
    }
    if (drag) {
        $('#sitemap_buttons').removeClass('hidden');
    } else {
        $('#sitemap_buttons').addClass('hidden');
    }
    doRequest('pages/sitemap', function (menu) {
        $("#dhtmlgoodies_tree2").replaceWith(menu);
        sitemapEvents();
    }, { 'drag': drag }, 'html');
}
requestDelete = function(){
	doRequest('pages/deletePage', deleteDone, {'id' : $('#pageId').attr('value')});
}
unlockPage = function(pageId){
	doRequest('pages/unlockpage', getMessage, {'id' : pageId}, 'json', false);
}
lockpage = function(pageId){
	if(lockChecked){
		return true;
	}
	lockChecked = true;
	if(pageId.length === undefined){
		pageId = $('#pageId').attr('value');
	}
	doRequest('pages/lockPage', function(result){
		setPageLockStatus(result, true);
	}, {'id' : pageId});
}

setPageLockStatus = function(lockstatus, lock){
	if(lockstatus.error !== null && lockstatus.error !== undefined){
		//if we get an error, let 
		getMessage(lockstatus);
		lock = true;
	}
	if(lock){
		if(lock && lockstatus.success !== null && lockstatus.success !== undefined){
			$('input[rel=change], select[rel=change], textarea[rel=change]').removeAttr('disabled');
		}else{
			$('input[rel=change], select[rel=change], textarea[rel=change]').attr('disabled', 'disabled');		
		}
		$('#statusp, #statusunp').attr('id', 'statusunp');			
		$('#statusunp img').attr('src', $('#statusunp img').attr('src').replace('off', 'disabled'));
		$('#statusunp span').html('Page locked by ' + lockstatus.name);
		lockChecked = true;
	}else{
		$('input[rel=change], select[rel=change], textarea[rel=change]').removeAttr('disabled', 'disabled');
		if(lockstatus.name !== null && lockstatus.name !== undefined){
			$('#statusp, #statusunp').attr('id', 'statusunp');			
			$('#statusunp span').html('Page locked by ' + lockstatus.name);
			$('#statusunp img').attr('src', $('#statusunp img').attr('src').replace('off', 'disabled'));
		}else{
			$('#statusp, #statusunp').attr('id', 'statusp');					
			$('#statusp img').attr('src', $('#statusp img').attr('src').replace('disabled', 'off'));
			$('#statusp span').html('Page is not locked');
			lockChecked = false;
		}
	}
}
getPageValues = function(){
    var values = {
        'published': $('#statusp').attr('value'),
        'pagename': $('#pagename').attr('value'),
        'template': $('#template').attr('value'),
        'keywords': $('#keywords').attr('value'),
        'description': $('#description').attr('value'),
        'id': $('#pageId').attr('value')
    };
    return values;
}
published = function(result){
	getMessage(result);
	if(result.success != undefined && result.success != null){
		getRevisionlist();
		setpubstatusBar(result);	
	}
}
setpubstatusBar = function(result){
	var txtStatus = 'draft';
	switch(parseInt(result.status)){
		case 0 : txtStatus  = 'draft'; break;
		case 1 : txtStatus  = 'pub'; break
		case 2 : 
		case 3 : 
		case 4 : txtStatus  = 'withdrawn';break;
		case 5 : txtStatus  = 'expired';break;
		default : getMessage('pubstat_unknown');break;
	}
	$("#pubstatus p").removeClass('hidden');
	$("#pubstatus p").addClass('hidden');
	if(result.oldStatus != null && result.oldStatus != undefined && result.status == 0 && result.oldStatus > 0){
		switch(parseInt(result.oldStatus )){
			case 0 : txtOldStatus  = 'draft'; break;
			case 1 : txtOldStatus  = 'pub'; break
			case 2 : 
			case 3 : 
			case 4 : txtOldStatus  = 'withdrawn';break;
			case 5 : txtOldStatus  = 'expired';break;
			default : getMessage('pubstat_unknown');break;
		}
		$("#pubstatus p#" + txtOldStatus).removeClass('hidden');

	}
	$("#pubstatus p#" + txtStatus).removeClass('hidden');
	if(result.success == 'withdrawn'){
		$("#pubstatus p#" + txtStatus  + " span#widthrawStatus").html('Withdrawn');
	}
	$("#pubstatus p#" + txtStatus  + " span#" + txtStatus + "date").html(result.date);
}
deleteDone = function (result) {
    if (isset(result.success)) {
        getPagemap();
    }
    getMessage(result);
}
spotmenu = function() {
	if($('div.spotbutton a').length == 0){
		$('#acceptmessage').bind('click', function(){
			$.facebox.close();
			return false;
		});
	}
	lockpage($('#pageId').attr('value'));
    $('div.spotbutton a').bind('click', function() {
        doRequest($(this).attr('href').replace('?rt=', ''), runSpot, { 'spotId': $(this).attr('rel'), 'id': $('#pageId').attr('value') }, 'html');
        return false;
    });
}
getRevisionlist = function() {
    doRequest('pages/revisionlist', revisionlist, { 'id': $('#pageId').attr('value') }, 'html');
}
revisionlist = function(list) {
    $('#revisionTable').html(list);
    $('#revisionTable .delete a').bind('click', function() {
        doRequest('pages/deleteRevision', delRevision, { 'idRev': this.id.replace('del_', ''), 'id' : $('#pageId').attr('value') });
        return false;
    });
    $('#revisionTable .next a').bind('click', function() {
        doRequest('pages/useRevision', useRevision, { 'idRev': this.id.replace('roll_', ''), 'id' : $('#pageId').attr('value') });
        return false;
    });
}
runSpot = function(html) {
	$.fancybox(html);
	$('#cancelspot').bind('click', function(){
		$.fancybox.close();
	});
    doRequest('pages/spotscript', includescript, { 'spotId': $('#spotId').attr('value'), 'id': $('#pageId').attr('value') }, 'html');
}
delRevision = function(){
    getRevisionlist();
}
useRevision = function(page) {
	if(page.id != undefined && page.id != null){
		getMessage('userevision');
	}
	setPageValues(page);
}
saveDone = function (page) {
    if (showMessage) {
        getMessage(page);
        lockChecked = false;
    }
    showMessage = true;
    setPageValues(page);
    getPagemap();
    return false;
}
setPageValues = function (page) {
    lockChecked = false;
    setPageLockStatus(page, false);
    $('#pageId').attr('value', page.id);
    $('#pagename').attr('value', (page.title != null) ? page.title : '');
    $('span.pageheadingTitle').html((page.title != null) ? page.title : '');
    $('#dhtmlgoodies_tree2 a[rel=' + page.id + ']').text($('#pagename').val());
    $('#revision-pagename').text($('#pagename').val());
    $('#statusp').attr('value', (page.published != null) ? page.published : 'Unpublished');
    $('#keywords').attr('value', (page.keywords != null) ? page.keywords : '');
    $('#description').attr('value', (page.description != null) ? page.description : '');
    if (page.index) {
        $('#isIndexText').text(' - (index)');
        $('#setIndex').addClass('hidden');
    } else {
        $('#isIndexText').text('');
        $('#setIndex').removeClass('hidden');
    }
    getRevisionlist();
    setpubstatusBar(page);
}