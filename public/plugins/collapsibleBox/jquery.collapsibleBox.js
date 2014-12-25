/*
 * Collapsible Plugin
 * @author milan
 * @copyright horisen
 * 
 */
;
(function ( $, window, document, undefined ) {

    // Create the defaults once
    var pluginName = "collapsibleBox",
    defaults = {
        handleSelector: ".collapsibleHandle",
        handleHtml: "",
        collapsibleSelector: ".collapsibleSelector",
        openClass: "open",
        closedClass: "closed",
        noToggleClass: "noToggle",
        onChange: function(el, closed){},
        getIsClosed: function(widget){
            return {
                toggable: true,
                closed: widget.$collapsible.hasClass(widget.options.closedClass)
            }
        }
    };

    // The actual plugin constructor
    function Plugin ( element, options ) {
        this.element = element;
        this.$el = $(this.element);
        this.options = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.toggable = true;
        this.isClosed = null;
        this.requestedClosed = null;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        updateStatus: function(){
            var self = this, result = {};
            //do not recalc if status was explicitely requested
            if(this.requestedClosed == null){
                result = this.options.getIsClosed(this);
                //console.log('Get calc result', result);
            }else{
                result = {
                    "closed": this.isClosed,
                    "toggable": this.toggable       
                };                
                //console.log('Get persistent result', result);
            }      
            this.setStatus(result.closed, true);
            this.toggable = result.toggable;
            if(this.toggable){
                this.$handle.removeClass(this.options.noToggleClass);                
            }
            else{
                this.$handle.addClass(this.options.noToggleClass);
            }
        },
        
        init: function () {
            var self = this;
            if(this.options.handleHtml){
                this.$customHandle = $(this.options.handleHtml);
                this.$el.append(this.$customHandle);
            }
            this.$handle = this.$el.find(this.options.handleSelector);
            this.$collapsible = this.$el.find(this.options.collapsibleSelector);
            //click logic            
            this.$handle.click(function(){
                self.requestedClosed = !self.isClosed;
                self.setStatus(self.requestedClosed);
                return false;
            });            
            //init status
            this.updateStatus();
            this.addResizeListener();
        },
        
        setStatus: function(closed, force){
            this.isClosed = closed;
            if(this.toggable || force){
                var removeClass = this.options.openClass, addClass = this.options.closedClass;
                if(!closed){
                    removeClass = this.options.closedClass;
                    addClass = this.options.openClass;
                }
                this.$handle.removeClass(removeClass).addClass(addClass);
                this.$collapsible.removeClass(removeClass).addClass(addClass);
                this.$el.removeClass(removeClass).addClass(addClass);                
            }
            this.options.onChange(this.$el, this.isClosed);            
        },
        
        addResizeListener: function(){
            var self = this;
            var resizeWin = null;
            $(window).bind('resize', function(e){
                if(resizeWin){
                    clearTimeout(resizeWin);
                    resizeWin = null;
                }                
                resizeWin = setTimeout(function(){
                    self.updateStatus(); 
                }, 1000);
            });
        }        
        
        
    });

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[ pluginName ] = function ( options, args ) {
        var result = null;
        this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            } else {
                var plugin = $.data( this, "plugin_" + pluginName);
                
                switch(options){
                    case 'getStatus':                        
                        result =  plugin.isClosed;
                        break;                    
                    case 'setStatus':
                        plugin.setStatus.apply(plugin, args);
                        break;
                    default:
                        break;
                }                
            }
        });
        if(result != null){
            return result;
        }
        // chain jQuery functions
        return this;
    };

})( jQuery, window, document );