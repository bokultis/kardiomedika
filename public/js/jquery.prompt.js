/**
 * jQuery Image Browser Dialog
 *
 */
(function($)
{

    /**
     * main jquery plugin function
     */
    $.fn.prompt = function(options)
    {
        var opts = {};
        var div = null;

        //create options
        opts = $.extend({}, $.fn.prompt.defaults, options);

        function getDiv(){
            if(div == null){
                div = document.createElement('div');
                document.body.appendChild(div);
                $(div).html('<label for="prompt_text">' + opts.label + '</label><input type="text" class="_input" name="prompt_text" id="prompt_text" value="' + opts.value + '" /></form>');
                $(div).attr({
                    'id': 'promptDiv',
                    'title':opts.title
                }).css('display','none');
            }

            return $(div);
        }

        function okDialog(dialog){
            $(dialog).dialog("close");
            opts.onClose($(div).find("#prompt_text").val());
        }

        getDiv().dialog({
            autoOpen: true,
            resizable: true,
            width:opts.width,
            height:opts.height,
            zIndex:(opts.zIndex)?opts.zIndex:null,
            modal: true,
            open: function(event, ui) {
                var dialog = $(this);
                dialog.find("#prompt_text").focus().select();
                dialog.keyup(function(e) {
                    if (e.keyCode == 13) {
                        okDialog(dialog);
                    }
                });

            },
            close: function(ev,ui){
                $(this).dialog('destroy');
            },
            buttons: [
            {
                text: _("OK"),
                click: function() {
                    okDialog(this);
                }
            },
            {
                text: _("Close"),
                click: function() {
                    $(this).dialog("close");
                    opts.onClose(null);
                }
            }
            ]
        });
    }
    /**
     * default options
     */
    $.fn.prompt.defaults =
    {
        width:300,
        height:200,
        title: "Prompt",
        value: "",
        label: "Enter value",
        onClose: function(value){}
    };


})(jQuery);   // pass the jQuery object to this function