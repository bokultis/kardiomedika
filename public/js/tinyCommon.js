/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var tiny = {
    conf:{
        init:{
            mode: "none",
            // Location of TinyMCE script
            script_url : '/plugins/tinymce/tiny_mce.js',
            // General options
            theme : "advanced",
            plugins : "advhr,advimage,advlink,inlinepopups,style,preview,media,table,searchreplace,print,contextmenu,paste,fullscreen,noneditable,nonbreaking,xhtmlxtras",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,bullist,numlist,outdent,indent,blockquote,|,cut,copy,paste,pastetext,pasteword",
            theme_advanced_buttons2 : "undo,redo,|,link,unlink,anchor,image,cleanup,code,|,hr,removeformat,|,media,advhr,charmap|,print,fullscreen,preview",
            theme_advanced_buttons3: "tablecontrols",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,
            //skin
            skin : "hfb",
            inlinepopups_skin: "hfb",
            // Example content CSS (should be your site CSS)
            content_css : "../../themes/cmstheme/css/tinyStyle.css",
            relative_urls : true,
            document_base_url : "/",
            file_browser_callback : 'tiny.fileBrowserPlugin',
            verify_html : false,
            inline_styles : true
        },
        dialog:{
            activeModule: null
        }
    },    
    init: function(textArea,fileWebRoot, activeModule, options){
        tiny.conf.init.document_base_url = fileWebRoot;
        tiny.conf.dialog.activeModule = activeModule;
        var opts = $.extend({}, tiny.conf.init, options);
        $(textArea).tinymce(opts);
    },
    fileBrowserPlugin: function(field_name, url, type, win){
        //Use CMS page dialog to link
        if(typeof HFBW != 'undefined' && HFBW && type == "file" && confirm(_("Would you like to link to a CMS page?"))){
            $.fn.pagedialog(HFBW.Manager.pages,{
                zIndex: 400000,
                onSelect: function(pageId){
                    // insert information now
                    win.document.getElementById(field_name).value = '/cms/index/index/cms_page_id/' + pageId;
                    return true;
                }
            });
        }
        //USE server browser dialog to link
        else{
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
        }
        
        return false;
        
    }

}


