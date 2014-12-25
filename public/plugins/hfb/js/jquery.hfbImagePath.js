/**
 * jQuery FB imagepath box
 * 
 */
(function($)
{
    /**
     * main function
     */
    $.fn.hfbImagePath = function(options)
    {
        //create options
        $.fn.hfbImagePath.options = $.extend({}, $.fn.hfbImagePath.defaults, options);
        var opts = $.fn.hfbImagePath.options;

        // return the object back to the chained call flow
        return this.each(function()
        {
            var inputBox = $(this);
            $(this).addClass('hfbImagePath').wrap('<div class="hfbImagePath"></div>').after('<button class="hfbImagePath" type="button"></button>');
            $(this).next('button.hfbImagePath').click(function(){
                inputBox.val('');
                inputBox.focus();
                if(typeof opts.onclear == 'function') {
                    opts.onclear();
                }
                return false;
            });

            $(this).trigger('blur');
        });
    };

    /**
     * default options
     */
    $.fn.hfbImagePath.defaults =
    {
        'onclear':null
    };

    /**
     * running options
     */
    $.fn.hfbImagePath.options = {};
})(jQuery); // pass the jQuery object to this function