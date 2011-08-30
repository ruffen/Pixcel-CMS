$(document).ready(function() {
    
    $('#createNewSite').bind('click', function(){
        $(this).animate({
            opacity : 0
        }, 1500);
        $(this).parent('.separator').animate({
            height: 40
          }, 2000);
        return false;
    });


    $('#topbarSitelist').addClass('hidden');
	$('#test_connection').bind('click', testconnection);
    $('#browseftp').bind('click', function(){
        addLoadingImageToElement($(this));
        doRequest($('#browseftp').attr('href'), function(html){
            removeLoadingImageInElement();
            $.fancybox(html, {onComplete : ftpbrowser });
        }, getFtpdetails(), 'html');
        return false;
    });
    $('#deletesite').bind('click', function(){
        getMessage('confirmdeletesite', function () {
            doRequest('sitemanagement/deletesite', function(){
                var id = $('#site_tree a').filter(':first').attr('id');
                getSite(id);
                getSitelist();
            });            
        });
        return false;
    });
    $('#save').bind('click', function(){
        var button = replaceButtonwithloading($(this));
    	var data = getFtpdetails();
    	data.name = $('#sitename').attr('value');
    	data.url = $('#siteurl').attr('value');
    	data.id = $('#siteId').attr('value');
		doRequest($(this).attr('href'), function(result){
            getMessage(result);
            $('input, select').removeClass('inputerror');
            if(isset(result.elements)){
                validate(result.elements);            
            }else{
                getSitelist();            
            }
		    $('#save').html(button);
		}, data);
		return false;
	});
    $('#cancel').bind('click', function(){
        var button = replaceButtonwithloading($(this));
        getSite($('#siteId').attr('value'), button);
        return false;
    });
	$('#newsite').bind('click', function(){
		$('#siteId').attr('value', 'new');
		$('input[type=text], input[type=password]').attr('value', '');
		$('#heading_sitename').text('New Site');
		$("#protocol").get(0).selectedIndex = 0;
		$('#pasv').attr('checked', true);
		$('#port').attr('value', 21);
		return false;
	});
	$('#sitename').bind('keyup', function(){
		$('#heading_sitename').text($('#sitename').attr('value'));
	});
	$('#protocol').bind('change', function(){
		if($(this).attr('value') == 'ftp'){
			$('#port').attr('value', 21);
		}else{
			$('#port').attr('value', 22);		
		}
	});
    getSiteevents();
});
getSiteevents = function(){
    $('#site_tree li a').unbind();
    $('#site_tree li a').bind('click', function(){
        getSite($(this).attr('id'));
        return false;
    });
}
getSite = function(siteId, cancelbutton){
    $('.forms').addClass('hidden');
    $('.loadingmain').removeClass('hidden');
    doRequest('sitemanagement/sitedetails', function(site){
        if(!isset(site.success)){
            getMessage(site);
            return false;
        }
        $('#siteId').attr('value', site.siteId);
        $('#sitename').attr('value', site.sitename);
        $('#heading_sitename').text($('#sitename').attr('value'));
        $('#url').attr('value', site.url);
		$('#username').attr('value', site.username);
        $('#password').attr('value', site.password);
        $('#ftp_url').attr('value', site.ftp_url);
        $('#root').attr('value', site.path);
        $('#port').attr('value', site.port);

        if(site.passive == 1){
            $('#pasv').attr('checked', true);
        }else{
            $('#pasv').attr('checked', false);
        }
        $('#protocol').attr('value', site.protocol);
        if(isset(cancelbutton)){
            $('#cancel').html(cancelbutton);
        }
        $('.forms').removeClass('hidden');
        $('.loadingmain').addClass('hidden');
    }, {'id' : siteId});
}
testconnection = function(){
   addLoadingImageToElement($('#test_connection'));
    
	$('#sitemanagement_form input').removeClass('error');
    var data = getFtpdetails();
    data.id = $('#siteId').attr('value');
	doRequest($(this).attr('href'), 
		function(result){
			if(isset(result.error) && result.error.search('missing') != -1){
                var field = result.error.replace('missing_', '');
                $('#' + field).addClass('error');
            }
            getMessage(result);
            removeLoadingImageInElement();
		}, data);
	return false;
}
getSitelist = function(){
    loadinggif($('#site_tree'));
	doRequest('sitemanagement/SiteList', function(sites){ 
        $('#site_tree').empty();
        for(var data in sites){
			createNewSiteListelement(sites[data]);
		}
        getSiteevents();	
	}, {});
}
createNewSiteListelement = function(element){
	var list = $("<li/>").appendTo('.file_tree');
	var anchor =$('<a id="'+element.id+'" href="/sitemanagement/sitedetails">' + element.name + '</a>').appendTo(list); 
}

ftpbrowser = function(){
	$('#ftp_up').bind('click', function(){
		data = getFtpdetails();
		data.root = $('#currentroot').attr('value') + '/../';
		sendFtp(data);	
	});
	$('#ftpbrowser li.directory a').bind('click', function(){
		data = getFtpdetails();
		data.root = $('#currentroot').attr('value') + '/' + $(this).text();
		sendFtp(data);
	});
	sendFtp = function(data){
		loadinggif($('#ftpbrowser_inner'));
        doRequest($('#browseftp').attr('href'), function(html){
			$('#ftpbrowser').replaceWith(html);
			ftpbrowser();
		}, data,'html');
	}
	$('#selectfolder').bind('click', function(){
		$('#root').attr('value', $('#currentroot').attr('value'));
		$.fancybox.close();
		return false;
	});
	$('#cancelbrowse').bind('click', function(){
		$.fancybox.close();
		return false;
	});
	return false;
}
getFtpdetails = function(){
	var data = {
		'ftp_url' : $('#ftp_url').attr('value'),
		'ftp_root' : $('#root').attr('value'),
		'ftp_username' : $('#username').attr('value'),
		'ftp_password' : $('#password').attr('value'),
		'ftp_mode' : $('#protocol').attr('value'),
		'ftp_passive': $('#protocol').attr('value'),
		'ftp_port': $('#port').attr('value')
	}
	data.passive = ($('#pasv').attr('checked')) ? 'on' : 'off';
	return data;	
}