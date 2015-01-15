/**
 * jQuery Image Browser Dialog - Boostrap version
 *
 */
(function($)
{

    /**
     * main jquery plugin function
     */
    $.fn.promptBS = function(options)
    {
        var opts = {};
        var div = null;

        //create options
        opts = $.extend({}, $.fn.promptBS.defaults, options);

        function getDiv(){
            if(div == null){
                div = $('\
<div id="promptDiv" class="modal fade" data-focus-on="input:first" style="display:none">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
        <h4 class="modal-title">' + opts.title + '</h4>\
      </div>\
      <div class="modal-body">\
        <label for="prompt_text">' + opts.label + '</label>\
        <input type="text" class="_input" name="prompt_text" id="prompt_text" value="' + opts.value + '" /></form>\
      </div>\
      <div class="modal-footer">\
        <button id="okButton" type="button" class="btn btn-primary">' + _("OK") + '</button>\
        <button id="closeButton" type="button" data-dismiss="modal" class="btn btn-default">' + _("Close") + '</button>\
      </div>\
</div>\
');
                $('body').append(div);
                //events
                div.find('#okButton').click(function(){
                    okDialog();
                });
                //keyboard
                div.keypress(function(e) {
                    if (e.which == 13) {
                        okDialog();
                        return false;
                    }
                });
            }

            return $(div);
        }

        function okDialog(){
            div.modal('hide');
            opts.onClose(div.find("#prompt_text").val());
        }

        getDiv().modal('show');
    }
    /**
     * default options
     */
    $.fn.promptBS.defaults =
    {
        width:300,
        height:200,
        title: "Prompt",
        value: "",
        label: "Enter value",
        onClose: function(value){}
    };


})(jQuery);   // pass the jQuery object to this function