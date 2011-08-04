onload = function () {
    alert('test');
    tinyMCE.execCommand('mceAddControl', false, 'tinymce');
}
$('#ok').bind('click', function () {
    if (tinyMCE.getInstanceById('tinymce')) {
        tinyMCE.triggerSave();
        tinyMCE.get('tinymce').remove();
    }
    alert($('#tinymce').attr('value'));
    doRequest('pages/setspotvalue', function () { $('#id_textarea').remove(); }, { 'id': $('#pageId').attr('value'), 'spotId': $('#spotId').attr('value'), 'value': $('#tinymce').attr('value') });
    $('#cancelspot').click();
});
onload();