/*----HORISEN FORM----*/

$(document).ready(function(){
    $('#captchaRefresh').click(function(){
        $.get('/' + CURR_LANG + '/contact/index/captcha-reload',function(data){
            $('#codeCaptcha').html(data.html);
            $('#captchaId').val(data.id);
        });
        return false;
    });    
});