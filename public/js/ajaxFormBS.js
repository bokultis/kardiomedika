/* 
 * JS Class for opening standard Zend form pages as Bootstrap Modal
 * depends on JQUERY/UI
 * depends on the function sessionUrl
 */

var ajaxFormBS = (function() {
    var div = null;

    function getDialog(){
        if(this.div){
            return $(this.div);
        }
        else{
            var $div = $('\
<div id="ajaxFormDialog" class="modal fade">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
        <h4 class="modal-title"></h4>\
      </div>\
      <div class="modal-body">\
      </div>\
      <div id="ajaxFormDialogButtons" class="modal-footer">\
        <button type="button" class="btn btn-default btn-small" data-dismiss="modal">Close</button>\
        <button type="button" class="btn btn-primary btn-small">Save changes</button>\
      </div>\
</div>\
');
            $("body").append($div);
            this.div = $div;         
            return $div;
        }
    }

    var ajaxResult = false;
    var ajaxData = null;

    /**
     * execute callback
     */
    function executeOnClose(success,data){
        if(ajaxFormBS.dialogOptions && ajaxFormBS.dialogOptions.onClose && typeof ajaxFormBS.dialogOptions.onClose == 'function'){
            ajaxFormBS.dialogOptions.onClose(success,data);
        }
        var self = getDialog();
        self.modal('hide');
        self.data('onCloseExecuted',true);
    }

    return { // public interface
        dialogOptions: {
            saveCaption: _('Save'),
            cancelCaption: _('Cancel'),
            closeOnSave: true,
            onClose: function(success, data){},
            onContent: function(dialog){},
            onSubmit: function(success,data,errors){},
            customButtons: []
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
                ajaxFormBS.parseErrors(nextElementId, field, currentMessage[field],errors);
            }
        },
        
        dialog: function (formUrl,options) {
            if(options == null){
                options = {};
            }
            ajaxFormBS.dialogOptions = $.extend({}, ajaxFormBS.dialogOptions, options);
            var method = options.method?options.method:"GET";
            $.ajax({
                type: method,
                // add fix sessionUrl for Safari 
                url: sessionUrl(formUrl),
                data: options.data,
                success: function(data) {
                    var self = getDialog();
                    //set dialog content
                    self.find(".modal-body").html(data);
                    //set dialog title
                    self.find(".modal-title").text(self.find("h1").text());
                    self.find("h1").remove();
                    //clean up buttons
                    self.find('#ajaxFormDialogButtons.modal-footer').html('');
                    //set post action -- add fix sessionUrl for Safari
                    var postAction = sessionUrl(self.find("form").attr("action"));
                    var saveCallback = null;
                    //set dialog buttons
                    //console.log(self.find("button.btn"));
                    self.find("[name='submit\\[save\\]']").each(function(){                        
                        ajaxFormBS.dialogOptions.saveCaption = $(this).text();
                        //fetch callback event on select
                        if($(this).data('onsave')){
                            saveCallback = $(this).data('onsave');
                        }
                        $(this).remove();
                    });
                    self.find("[name='submit\\[cancel\\]']").each(function(){
                        ajaxFormBS.dialogOptions.cancelCaption = $(this).text();
                        $(this).remove();
                    });
                    
                    //SAVE BUTTON
                    if(ajaxFormBS.dialogOptions.saveCaption != ''){
                        var $saveButton = $('<button type="button" class="btn btn-primary btn-small">' + ajaxFormBS.dialogOptions.saveCaption + '</button>');
                        $saveButton.click(function() {

                            //exec callback and stop exec if cb result is false
                            if(saveCallback){
                                var cbResult = saveCallback();
                                if(!cbResult){
                                    return false;
                                }
                            }
                            $.ajax({
                                type: "POST",
                                data: self.find("form").serialize(),
                                url: postAction,
                                success: function(data) {
                                    var errors = {};
                                    self.find("ul.error").remove();
                                    if(data['success']){
                                        ajaxResult = true;
                                        ajaxData = data;
                                        if( ajaxFormBS.dialogOptions.closeOnSave ){
                                            if(data['message']){
                                                $.flashMessenger(data['message'],{clsName:"ok"});
                                            }
                                            executeOnClose(ajaxResult,ajaxData);
                                        }
                                    }
                                    else{
                                        var errors = {};
                                        ajaxFormBS.parseErrors('data',null,data['message'],errors);
                                        for(var field in errors){
                                            var errorUl = '<ul class="error">';
                                            for(var i = 0; i < errors[field].length; i++){
                                                errorUl += '<li>' + errors[field][i] + '</li>';
                                            }
                                            errorUl += '</ul>';
                                            $(ajaxFormBS.jqId(field)).parent().append(errorUl);
                                        }
                                    }
                                    //custom submit processing
                                    ajaxFormBS.dialogOptions.onSubmit(data['success'],data,errors);
                                },
                                error: function(data) {
                                    alert(_('An error has occured retrieving data!'));
                                }
                            })
                        });
                        self.find('#ajaxFormDialogButtons.modal-footer').append($saveButton);
                    }
                    //CANCEL BUTTON
                    if(ajaxFormBS.dialogOptions.cancelCaption != ''){
                        var $cancelButton = $('<button type="button" class="btn btn-default btn-small">' + ajaxFormBS.dialogOptions.cancelCaption + '</button>');
                        $cancelButton.click(function(){
                            //execute callback
                            ajaxData = null;
                            ajaxResult = false;
                            executeOnClose(ajaxResult,ajaxData);
                        });
                        self.find('#ajaxFormDialogButtons.modal-footer').append($cancelButton);
                    }

                    for(var buttonId in ajaxFormBS.dialogOptions.customButtons){
                        var button = ajaxFormBS.dialogOptions.customButtons[buttonId];
                        if(!button.cssClass){
                            button.cssClass = 'default';
                        }
                        var $button = $('<button type="button" class="btn btn-' + button.cssClass + '">' + button.caption + '</button>');
                        if(button.onClick){
                            $button.click(function(eventData){
                                button.onClick(self, eventData);
                            });
                        }
                        self.find('#ajaxFormDialogButtons.modal-footer').append($button);
                    }

                    $.extend(ajaxFormBS.dialogOptions, options);
                    //open dialog
                    //self.dialog(ajaxFormBS.dialogOptions);
                    self.modal();
                    //custom close handler...if no buttons are clicked
                    self.on('hidden.bs.modal', function (e) {
                        if(!self.data('onCloseExecuted')){
                            executeOnClose(false,null);
                        }                        
                    });                    
                    //apply tipsy
                    //self.find(".hfbTipsy").hfbTipsy();
                    //custom content process
                    ajaxFormBS.dialogOptions.onContent(self);
                },
                error: function(data) {
                    alert(_('An error has occured retrieving data!'));
                }
            });
        },
        closeDialog: function(){
            var self = getDialog();
            executeOnClose(ajaxResult,ajaxData);
        }
    };
})();
