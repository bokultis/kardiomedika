/**
 * jQuery File Browser Dialog
 * 
 */
include('/plugins/serverbrowser/jquery.serverbrowser.js');
include('/plugins/serverbrowser/style.css');

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
                dialogElement = document.createElement('div');
                document.body.appendChild(dialogElement);
                $(dialogElement).attr({
                    'id'    : "file_browser_dialog",
                    'title' : 'File Browser'
                });
                dialogElement = $("#file_browser_dialog");
                dialogElement.html('<div id="file_browser"></div>');
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
                'initPath':initPath,
                'startingSlash':opts.startingSlash,
                "fileWebRoot":opts.fileWebRoot
            };


            $("#file_browser").serverbrowser(options);

            dialogElement.dialog({
                autoOpen: true,
                resizable: true,
                width:opts.dialogWidth,
                height:opts.dialogHeight,
                zIndex:(opts.zIndex)?opts.zIndex:null,
                modal: true,
                close: function(event, ui) {
                },
                buttons: [
                    {
                        text: _("Select"),
                        click: function() {
                            var selectedFile = dialogElement.find("#file_browser").serverbrowser('selected');
                            if(selectedFile == null || selectedFile.path == null){
                                alert(_("Please select an file."));
                                return false;
                            }
                            //select folder
                            else if(opts.extensions == '/'){
                                if(selectedFile.type != 'dir'){
                                    alert(_("Please select a folder."));
                                    return false;
                                }                                
                            }
                            //select file
                            else{
                                if(selectedFile.type != 'file'){
                                    alert(_("Please select an file."));
                                    return false;
                                }
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
                            }
                            if(self){
                                self.val(selectedFile.path);
                            }
                            if(opts.onSelect){
                                opts.onSelect(selectedFile.path);
                            }
                            dialogElement.dialog("close");                            
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
            self.attr("readonly", "readonly");

            //append browse link
            $('<button class="btn_general">' + _('Browse') + '...</button>').insertAfter(self).click(function(){
                var initPath = "";
                if(self.val() != ''){
                    initPath = self.val();
                }
                else{
                    initPath = options.initPath;
                }
                openFileDialog(initPath);
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