onload = function() {
    $('.imagepicker .imagelist img').bind('click', function() {
        selectimage($(this).parents('tr').attr('id').replace('file_', ''));
        $('#cancelspot').click();
    });
}
selectimage = function(fileid) {
    doRequest('pages/setspotvalue', null, { 'id': $('#pageId').attr('value'), 'spotId': $('#spotId').attr('value'), 'value': fileid });
}
onload();