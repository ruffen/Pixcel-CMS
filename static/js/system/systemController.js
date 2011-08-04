$(document).ready(function() {
    $('#fileSubmit').bind('click', function() {
        ajaxFileUpload(setText);
        return false;
    });
    $('#maketemplate').bind('click', function() {
        makeTemplate();
        return false;
    });
    $('#saveTemplate').bind('click', function() {
        if ($('#assetlist ul li.error').length > 0) {
            alert('All assets need to be valid');
        } else {
            saveTemplate();
        }
        return false;
    });
});
saveTemplate = function() {
    var spotArray = new Array();
    $('#assetlist ul li').each(function(index, element) {
        spotArray[index] = $(element).html();
    });
    $.ajax({
        type: "POST",
        url: '?rt=system/savetemplate',
        data: ({
            'name': $('#templateName').attr('value'),
            'path': $('#filename').text(),
            'spots[]' : spotArray
        }),
        dataType: 'json',
        success: function(result) {

        }
    });
}
makeTemplate = function(){
    if ($('#filename').text() == '') {
        alert('no filename');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "?rt=files/maketpl",
        data: ({
            'path': $('#filename').text(),
            'name': $('#templateName').attr('value')
        }),
        dataType: 'json',
        success: function(template) {
            $('#templateName').attr('value', template.name);
            $.each(template.spots, function(index, spot) {
                if (typeof (spot.name) != 'undefined') {
                    var list = $('<li class="error" id="spot_' + index + '>' + spot.name + '</li>');
                    $('#assetlist ul').append(list);
                } else {
                    getSpot(spot.id, index);
                }
            });
            var orderedList = $('#assetlist ul li').sort(sortId);
            var orderedList = $('#assetlist ul li').sort(sortId);
            $('#assetlist ul').html(orderedList);
        },
        error : function(data){
            alert(data.responseText);
        }
    });    
}
getSpot = function(spotId, index) {
    $.ajax({
        type : 'POST',
        url : '?rt=system/getspot',
        data : ({
            'spotId' : spotId
        }),
        dataType : 'json',
        success : function(spot){
            var list = $('<li class="spot" id="spot_' + index + '>' + spot.name + '</li>');
            $('#assetlist ul').append(list);
            var orderedList = $('#assetlist ul li').sort(sortId);
            $('#assetlist ul').html(orderedList);
        },
        error : function(data){
            alert(data.responseText);
        }
    });
}
setText = function(data) {
    $('#filename').text(data.filepath);
}
function ajaxFileUpload(doneFunction){
    $.ajaxFileUpload({
        url: '?rt=files/upload',
        secureuri: false,
        fileElementId: 'file',
        dataType: 'json',
        success: function(data, status) {
			if (typeof (data.error) != 'undefined') {
			    if (data.error != '') {
                } else {
                    doneFunction(data);
                }
            }else{
                doneFunction(data);
            }
        },
        error: function(data, status, e) {
            alert(e);
        }
    });
	return false;
}  