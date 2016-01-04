$(document).ready(function() {
    $('.Default').find('input').attr('readonly', true);
    $('.Default').find('input').css({'font-style' : 'italic', 'border':'1px solid #e0e0e0'});
     
    //        $("#tabs").tabs();
    /*$('#tab-side-container').easytabs({
        animate: false,
        tabActiveClass: "selected-tab",
        panelActiveClass: "displayed"
    }); */
    $("#addNewToEmail").click(function(){
        var htmlStr  = "<td><input type='text' name=\"data[to_emails]["+ (parseInt($('.table_to_emails tr:last').index())+1) +"][name]\" value=\"\" /></td>";
        htmlStr += "<td><input type='text' name=\"data[to_emails]["+ (parseInt($('.table_to_emails tr:last').index())+1) +"][email]\" value=\"\" /></td>";
        $('.table_to_emails tbody tr:last').after('<tr>'+ htmlStr + '</tr>');
        return false;
    })
    $("#removeToEmail").click(function(){
        if($('.table_to_emails tr:last').index() > 1 ){
            $('.table_to_emails tr:last').remove();
        }
        return false;
    });
    /* extensions */
    $("#addExtensions").click(function(){
        var trIndex = parseInt($('.table_extensions tr:last').index())+1;
        var htmlStr  = "<td class='newExt'><input type='text' id=\"data["+trIndex+"][extensions]\" name=\"data[upload]["+ trIndex +"][extensions]\" value=\"\" />\n\
                            <span class=\"removeExtensions\" data-tr_index=\""+ trIndex +"\" ><i class='icon-close-big'></i> </span>    </td>";
        var content = "<tr id=\"tr_"+ trIndex +"\">"+ htmlStr + '</tr>';
        if(trIndex == 0){
            $('.table_extensions').append(content);
        }else{
            $('.table_extensions tr:last').after(content);
        }  
        return false;
    });
    
    
    $(".table_extensions").on("click", "span.removeExtensions", function(){  
        $('.table_extensions #tr_'+$(this).data('tr_index')).remove();
        
    });
    /* mimetypes */
    $("#addMimetypes").click(function(){
        var trIndex = parseInt($('.table_mimetypes tr:last').index())+1;
        var htmlStr  = "<td class='newMime'><input type='text' id=\"data["+trIndex+"][mimetypes]\" name=\"data[upload]["+ trIndex +"][mimetypes]\" value=\"\" />\n\
        <span  class=\"removeMimetypes\" data-tr_index=\""+ trIndex +"\" ><i class='icon-close-big'></i> </span>    </td>";
        var content = "<tr id=\"tr_"+ trIndex +"\">"+ htmlStr + '</tr>';
        if(trIndex == 0){
            $('.table_mimetypes').append(content);
        }else{
            $('.table_mimetypes tr:last').after(content);
        }
        return false;
    })
   
    
    
    $(".table_mimetypes").on("click", "span.removeMimetypes", function(){  
        $('.table_mimetypes #tr_'+$(this).data('tr_index')).remove();
    });
        
    /***************    *******/
        
    $(".submit").click(function(){
        var activeTab = $(".res-tabs").restabs("getActiveId");
        var activeTabContent = activeTab.replace("-tab-", "-article-");
        var data = $("#" + activeTabContent).find("form").serialize();
        var editUrl = $("#" + activeTabContent).find("form").attr('action') +"/tab/" + $("#" + activeTabContent).children("div").attr('id');
        saveConfig(editUrl, data);
        return false;
    });
    
    
    /*FB OG property setinngs */
    var fileInputImg = ("#data\\[image\\]");
    var opts = {};
    opts.fileWebRoot = window.fileWebRoot;
    opts.initPath = $(fileInputImg).val();
    opts.extensions = "gif,png,jpg,jpeg";
    opts.maxwidth = 400;
    opts.maxheight = 400;
    opts.minwidth = 200;
    opts.minheight = 200;
  
    $(fileInputImg).imagebrowserdialog(opts);
    $("#imageClear").click(function (e) {
        e.preventDefault();
        $('#data\\[image\\]').val("");
        return false;
    });
    
});
    
function saveConfig(editUrl, data){
    $(".error").remove();
    $.ajax({
        url: editUrl,
        type: "POST",
        data: data, 
        success: function(res){
            if(res.success){
                    $.flashMessenger(res.message,{clsName:"ok"});
            }else{
                var errors = {};
                ajaxForm.parseErrors('data',null,res['message'],errors);
                for(var field in errors){
                    var errorUl = '<ul class="error">';
                    for(var i = 0; i < errors[field].length; i++){
                        errorUl += '<li>' + errors[field][i] + '</li>';
                    }
                    errorUl += '</ul>';
                    $(ajaxForm.jqId(field)).parent().append(errorUl);
                }
            }
        }
    });
}


