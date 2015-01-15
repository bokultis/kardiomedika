/*
 * JS Class for opening standard Zend form pages as JQuery Dialog
 * depends on JQUERY/UI
 */

var ajaxForm = (function() {
    var div = null;
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
        /*restabsOptions: {
            onClose: function(success, data){},
            onContent: function(restabsId){}
           
        },*/
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
                ajaxForm.parseErrors(nextElementId, field, currentMessage[field],errors);
            }
        },
        
        newTab: function (formUrl, options) {
            if(options == null){
                options = {};
            }
            //console.log("Data: " + options);
            ajaxForm.restabsOptions = $.extend({}, ajaxForm.restabsOptions, options);
            //console.log(ajaxForm.restabsOptions);
            var method = "GET";
            var restabsIdBtn = $(".res-tabs").restabs("getActiveId");
            var restabsId = $("#" + restabsIdBtn).attr("aria-controls");
            
            
            $.ajax({
                type: method,
                url: formUrl,
                data: options.data,
                success: function(data) {
                    var self = $("#" + restabsId);
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
                            ajaxForm.restabsOptions.saveCaption = $(this).val();
                            //fetch callback event on select
                            
                            if($(this).data('onsave')){
                                saveCallback = $(this).data('onsave');
                            }
                        }
                        if($(this).attr("name") == 'submit[cancel]'){
                            ajaxForm.restabsOptions.cancelCaption = $(this).val();
                        }
                        $(this).remove();
                        
                    }); 
                    // add pageId to all inputs 
                    var editedPageId = $("#" + restabsIdBtn).attr("data-pageid");
                    self.find("input:text, textarea, select").each(function(){
                        $(this).attr("data-pageid", editedPageId);
                        var oldId = $(this).attr("id");
                        var newId = oldId + "-" + editedPageId;
                        $(this).attr("id", newId);
                    });
                    
                    
                    var restabsButtons = {};
                    //SAVE BUTTON
                    if(ajaxForm.restabsOptions.saveCaption != ''){
                        restabsButtons[ajaxForm.restabsOptions.saveCaption] = function() {
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
                                        
                                        
                                    }
                                    else{
                                        var errors = {};
                                        ajaxForm.parseErrors('data',null,data['message'],errors);
                                        for(var field in errors){
                                            var errorUl = '<ul class="error">';
                                            for(var i = 0; i < errors[field].length; i++){
                                                errorUl += '<li>' + errors[field][i] + '</li>';
                                            }
                                            errorUl += '</ul>';
                                            $(ajaxForm.jqId(field)).parent().append(errorUl);
                                        }
                                    }
                                },
                                error: function(data) {
                                    alert(_('An error has occured retrieving data!'));
                                }
                            });
                        };
                    }
                    
                    
                    //CANCEL BUTTON
                    
                    if(ajaxForm.restabsOptions.cancelCaption != ''){
                        restabsButtons[ajaxForm.restabsOptions.cancelCaption] = function(){
                            //execute callback
                            ajaxData = null;
                            ajaxResult = false;
                        };
                    }
                    
                    ajaxForm.restabsOptions.buttons = restabsButtons;
                    self.append( "<div class='actionButtonContainer'> <button class='btn btn-primary' id='saveButton'>" + ajaxForm.restabsOptions.saveCaption + "</button>  <button class='btn btn-primary' id='cancelButton'>" + ajaxForm.restabsOptions.cancelCaption + "</button> </div>" );
                    
                    
                    
                    self.on("save", restabsButtons[ajaxForm.restabsOptions.saveCaption]);
                    
                    
                    
                    
                    ajaxForm.restabsOptions.onContent(self);
                    
                },
                error: function() {
                    alert(_('An error has occured retrieving data!'));
                }
            });
        }
    };
})();


                 
               