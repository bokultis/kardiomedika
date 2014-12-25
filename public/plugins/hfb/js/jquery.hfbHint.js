/**
 * jQuery FB search box
 * 
 */
(function($)
{
    /**
     * main gmailname function
     */
    $.fn.hfbHint = function(options)
    {
        //create options
        $.fn.hfbHint.options = $.extend({}, $.fn.hfbHint.defaults, options);
        var opts = $.fn.hfbHint.options;

        // return the object back to the chained call flow
        return this.each(function()
        {
            var inputBox = $(this);
            $(this).addClass('hfbHint').wrap('<div class="hfbHint"></div>');
            $(this).blur(function(){
                if($(this).val() == ''){
                    $(this).val(opts.placeholder).addClass('hfbPlaceholder');
                }
            }).focus(function(){
                if($(this).val() == opts.placeholder){
                    $(this).val('').removeClass('hfbPlaceholder');
                }
            });

            $(this).trigger('blur');
        });
    };

    /**
     * default options
     */
    $.fn.hfbHint.defaults =
    {
        'placeholder':'',
        'onclear':null
    };

    /**
     * running options
     */
    $.fn.hfbHint.options = {};
})(jQuery); // pass the jQuery object to this function