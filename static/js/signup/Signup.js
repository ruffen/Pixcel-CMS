$(window).load(function () {
    $('#signupform').submit(function () {
        $.post(
            $(this).attr('action'),
            $(this).serialize(),
            function(data){
                $('#rightcol').html(data);
            }  
        );
        return false;  
    });

});