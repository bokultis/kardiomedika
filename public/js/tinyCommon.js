/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var tiny = {
    conf:{
        init:{
            mode: "none",
            // Location of TinyMCE script
            script_url : '/plugins/tinymce/tinymce.min.js',
            // General options
            theme : "modern",
            plugins: [
                     "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                     "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                     "save table contextmenu directionality emoticons template paste textcolor"
               ],
            toolbar: "insertfile undo redo  | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",            
                       
            menubar : true,
            
            //skin
            //skin : "hfb",
            //inlinepopups_skin: "hfb",
            
            // Example content CSS (should be your site CSS)
            content_css : "../../themes/genesis/css/tinyStyle.css",
            relative_urls : true,
            document_base_url : "/",
            
            file_browser_callback : function(field_name, url, type, win){
                $(win.document.getElementById(field_name)).imagebrowserdialog({
                    method:         "open",
                    initPath:       win.document.getElementById(field_name).value,
                    activeModule:   tiny.conf.dialog.activeModule,
                    startingSlash:  false,
                    zIndex:         400000,
                    fileWebRoot:    tiny.conf.init.document_base_url,
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
            },
            
            verify_html : false,
            inline_styles : true
        },
        dialog:{
            activeModule: null
        }
    },    
    init: function(textArea,fileWebRoot, activeModule, options){
        tiny.conf.init.document_base_url = fileWebRoot;
        tiny.conf.init.selector = textArea;
        tiny.conf.dialog.activeModule = activeModule;
        var opts = $.extend({}, tiny.conf.init, options);
        
        $(textArea).tinymce(opts);
        
    }      
    
};


