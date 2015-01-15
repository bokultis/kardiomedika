/**
 * jQuery Image Browser Dialog
 * 
 */
include('/plugins/imagebrowser/jquery.imagebrowser.js');
//include('/plugins/imagebrowser/style.css');

(function($)
{
    
    /**
     * main jquery plugin function
     */
    $.fn.imagebrowserdialog = function(options)
    {
        var opts = {};
        var self = null;
        var dialogElement = null;
        
        if(arguments.length == 2 && arguments[0] == 'update'){
            self = $(this);
            storeOptions(arguments[1]);
            return;
        }

        //create options
        opts = $.extend({}, $.fn.imagebrowserdialog.defaults, options);
        
        if(options.method == 'open'){
            self = $(this);
            storeOptions(options);
            return openImageDialog(options.initPath);
        }

        function storeOptions(options){
            opts = self.data('opts');
            opts = $.extend({}, opts, options);
            self.data('opts',opts);
        }
        
        function openImageDialog(initPath){
            opts = self.data('opts');
            dialogElement = $("#image_browser_dialog");

            if(dialogElement.length == 0){
                dialogElement = document.createElement('div');
                document.body.appendChild(dialogElement);
                $(dialogElement).attr({
                    'id'    : "image_browser_dialog",
                    'title' : 'Image Browser'
                });
                dialogElement = $("#image_browser_dialog");
                dialogElement.html('<div id="image_browser"></div>');
            }
            
            dialogElement.find("#image_browser").imagebrowser({
                "initPath": initPath,
                "cropBox":  [opts.maxwidth,opts.maxheight],
                "minSize": [opts.minwidth,opts.minheight],
                "fileWebRoot":opts.fileWebRoot,
                "activeModule":opts.activeModule,
                "startingSlash":opts.startingSlash
            });
            dialogElement.dialog({
                autoOpen: true,
                resizable: true,
                width:opts.dialogWidth,
                height:opts.dialogHeight,
                zIndex:(opts.zIndex)?opts.zIndex:null,
                modal: true,
                close: function(event, ui) {
                    //remove crop dialog if opened
                    $("#ibCropPreview").dialog('destroy');
                    $("#ibCropPreview").remove();
                },
                buttons: [
                    /*{
                        text: _("Crop"),
                        click: function() {
                            dialogElement.find(".ibToolbar button.crop").click();
                        }
                    },*/
                    {
                        text: _("Select"),
                        click: function() {
                            var selectedFile = dialogElement.find("#image_browser").imagebrowser('selected');
                            if(selectedFile == null || selectedFile.type != 'file' || selectedFile.path == null){
                                alert(_("Please select an image."));
                                return false;
                            }
                            else{
                                //validate selected file
                                if(opts.extensions){
                                    var extension = pathinfo(selectedFile.path,'PATHINFO_EXTENSION');
                                    extension = extension.toLowerCase();
                                    var extensions = opts.extensions.split(",");
                                    if(!in_array(extension, extensions)){
                                        alert(sprintf(_("Invalid file extension [%s], should be among: [%s]."),extension,opts.extensions));
                                        return false;
                                    }
                                }
                                if(opts.maxwidth && selectedFile.width > opts.maxwidth){
                                    alert(sprintf(_("Maximum image width is %dpx, but %dpx detected. Please CROP or RESIZE the image."),opts.maxwidth,selectedFile.width));
                                    return false;
                                }
                                if(opts.minwidth && selectedFile.width < opts.minwidth){
                                    alert(sprintf(_("Minimum image width is %dpx, but %dpx detected."),opts.minwidth,selectedFile.width));
                                    return false;
                                }
                                if(opts.maxheight && selectedFile.height > opts.maxheight){
                                    alert(sprintf(_("Maximum image height is %dpx, but %dpx detected. Please CROP or RESIZE the image."),opts.maxheight,selectedFile.height));
                                    return false;
                                }
                                if(opts.minheight && selectedFile.height < opts.minheight){
                                    alert(sprintf(_("Minimum image height is %dpx, but %dpx detected."),opts.minheight,selectedFile.height));
                                    return false;
                                }
                                if(self){
                                    self.val(selectedFile.path);
                                }
                                if(opts.onSelect){
                                    opts.onSelect(selectedFile.path);
                                }
                                dialogElement.dialog("close");
                            }
                        }
                    },
                    {
                        text: _("Close"),
                        click: function() {dialogElement.dialog("close");}
                    }
                ]
            });            
        }

        // return the object back to the chained call flow
        return this.each(function()
        {
            self = $(this);
            self.data('opts',opts);
            self.attr("readonly", "readonly");

            //append browse link
            $('<button class="btn_general" type="button">' + _('Browse') + '...</button>').insertAfter(self).click(function(){
                var initPath = "";
                if(self.val()){
                    initPath = self.val();
                }
                openImageDialog(initPath);
                return false;
            });

            applyPreview(this);
        });

        function applyPreview(inputBox){
            /* CONFIG */
            xOffset = 10;
            yOffset = 10;
            // these 2 variable determine popup's distance from the cursor
            // you might want to adjust to get the right result

            /* END CONFIG */
            $(inputBox).hover(function(e){
                if($(this).val() == ''){
                    return;
                }
                $("body").append('<div id="ibPreviewHover"><img src="' + $(this).val() + '" alt="Image preview" /></div>');
                $("#ibPreviewHover").css("top",(e.pageY + yOffset) + "px")
                                    .css("left",(e.pageX + xOffset) + "px")
                                    .fadeIn("fast");
            },
            function(){
                $("#ibPreviewHover").remove();
            });
        }
    };    

    /**
     * default options
     */
    $.fn.imagebrowserdialog.defaults =
    {
        dialogWidth: 720,
        dialogHeight: 525,
        startingSlash:false,
        maxwidth: 0,
        maxheight: 0,
        minwidth: 0,
        minheight: 0
    };
        
 
})(jQuery);   // pass the jQuery object to this function