/**
 * jQuery FB select box
 * 
 */
(function($)
{
    /**
     * main gmailname function
     */
    $.fn.hfbSelect = function(options)
    {
        //create options
        $.fn.hfbSelect.options = $.extend({}, $.fn.hfbSelect.defaults, options);
        var opts = $.fn.hfbSelect.options;

        // return the object back to the chained call flow
        return this.each(function()
        {
            if(this.selectedIndex >= 0 && this.options[this.selectedIndex]){
                var selectedText = this.options[this.selectedIndex].text;
            }
            else{
                var selectedText = '';
            }
            var html = '<div class="hfbFilterMenu"><div class="filterLabel">';
            html += selectedText + '<span></span></div>';
            html += '<ul>';
            for(var i = 0; i < this.options.length; i++){
                if(this.selectedIndex == i){
                    var selected = 'class="selected"';
                }
                else{
                    selected = '';
                }
                html += '<li ' + selected + 'data-index="' + i + '" data-value="' + this.options[i].value + '">' + this.options[i].text + '</li>';
            }
            html += '</ul></div>';
            var selectBoxDom  = this;
            var selectBox = $(this);
            selectBox.after(html);
            selectBox.hide();
            var filterBox = selectBox.next();
            filterBox.hover(
                function(){
                    $(this).find('ul').show();
                },
                function (){
                    $(this).find('ul').hide();
                }
            );
            filterBox.find('ul li').click(function(){
                filterBox.find('ul li').removeClass('selected');
                $(this).addClass('selected');
                filterBox.find('div.filterLabel').html($(this).html() + '<span></span>');
                filterBox.find('ul').hide();
                selectBoxDom.selectedIndex = $(this).data('index');
                selectBox.trigger('change');
            });
        });
    };

    /**
     * default options
     */
    $.fn.hfbSelect.defaults =
    {
    };

    /**
     * running options
     */
    $.fn.hfbSelect.options = {};
})(jQuery); // pass the jQuery object to this function