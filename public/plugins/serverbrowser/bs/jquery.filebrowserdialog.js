/**
 * jQuery File Browser Dialog
 * 
 */
include('/plugins/serverbrowser/bs/jquery.serverbrowser.js');
include('/plugins/serverbrowser/bs/style.css');

(function($)
{
    
    /**
     * main jquery plugin function
     */
    $.fn.filebrowserdialog = function(options)
    {
        var opts = {};
        var self = null;
        var dialogElement = null;
        
        //create options
        opts = $.extend({}, $.fn.filebrowserdialog.defaults, options);
        
        if(options.method == 'open'){
            return openFileDialog(options.initPath);
        }
        
        function openFileDialog(initPath){
            dialogElement = $("#file_browser_dialog");

            if(dialogElement.length == 0){
                var dialogElement = $('\
<div id="file_browser_dialog" class="modal fade">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
        <h4 class="modal-title">' + _("File Browser") + '</h4>\
      </div>\
      <div class="modal-body">\
        <div id="file_browser"></div>\
      </div>\
      <div class="modal-footer">\
        <button id="selectButton" type="button" class="btn btn-primary">' + _("Select") + '</button>\
        <button id="closeButton" type="button" data-dismiss="modal" class="btn btn-default">' + _("Close") + '</button>\
      </div>\
</div>\
');
                $("body").append(dialogElement);
            }
            var options = {
                'selectableOptions':{
                    selected: function(event, ui){
                        var selectedFile = self.find("#file_browser").serverbrowser('selected');
                    },
                    unselected: function(event, ui){
                    }
                },
                'activeModule': opts.activeModule,
                'initPath':opts.initPath,
                'startingSlash':opts.startingSlash,
                "fileWebRoot":opts.fileWebRoot
            };


            $("#file_browser").serverbrowser(options);

            dialogElement.find("#selectButton").click(function(){
                var selectedFile = dialogElement.find("#file_browser").serverbrowser('selected');
                if(selectedFile == null || selectedFile.type != 'file' || selectedFile.path == null){
                    alert(_("Please select an file."));
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
                    if(self){
                        self.val(selectedFile.path);
                        self.trigger("change");
                    }
                    if(opts.onSelect){
                        opts.onSelect(selectedFile.path);
                    }
                    dialogElement.modal("hide");
                }
            });

            dialogElement.modal('show');
        }

        // return the object back to the chained call flow
        return this.each(function()
        {
            self = $(this);
            self.attr("readonly", "readonly");

            //append browse link
            self.parent().find('button.browse').click(function(){
                var initPath = "";
                if(self.val()){
                    initPath = self.val();
                }
                openFileDialog(initPath);
                return false;
            });
            self.parent().find('button.clear').click(function(){
                self.val('');
                self.trigger("change");
                return false;
            });          
        });
    };    

    /**
     * default options
     */
    $.fn.filebrowserdialog.defaults =
    {
        dialogWidth: 720,
        dialogHeight: 525,
        startingSlash:false
    };
        
 
})(jQuery);   // pass the jQuery object to this function