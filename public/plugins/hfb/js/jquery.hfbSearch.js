/**
 * jQuery FB search box
 * 
 */
(function($)
{
    /**
     * main gmailname function
     */
    $.fn.hfbSearch = function(options)
    {
        //create options
        $.fn.hfbSearch.options = $.extend({}, $.fn.hfbSearch.defaults, options);
        var opts = $.fn.hfbSearch.options;

        // return the object back to the chained call flow
        return this.each(function()
        {
            var inputBox = $(this);
            $(this).addClass('hfbSearch').wrap('<div class="hfbSearch"></div>').after('<button class="hfbSearch"></button>');
            $(this).next('button.hfbSearch').click(function(){
                inputBox.val('');
                inputBox.focus();
                if(typeof opts.onclear == 'function') {
                    opts.onclear();
                }
            });
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
    $.fn.hfbSearch.defaults =
    {
        'placeholder':'',
        'onclear':null
    };

    /**
     * running options
     */
    $.fn.hfbSearch.options = {};
})(jQuery); // pass the jQuery object to this function