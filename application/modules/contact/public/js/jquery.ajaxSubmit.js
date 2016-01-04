/*
 * Submit form via ajax Plugin
 * @author milan
 * @copyright horisen
 * 
 */
;
(function ( $, window, document, undefined ) {

    // Create the defaults once
    var pluginName = "ajaxSubmit",
    defaults = {
        errorClass: 'error',
        addErrorClass: true,
        fieldSelector: '#data\\[{field}\\]',
        onSubmit: function(data, errors){}
    };

    // The actual plugin constructor
    function Plugin ( element, options ) {
        this.element = element;
        this.$el = $(this.element);
        this.options = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {
            var self = this;
            this.$el.submit(function(ev){
                ev.preventDefault();
                var values = {};
                $.each(self.$el.serializeArray(), function(i, field) {
                    values[field.name] = field.value;
                });                
                $.ajax({
                    type: self.$el.attr('method'),
                    url: self.$el.attr('action'),
                    data: values,
                    success: function(data) {
                        var errors = self.parseResult(data);
                        //console.log(errors);
                        self.options.onSubmit(data, errors);            
                    },
                    error: function(data) {
                        console.log('unknown error')
                    }
                });                
            })
        },
        parseResult: function(data){
            function escapeRegExp(string) {
                return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
            }
            function replaceAll(string, find, replace) {
                return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
            }            
            this.$el.find('.' + this.options.errorClass).removeClass(this.options.errorClass);
            if(!data['message']){
                return [];
            }
            var errors = [];
            for(var field in data['message']){
                var fieldSelector = replaceAll(this.options.fieldSelector, '{field}', field);
                if(this.options.addErrorClass){
                    this.$el.find(fieldSelector).addClass(this.options.errorClass);
                }
                for(var errorKey in data['message'][field]){
                    errors.push(data['message'][field][errorKey]);
                }      
            }
            return errors;
        }
    });

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[ pluginName ] = function ( options ) {
        this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });

        // chain jQuery functions
        return this;
    };

})( jQuery, window, document );