$(document).ready(function(){
    
    var tinyOptions = {
        selector: "div.editable",
        inline: true,
        relative_urls : true,
        convert_urls: false,
        document_base_url : "/",
        image_dimensions: false,
        verify_html : false,
        inline_styles : true,
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste template codemirror"
        ],
        menubar: "edit format insert table tools",
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify  | bullist numlist outdent indent | link image",
        codemirror: {
            path: 'codemirror-4.8'
        },
                  
        content_css : ["modules/cms/css/tinyFrontStyle.css", "../../themes/"+ theme +"/css/tinyStyle.css"],
        file_browser_callback : function(field_name, url, type, win){
                $(win.document.getElementById(field_name)).imagebrowserdialog({
                    method:         "open",
                    initPath:       win.document.getElementById(field_name).value,
                    activeModule:   'cms',
                    startingSlash:  false,
                    zIndex:         400000,
                    fileWebRoot:    fileWebRoot,
                    dialogWidth: 'auto',
                    dialogHeight: 'auto',
                    onSelect: function(path){
                        // insert information now
                        win.document.getElementById(field_name).value = path;
                        // are we an image browser
                        if (typeof(win.ImageDialog) != "undefined") {
                            // we are, so update image dimensions...
                            if (win.ImageDialog.getImageData)
                                win.ImageDialog.getImageData();
                            // ... and preview if necessary
                            if (win.ImageDialog.showPreviewImage)
                                win.ImageDialog.showPreviewImage(path);
                        }

                    }
                });
            }         
    };
    
    if(tinyOptionsFrontExtension !== 'undefined'){
        tinyOptions = $.extend({}, tinyOptions, tinyOptionsFrontExtension);
    }
    
    tinymce.init(tinyOptions);
    
    //exit editing
    $('#front-admin-btn-exit').click(function(){
        $.post('/' + CURR_LANG + '/cms/admin-front/mode/', {
            'enabled' : '0'
        }, function(data){
            if(data.success){
                location.reload();
            }
            else{
                alert('Unknown Error');
            }
        });        
    });
    
    //save page
    $('#front-admin-btn-save').click(function(){
        $("div.editable").each(function(){
            $.post('/' + CURR_LANG + '/cms/admin-front/save/', {
                'id' : $(this).data('id'),
                'lang' : CURR_LANG,
                'content': tinymce.get($(this).attr('id')).getContent()
            }, function(data){
                if(data.success){
                    $.flashMessenger("Edits successfully saved",{clsName:"ok"});
                    
                }
                else{
                    alert('Unknown Error');
                }
            });              
        });      
    });  
    
    //unrevealed collapsible
    $('div.editable').click(function(){
        $(".collapsibleSection").each(function(){
            $(this).children(".row").removeClass("closed").addClass("open");   
        });  
    });
    
});

