$(document).ready(function () {
    setSaveEvents();
    $('#editStructure').bind('click', function () {
        if ($(this).attr('rel') == 'false') {
            getUsermenu('true');
            $(this).attr('rel', 'true');
            $('#editstructure_buttons').removeClass('hidden');
        } else {
            getUsermenu();
            $(this).attr('rel', 'false');
            $('#editstructure_buttons').addClass('hidden');
        }
        return false;
    });
    $('#user_structure_cancel').bind('click', function () {
        getUsermenu();
        $('#editSitemap').attr('rel', 'false');
        $('#editStructure').attr('rel', 'false');
        $('#editstructure_buttons').addClass('hidden');

        return false;
    });
    $('#user_structure_submit').bind('click', function () {
        postData = $('#dhtmlgoodies_tree2').serializeTree("rel", "users");
        doRequest('users/saveusermenu', function (result) {
            getMessage(result);
            getUsermenu();
            $(this).attr('rel', 'false');
            $('#editstructure_buttons').addClass('hidden');
        }, postData);
        return false;
    });
    $('#createGroup').bind('click', function () {
        getGroup('new');
    });
    $('#deleteGroup').bind('click', function () {
        getMessage('confirmdeletegroup', function () {
            doRequest('users/deletegroup', function (result) {
                getUsermenu();
                getMessage(result);
                if (isset(result.success)) {
                    getGroup('');
                }
            }, { 'id': $('#groupId').attr('value') });
        });
    });
    $('#createUser').bind('click', function () {
        getUser('new');
    });
    $('#deleteUser').bind('click', function () {
        getMessage('confirmdeleteuser', function () {
            doRequest('users/deleteUser', function (result) {
                getUsermenu();
                getMessage(result);
                if (isset(result.success)) {
                    getUser('');
                }
            }, { 'id': $('#userId').attr('value') });
        });
    });
});
setSaveEvents = function () {
    if ($('#dhtmlgoodies_tree2 img').length == 0) {
        treeObj = new JSDragDropTree();
        treeObj.setTreeId('dhtmlgoodies_tree2');
        treeObj.setMaximumDepth(2);
        treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
        treeObj.initTree();
        treeObj.expandAll();
    }
    $('#save_group').unbind();
    $('#save_group').bind('click', function () {
        var oldElement = replaceButtonwithloading($(this));
        saveGroup(oldElement);
        return false;
    });
    $('#cancel_group').unbind();
    $('#cancel_group').bind('click', function () {
        var oldElement = replaceButtonwithloading($(this));
        if ($('#groupId').attr('value') == 'new') {
            $('#groupId').attr('value', '');
        }
        getGroup($('#groupId').attr('value'), oldElement);
        return false;
    });
    $('#save_user').unbind();
    $('#save_user').bind('click', function () {
        var element = replaceButtonwithloading($(this));
        saveUser(element);
        return false;
    });
    $('#cancel_user').unbind();
    $('#cancel_user').bind('click', function () {
        var oldElement = replaceButtonwithloading($(this));
        if ($('#userId').attr('value') == 'new') {
            $('#userId').attr('value', '');
        }
        getUser($('#userId').attr('value'), oldElement);
        return false;
    });
    $('#groupname').unbind();
    $('#groupname').bind('keyup', function () {
        $('.groupname').text($(this).attr('value'));
    });
    $('.file_tree li a').unbind();
    $('.file_tree li a').bind('click', function () {
        if ($(this).attr('rel').search('group_') != -1) {
            getGroup($(this).attr('rel').replace('group_', ''));
        } else {
            getUser($(this).attr('rel').replace('user_', ''));
        }
        return false;
    });
}
getGroup = function (groupId, element) {
    $('.usermenu').addClass('hidden');
    $('.groupmenu').removeClass('hidden');
    loadinggif($('.groupmenu'));
    doRequest('users/groupprofile', function (profile) {
        $('#rightcol').html(profile);
        $('input:checkbox').checkbox();
        setSaveEvents();
        $('#cancel_group').html(element);
    }, { 'id': groupId }, 'html');
}
getUser = function (userId, element) {
    $('.usermenu').removeClass('hidden');
    $('.groupmenu').addClass('hidden');
    doRequest('users/userprofile', function (profile) {
        $('#rightcol').html(profile);
        $('input:checkbox').checkbox();
        $('#firstname,#lastname').bind('keyup', function () {
            $('.' + $(this).attr('id')).text($(this).attr('value'));
        });
        setSaveEvents();
        if (isset(element)) {
            $('#cancel_user').html(element);
        }
    }, { 'id': userId }, 'html');
}
getUsermenu = function (drag) {
    drag = (isset(drag)) ? drag : '';
    doRequest('users/UsersMenu', function (list) {
        $('.file_tree').html(list);
        setSaveEvents();
    }, { 'drag': drag }, 'html');
}
saveGroup = function (element) {
    var data = {
        'name': $('#groupname').attr('value'),
        'id': $('#groupId').attr('value')
    };
    $('#moduletable tr input[type=checkbox]').each(function (index, element) {
        data[$(element).attr('id')] = $(element).attr('checked');
    });

    doRequest('users/savegroup', function (result) {
        getMessage(result);
        getUsermenu();
        $('#groupId').attr('value', result.id);
        $('#save_group').html(element);
    }, data);

}
saveUser = function (element) {
    var user = {};
    user.group = $('#group').attr('value');
    user.firstname = $('#firstname').attr('value');
    user.lastname = $('#lastname').attr('value');
    user.email = $('#email').attr('value');
    user.altered_username = $('#username').attr('value');
    user.altered_password = $('#password').attr('value');
    user.altered_password_repeat = $('#password_repeat').attr('value');
    user.active = $('#active').attr('checked');
    user.id = $('#userId').attr('value');
    $('input, select').removeClass('inputerror');
    doRequest('users/saveuser', function (result) {
        if (isset(result.elements)) {
            validate(result.elements);
        }
        getMessage(result);
        if (isset(result.id)) {
            getUsermenu();
            $('#userId').attr('value', result.id);
        }
        $('#save_user').html(element);
    }, user);
}
