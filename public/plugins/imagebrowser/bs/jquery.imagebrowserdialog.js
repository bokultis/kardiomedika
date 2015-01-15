/**
 * jQuery Image Browser Dialog
 * 
 */
include('/plugins/imagebrowser/bs/jquery.imagebrowser.js');
//include('/plugins/imagebrowser/bs/style.css');

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
                var dialogElement = $('\
    <div id="image_browser_dialog" class="modal fade container">\
          <div class="modal-header">\
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
            <h4 class="modal-title">' + _("Image Browser") + '</h4>\
          </div>\
          <div class="modal-body">\
            <div id="image_browser"></div>\
          </div>\
          <div id="image_browser_dialog_buttons" class="modal-footer">\
          </div>\
    </div>\
    ');                
                $("body").append(dialogElement);
            }
            
            dialogElement.find("#image_browser").imagebrowser({
                "initPath": initPath,
                "cropBox":  [opts.maxwidth,opts.maxheight],
                "minSize": [opts.minwidth,opts.minheight],
                "fileWebRoot":opts.fileWebRoot,
                "activeModule":opts.activeModule,
                "startingSlash":opts.startingSlash
            });

            //add buttons
            var dialogFooter = dialogElement.find('#image_browser_dialog_buttons.modal-footer');
            dialogFooter.html('');
            var selectButton = $('<button type="button" class="btn btn-primary">' + _("Select") + '</button>');
            selectButton.click(function(){
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
                        if(opts.absolutePath){
                            self.val($('base').attr('href') + selectedFile.path);
                        }
                        self.trigger("change");
                    }
                    if(opts.onSelect){
                        opts.onSelect(selectedFile.path);
                    }
                    dialogElement.modal("hide");
                }
            });
            var closeButton = $('<button type="button" class="btn btn-default" data-dismiss="modal">' + _("Close") + '</button>');
            closeButton.click(function(){
                dialogElement.modal("hide");
            });
            dialogFooter.append(selectButton);
            dialogFooter.append(closeButton);            
            dialogElement.modal("show");
        }

        // return the object back to the chained call flow
        return this.each(function()
        {
            self = $(this);
            self.data('opts',opts);
            if(opts.readonly){
                self.attr("readonly", "readonly");
            }
            

            //append browse link
            self.parent().find('button.browse').click(function(){
                var initPath = "";
                if(self.val()){
                    initPath = self.val();
                }
                openImageDialog(initPath);
                return false;
            });
            self.parent().find('button.clear').click(function(){
                self.val('');
                self.trigger("change");
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
        minheight: 0,
        readonly: true,
        absolutePath: false
    };
        
 
})(jQuery);   // pass the jQuery object to this function