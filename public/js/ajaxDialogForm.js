/*
 * JS Class for opening standard Zend form pages as JQuery Dialog
 * depends on JQUERY/UI
 */

var ajaxDialogForm = (function() {
    var div = null;

    function getDialog(dialogId){
        if(dialogId){
            return $("#"+dialogId);
        }else{
            if(this.div){
                return $(this.div);
            }
            else{
                var id = 'ajaxDialogFormDialog';
                var div = document.createElement("div");
                div.id = id;
                document.body.appendChild(div);
                this.div = div;
                return $(div);
            }   
        }
        
    }

    /**
     * execute callback
     */
    function executeOnClose(options,success,data){
        if(options && options.onClose && typeof options.onClose == 'function'){
            options.onClose(success,data);
        }
    }

    var ajaxResult = false;
    var ajaxData = null;

    return { // public interface
        dialogOptions: {
            autoOpen: true,
            height: 550,
            width: 400,
            modal: true,
            saveCaption: 'Save',
            cancelCaption: 'Cancel',
            onClose: function(success, data){},
            onContent: function(dialog){},
            close: function(ev,ui){
                //execute callback
                executeOnClose(ajaxDialogForm.dialogOptions, ajaxResult, ajaxData);
                $(this).dialog('destroy');
            }
        },
        /**
         * return escaped element id
         */
        jqId: function (myid) {
            return '#' + myid.replace(/(:|\[|\]|\.)/g,'\\$1');
        },

        /**
         * parse nested array, recursivly
         */
        parseErrors: function (currentElementId,errorName, currentMessage, errors){
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

            var nextElementId = currentElementId;
            for(var field in currentMessage){
                if(errorName){
                    nextElementId = currentElementId + '[' + errorName + ']';
                }
                ajaxDialogForm.parseErrors(nextElementId, field, currentMessage[field],errors);
            }
        },
        
        dialog: function (formUrl,options) {
            if(options == null){
                options = {};
            }
            ajaxDialogForm.dialogOptions = $.extend({}, ajaxDialogForm.dialogOptions, options);
            var method = options.method?options.method:"GET";
            var dialogId = options.dialogId?options.dialogId:"";
            $.ajax({
                type: method,
                url: formUrl,
                data: options.data,
                success: function(data) {
                    var self = getDialog(dialogId);
                    //set dialog content
                    self.html(data);
                    //set dialog title
                    self.attr("title",self.find("form").attr("title"));
                    //set post action
                    var postAction = self.find("form").attr("action");
                    var saveCallback = null;
                    //set dialog buttons
                    self.find("input:submit,button.submit").each(function(){
                        if($(this).attr("name") == 'submit[save]'){
                            ajaxDialogForm.dialogOptions.saveCaption = $(this).val();
                            //fetch callback event on select
                            if($(this).data('onsave')){
                                saveCallback = $(this).data('onsave');
                            }
                        }
                        if($(this).attr("name") == 'submit[cancel]'){
                            ajaxDialogForm.dialogOptions.cancelCaption = $(this).val();
                        }
                        $(this).remove();
                    });
                    var dialogButtons = {};
                    //SAVE BUTTON
                    if(ajaxDialogForm.dialogOptions.saveCaption != ''){
                        dialogButtons[ajaxDialogForm.dialogOptions.saveCaption] = function() {
                            //exec callback and stop exec if cb result is false
                            if(saveCallback){
                                var cbResult = saveCallback();
                                if(!cbResult){
                                    return false;
                                }
                            }
                            $.ajax({
                                type: "POST",
                                data: $(this).find("form").serialize(),
                                url: postAction,
                                success: function(data) {
                                    self.find("ul.error").remove();
                                    if(data['success']){
                                        if(data['message']){
                                            $.flashMessenger(data['message'],{clsName:"ok"});
                                        }
                                        ajaxResult = true;
                                        ajaxData = data;

                                        self.dialog('close');
                                    }
                                    else{
                                        var errors = {};
                                        ajaxDialogForm.parseErrors('data',null,data['message'],errors);
                                        for(var field in errors){
                                            var errorUl = '<ul class="error">';
                                            for(var i = 0; i < errors[field].length; i++){
                                                errorUl += '<li>' + errors[field][i] + '</li>';
                                            }
                                            errorUl += '</ul>';
                                            $(ajaxDialogForm.jqId(field)).parent().append(errorUl);
                                        }
                                    }
                                },
                                error: function(data) {
                                    alert(_('An error has occured retrieving data!'));
                                }
                            })
                        };
                    }
                    //CANCEL BUTTON
                    if(ajaxDialogForm.dialogOptions.cancelCaption != ''){
                        dialogButtons[ajaxDialogForm.dialogOptions.cancelCaption] = function(){
                            //execute callback
                            ajaxData = null;
                            ajaxResult = false;
                            self.dialog('close');
                        };
                    }

                    ajaxDialogForm.dialogOptions.buttons = dialogButtons;

                    $.extend(ajaxDialogForm.dialogOptions, options);
                    //open dialog
                    self.dialog(ajaxDialogForm.dialogOptions);
                    //custom content process
                    ajaxDialogForm.dialogOptions.onContent(self);
                },
                error: function(data) {
                    alert(_('An error has occured retrieving data!'));
                }
            });
        }
    };
})();