/**
 * jQuery Server (File) Browser
 * 
 */
(function($)
{
    include('/js/json2.js');
    include('/js/jquery.zend.jsonrpc.js');    
    include('/js/aim.js');
    include('/plugins/contextMenu/jquery.contextMenu.js');
    include('/plugins/contextMenu/jquery.contextMenu.css');

    /**
     * main jquery plugin function
     */
    $.fn.serverbrowser = function(options)
    {
        var json = {};
        
        var opts = {};
        var uploadDialog = null;
        var self = $(this);
        
        if(options == 'selected'){
            var selFile = getSelected();
            if(!opts.startingSlash && selFile && selFile.path.substr(0,1) == '/'){
                selFile.path = selFile.path.substr(1);
            }
            return selFile;
        }
        if(options == 'reload'){
            opts = self.data('opts');
            initJsonRpc();
            return reload();
        }
        //create options
        opts = $.extend({}, $.fn.serverbrowser.defaults, options);

        // return the object back to the chained call flow
        return this.each(function()
        {
            var self = $(this);
            self.data('opts',opts);
            $.loadTpl(opts.template,function(tplStr){
                self.html($.tmpl(tplStr,{}));
                //buttons

                //UP
                self.find(".sbToolbar button.up").text(_("Move Up")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_up"
                    }
                })
                .click(function(){
                    enterDir('..');
                    $(this).blur();
                    return false;
                });

                //NEW
                self.find(".sbToolbar button.new").text(_("New Folder")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_new_folder"
                    }
                })
                .click(function(){
                    $(this).blur();
                    $.fn.prompt({
                        title: _("Prompt"),
                        value: _("New Directory"),
                        label: _("Please enter directory name"),
                        onClose: function(newDir){
                            if (newDir == null || newDir == ""){
                                return false;
                            }
                            json.mkdir(self.data('currPath'), newDir, {
                                'success':function(data){
                                    reload();
                                    $.flashMessenger(_("Directory created."));
                                },
                                'error':function (self,req,stat,err,id,key){
                                    $.flashMessenger(err,{clsName:"err"});
                                }
                            });
                        }
                    });
                    return false;
                });

                //RENAME
                self.find(".sbToolbar button.rename").text(_("Rename")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_rename"
                    }
                })
                .click(function(){
                    $(this).blur();
                    var selectedObj = getSelected();
                    if(!selectedObj){
                        $.flashMessenger(_("Please select a file or a directory!"),{clsName:"warn"});
                        return false;
                    }
                    var oldName = '';
                    if(selectedObj.type == 'dir'){
                        oldName = basename(selectedObj.path);
                    }
                    else{
                        oldName = pathinfo(selectedObj.path,'PATHINFO_FILENAME');
                    }

                    $.fn.prompt({
                        title: _("Prompt"),
                        value: oldName,
                        label: _("Please enter new name"),
                        onClose: function(newName){
                            if (newName == null || newName == ""){
                                return false;
                            }

                            json.rename(selectedObj.path,newName,{
                                'success':function(data){
                                    reload();
                                    $.flashMessenger(_("Item Renamed"));
                                },
                                'error':function (self,req,stat,err,id,key){
                                    $.flashMessenger(err,{clsName:"err"});
                                }
                            });
                        }
                    });
                });

                //DELETE
                self.find(".sbToolbar button.delete").text(_("Delete")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_delete"
                    }
                })
                .click(function(){
                    $(this).blur();
                    if (confirm(_('Are you sure you want to delete this item ?'))){
                        var selectedObj = getSelected();
                        if(selectedObj){
                            if(selectedObj.type == "file"){
                                json.deleteFile(selectedObj.path,{
                                    'success':function(data){
                                        reload();
                                        $.flashMessenger(_("File deleted."));
                                    },
                                    'error':function (self,req,stat,err,id,key){
                                        $.flashMessenger(err,{clsName:"err"});
                                    }
                                })
                            }else{
                                json.rmdir(selectedObj.path,{
                                    'success':function(data){
                                        reload();
                                        $.flashMessenger(_("Directory deleted"));
                                    },
                                    'error':function (self,req,stat,err,id,key){
                                        $.flashMessenger(err,{clsName:"err"});
                                    }
                                })
                            }
                        }else{
                            $.flashMessenger(_("Please select a file or a directory!"),{clsName:"warn"});
                        }
                        return false;
                    }else{
                       return false;
                    }
                });

                //UPLOAD
                self.find(".sbToolbar button.upload").text(_("Upload")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_upload"
                    }
                })
                .click(function(){
                    $(this).blur();
                    jQuery('#file_upload').val("");
                    uploadDialog = getUploadDialog();
                    uploadDialogReset();
                    uploadDialog.find("#data\\[dir\\]").val(self.data('currPath'));
                    uploadDialog.dialog({
                        autoOpen: true,
                        resizable: true,
                        width:400,
                        height:'auto',
                        modal: true,
                        close: function(ev,ui){
                            $(this).dialog('destroy');
                        },
                        buttons: [
                            {
                                text: "Upload",
                                click: function() {
                                    //trigger submit
                                    getUploadDialog().find("form").submit();
                                    return false;
                                }
                            },
                            {
                                text: "Close",
                                click: function() {$(this).dialog("close");}
                            }
                        ]
                    });
                    return false;
                });
               
                //selectable
                //self.find(".sbList").selectable(opts.selectableOptions);

                //init json rpc
                initJsonRpc();

                if(opts.initPath != null && opts.initPath != ''){
                    if(!opts.startingSlash && opts.initPath.substr(0, 1) != "/"){
                        opts.initPath = '/' + opts.initPath;
                    }
                    enterDir(null,dirname(opts.initPath), basename(opts.initPath));
                }
                else{
                    //enter root folder
                    reload();
                }
            }); 
        });

        function initJsonRpc(){
            
            //init json rpc
            json = jQuery.Zend.jsonrpc({
                'url': sessionUrl(opts.serverUrl + '/active_module/' + opts.activeModule),
                'async':true
            });
        }

        function reload(){
            var selected = getSelectedObj();
            if(selected != null && selected.data("path")){
                enterDir('.','',selected.data("path"));
            }
            else{
                enterDir('.');
            }

        }

        /**
         * Enters new folder and updates browser
         */
        function enterDir(directory,absPath,selectFile){
            var newDir = '';
            var currPath = self.data('currPath');
            if(currPath == null){
                currPath = "/";
            }
            if(directory != null){
                if(directory == '..'){
                    newDir = dirname(currPath);
                    if(newDir == ''){
                        newDir = '/';
                    }
                }
                else if(directory == '.'){
                    newDir = currPath;
                }
                else if(currPath == '/'){
                    newDir = currPath + directory;
                }
                else{
                    newDir = rtrim(currPath, '/') + '/' + directory;
                }
            }
            else{
                newDir = absPath;
            }
            json.listing(newDir,{
                'success':function(data){
                    self.data('currPath',newDir);
                    updateFileList(data, selectFile);
                    updateBreadCrumbs();
                    updateStats();
                },
                'error':function (self,req,stat,err,id,key){
                    alert(err);
                }
            })
        }

        function updateStats(){
            json.stats({
                'success':function(data){
                    var free = $.formatFileSize(data.free);
                    var quota = $.formatFileSize(data.quota);
                    var used = $.formatFileSize(data.used);
                    var statusText = [];
                    statusText.push(_("Total") + ": " + quota);
                    statusText.push(_("Used") + ": " + used);
                    statusText.push(_("Free") + ": " + free);
                    self.find('.statusPanel').html(statusText.join(" | "));
                },
                'error':function (self,req,stat,err,id,key){
                    alert(err);
                }
            })
        }

        function updateBreadCrumbs(){
            var currPath = self.data('currPath');
            var parts = currPath.split("/");
            var currDir = "/";
            var html = '<ul><li data-path="' + currDir + '">Start</li>';
            for(var i = 0; i < parts.length; i++){
                if(!parts[i] || parts[i] == ''){
                    continue;
                }
                if(currDir != '/'){
                    currDir += '/';
                }
                currDir += parts[i];
                html += '<li data-path="' + currDir + '">' + parts[i] + '</li>';
            }
            html += '</ul>';
            self.find('.sbBreadcrumb').html(html);
            self.find('.sbBreadcrumb li').click(function(){
                var path = $(this).data('path');
                enterDir(null,path);
            });
        }

        /**
         * Updates file list
         */
        function updateFileList(data,selectFile){
            if(opts.selectableOptions.unselected){
                opts.selectableOptions.unselected();
            }
            var items = '';
            var className = 'file';
            var fileSize = '';
            var ext = '';
            for(var file in data){
                if(data[file].type == 'dir'){
                    className = 'file dir';
                    fileSize = '';
                }
                else{
                    className = 'file';
                    ext = pathinfo(file, 'PATHINFO_EXTENSION');
                    ext = ext.toLowerCase();
                    if(ext == 'jpg' || ext == 'png' || ext == 'gif' || ext == 'jpeg'){
                        className += ' image';
                    }
                    fileSize = '<span class="fileSize">' +  $.formatFileSize(data[file].size) + '</span>'

                }
                var fullName = ltrim(file,'./');
                var fileNameHtml = '<abbr title="' + fullName + '">' + fullName  + '</abbr>';
                items += '<li class="' + className + ' clearfix" data-path="' + htmlspecialchars(file) + '">' + fileNameHtml + fileSize + '</li>';
            }
            
            
            self.find('.sbList').html(items);
            self.find('.sbList li').each(function(){
                var path = $(this).data('path');
                var fileDesc = data[path];
                $(this).data("fileDesc", fileDesc);
                //select on click
                $(this).click(function(){
                    if(opts.selectableOptions.unselected){
                        opts.selectableOptions.unselected(null,self.find('.sbList li.ui-selected'));
                    }
                    self.find('.sbList li').removeClass("ui-selected");
                    $(this).addClass("ui-selected");
                    if(opts.selectableOptions.selected){
                        opts.selectableOptions.selected(null,$(this));
                    }
                });
                //enter dir on double click
                if(fileDesc['type'] == 'dir'){
                    $(this).dblclick(function(){
                        enterDir(path);
                    });
                }
                //select default file
                if(selectFile && $(this).data("path") == selectFile){
                    $(this).addClass('ui-selected');
                    //opts.selectableOptions.selected(null,$(this));
                }
            })
            bindContextMenu(self.find('.sbList'));
            bindContextMenu(self.find('.sbList li'));
        }
      
        function bindContextMenu(elem){
            elem.destroyContextMenu() // unbind first, to prevent duplicates
            .contextMenu({
                    menu: 'sbMenu'
                },
                function(action, el, pos) {
                    var currPath =  self.data('currPath');
                    var path = (el.data('path'))?((currPath != "/")?currPath :"") + "/" + el.data('path'):self.data('currPath');
                    var type = (el.hasClass('dir'))?'dir':(el.hasClass('file'))?'file':'dir';
                    switch( action ) {
                        case "cut":
                        case "copy":
                        case "paste":
                            copyPaste(action, path, type);
                            break;
                        case "open":
                            var link = $("base").attr("href") + self.data('currPath') + '/' + el.data("path");
                            window.open(link, "_blank");
                            break;
                         case "unzip":
                            json.unzip(path,{
                                'success':function(data){
                                    reload();
                                },
                                'error':function (self,req,stat,err,id,key){
                                    alert(err);
                                }
                            });
                            return false;
                            break;
                        default:
                        break;  
                    }
                 }
            ); 
        }
        
        function getSelected(){
            var selObject = getSelectedObj();
            var currPath = self.data('currPath');
            if(selObject){
                return {
                    path: ((currPath != "/")?currPath :"") + "/" + selObject.data('path'),
                    type: (selObject.hasClass('dir'))?'dir':'file'
                }
            }
            else{
                return null;
            }
        }

        function getSelectedObj(){
            var selObject = self.find(".sbList li.ui-selected");
            if(selObject.data('path')){
                return selObject;
            }
            else{
                return null;
            }
        }

        function uploadDialogAddFile(){
            var index = uploadDialog.find("form input:file").length;
            $('<input type="file" name="file_upload[]" />').appendTo(uploadDialog.find("form"));
        }

        function uploadDialogClearFiles(){
           uploadDialog.find("form input:file").remove();
        }

        function uploadDialogReset(){
            //reset files
            uploadDialogClearFiles();
            uploadDialogAddFile();
        }

        function getUploadDialog(){
            if(uploadDialog == null){
                uploadDialog = $("#sbUploadDialog");
                uploadDialog.find("form").attr("action", sessionUrl('/' + CURR_LANG + '/admin/file-server/upload/active_module/' + opts.activeModule));
                //add file action
                uploadDialog.find("#sbUploadAdd").click(function(){
                    uploadDialogAddFile();
                    return false;
                });
                //assign onsubmit
                $(uploadDialog).find('form').submit(function(){
                    return AIM.submit(this,{
                        'onStart' : function(){
                            ajaxLoader.show();
                            $(".error").remove();
                            return true;
                        },
                        'onComplete' : function(response){
                            ajaxLoader.hide();
                            $(uploadDialog).dialog("close");
                            eval("var result = " + response + ";");
                            if(result.success){
                                reload();
                                if(result.message){
                                    $.flashMessenger(_("Some files uploaded.") + " " + result.message,{clsName:"err"});
                                }
                                else{
                                    $.flashMessenger(_("File uploaded"));
                                }
                            }
                            else{
                                $.flashMessenger(result.message,{clsName:"err"});
                            }                            
                        }}
                    );
                });
            }
            return $(uploadDialog);
        }

        function getExtension(value){
            var parts = value.split('.');
            return parts[parts.length-1];

        }

        function getFileName(value){
            var parts = value.split('.');
            return parts[parts.length-2];
        }

        function parseErrors(currentElementId, errorName, currentMessage, errors){
            var messageType = typeof currentMessage;

            if(messageType != 'array' && messageType != 'object'){
                if(errors[currentElementId] == null){
                    errors[currentElementId] = new Array(0);
                }
                var currLen = errors[currentElementId].length;
                errors[currentElementId].length = currLen + 1;
                errors[currentElementId][currLen] = currentMessage;
                return;
            }

            for(var field in currentMessage){
                if(errorName){
                    currentElementId = currentElementId + '[' + errorName + ']';
                }
                parseErrors(currentElementId, field, currentMessage[field],errors);
            }
        }
        var clipboardPath = null;
        var pasteMode = null;
        var clipboardType = null;
        function copyPaste(action, path, type) {
            switch( action ) {
            case "cut":
            case "copy":
                clipboardPath = path;
                pasteMode = action;
                clipboardType = type;
            break;
            case "paste":
                if( !clipboardPath ) {
                    alert("Clipoard is empty.");
                    break;
                }
                $.ajax({
                  type: "POST",
                  url: sessionUrl('/' + CURR_LANG + '/admin/file-server/paste/active_module/' + opts.activeModule),
                  data: "clipboardPath="+clipboardPath+"&path="+path+"&type="+ type + "&clipboardType="+ clipboardType
                }).done(function( result ) {
                    if(result.success){
                        reload();
                        $.flashMessenger(result.message,{clsName:"ok"});
                    }
                    else{
                        $.flashMessenger( result.message,{clsName:"err"});
                    }
                });
              break;
            default:
                alert("'" + action + "'action is not defined" );
            break;
            }
        };

        /**
        * return escaped element id
        */
        function jqId(myid) {
            return '#' + myid.replace(/(:|\[|\]|\.)/g,'\\$1');
        }
    };
    
    var cache = {};
    var tpls = {};
    
    $.tmpl = function(str, data){
        // Figure out if we're getting a template, or if we need to
        // load the template - and be sure to cache the result.
        var fn = !/\W/.test(str) ?
        cache[str] = cache[str] ||
        $.tmpl(document.getElementById(str).innerHTML) :

        // Generate a reusable function that will serve as a template
        // generator (and which will be cached).
        new Function("obj",
            "var p=[],print=function(){p.push.apply(p,arguments);};" +

            // Introduce the data as local variables using with(){}
            "with(obj){p.push('" +

            // Convert the template into pure JavaScript
            str
            .replace(/[\r\t\n]/g, " ")
            .split("<%").join("\t")
            .replace(/((^|%>)[^\t]*)'/g, "$1\r")
            .replace(/\t=(.*?)%>/g, "',$1,'")
            .split("\t").join("');")
            .split("%>").join("p.push('")
            .split("\r").join("\\'")
            + "');}return p.join('');");

        // Provide some basic currying to the user
        return data ? fn( data ) : fn;
    };

    $.loadTpl = function(url,callback){
        if(tpls[url] == null){
            $.get(url, function(data){
                tpls[url] = data;
                if(callback){
                    callback(data);
                }
            }, 'html');
        }
        else{
            if(callback){
                callback(tpls[url]);
            }
        }


        return tpls[url];
    }

    /**
     * Human readable file size
     */
    $.formatFileSize = function (filesize) {
        if (filesize >= 1073741824) {
            filesize = number_format(filesize / 1073741824, 2, '.', '') + ' GB';
        } else {
            if (filesize >= 1048576) {
                filesize = number_format(filesize / 1048576, 2, '.', '') + ' MB';
            } else {
                if (filesize >= 1024) {
                    filesize = number_format(filesize / 1024, 0) + ' KB';
                } else {
                    filesize = number_format(filesize, 0) + ' bytes';
                }
            }
        }
        return filesize;
    };
    
    
    /**
     * default options
     */
    $.fn.serverbrowser.defaults =
    {
        'template':'/plugins/serverbrowser/template.tpl',
        'serverUrl':'/' + CURR_LANG + '/admin/file-server/index',
        'selectableOptions':{},
        'initPath':'',
        'activeModule':'',
        'startingSlash':true
    };
})(jQuery);   // pass the jQuery object to this function