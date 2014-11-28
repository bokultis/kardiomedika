$(document).ready(function() {
    //flash messanger - parse static html
    $("#flashMessenger p").each(function(){
        $.flashMessenger($(this).text(),{clsName:$(this).attr('class')});
    });

    window.alert = function(str){
        $.flashMessenger(str,{
            autoClose:false,
            modal:true,
            clsName:"warn"
        });
    }
});


