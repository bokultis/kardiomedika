Function.prototype.inherits = function(parent)
{
    this.prototype = new parent();
    this.prototype.constructor = this;
}

var hDashboard = {
    Manager: {
        dirty: false,
        options:{
            componentSelector: '.hd-component',
            regionSelector: '.hd-region',
            data:{},
            components:[],
            onUpdate: function(){
                console.log("widgets updated");
            }
        },
        // creates an empty namespace
        createNamespace: function (className) {
            //prepare namespace
            var ns = window;
            if (className != '') {
                var parts = className.split('.');
                for (var i = 0; i < (parts.length - 1); i++) {
                    if (!ns[parts[i]]) {
                        ns[parts[i]] = {};
                    }
                    ns = ns[parts[i]];
                }
            }
        },
        reflectClass: function (className){
            return eval(className);
        },
        widgetFactory: function(componentClassName,settings){
            var componentClass = this.reflectClass(componentClassName);
            return new componentClass(settings,componentClassName);
        },
        widgetRender: function(widgetObject,containerDom, replaceWith){
            var self = this;
            var content = $('<div class="hd-widget-wrapper">\n\
                                <div class="hd-widget-header">\n\
                                    <span class="hd-widget-title"></span>\n\
                                    <div class="hd-widget-buttons"><a href="#" class="hd-button move inactive" title="Move">Move</a>\n\
                                    <a href="#" class="hd-button configure inactive" title="Configure">Configure</a>\n\
                                    <a href="#" class="hd-button collapse inactive" title="Collapse">Collapse</a>\n\
                                    <a href="#" class="hd-button close inactive" title="Close">Close</a></div>\n\
                                </div><div class="hd-widget-content"></div></div>');
            content.data("widget-object",widgetObject);
            content.find(".hd-widget-content").attr('id', 'widget-' + $('.hd-widget-wrapper').length);
            content.find(".hd-widget-title").text(widgetObject.getTitle());
            if(replaceWith){
                $(containerDom).replaceWith(content);
            }
            else{
                $(containerDom).append(content);
            }
            widgetObject.render(content.find(".hd-widget-content"));
            content.find(".hd-widget-header").hover(function(){
                var widgetWrapper = self.findWidgetWrapper($(this));
                $(widgetWrapper).addClass('hd-widget-wrapper-hover');
                $(this).find("a").removeClass('inactive');
            },
            function(){
                var widgetWrapper = self.findWidgetWrapper($(this));
                $(widgetWrapper).removeClass('hd-widget-wrapper-hover');
                //$(this).find("a").show();
                $(this).find("a").addClass('inactive');
            });
            //attach events
            content.find("a.configure").click(function(){
                var widgetObject =  self.findWidgetObject($(this));
                widgetObject.configure();
                return false;
            });
            content.find("a.close").click(function(){
                var widgetWrapper = self.findWidgetWrapper($(this));
                $(widgetWrapper).remove();
                self._triggerUpdate();
                return false;
            });
            content.find("a.collapse").click(function(){
                var collapseButton = this;
                var widgetWrapper = self.findWidgetWrapper($(this));
                $(widgetWrapper).find(".hd-widget-content").toggle(function(){
                    if($(this).is(":visible")){
                        $(collapseButton).removeClass('collapsed');
                    }
                    else{
                        $(collapseButton).addClass('collapsed');
                    }
                });
                self._triggerUpdate();
                return false;
            });
        },
        findWidgetWrapper: function(widgetElement){
            var wrappers = $(widgetElement).parents(".hd-widget-wrapper");
            if(wrappers.length > 0){
                return wrappers[0];
            }
            else{
                return null;
            }
        },
        findWidgetObject: function(widgetElement){
            var wrapper =  this.findWidgetWrapper(widgetElement);
            if(wrapper){
                return $(wrapper).data('widget-object')
            }
            else{
                return null;
            }
        },
        
        regionRender: function(regionDom){
            var regionId = $(regionDom).attr("id");
            if(!this.options.data.regions[regionId] || !this.options.data.regions[regionId].widgets){
                return;
            }
            var widgets = this.options.data.regions[regionId].widgets;
            for(var i in widgets){
                var currWidget = widgets[i];
                var widgetObject = this.widgetFactory(currWidget.componentClass,currWidget.settings);
                this.widgetRender(widgetObject,regionDom);
            }
        },

        toolboxRender: function(){
            var html = '';
            for(var i in this.options.components){
                var className = this.options.components[i];
                var componentClass = this.reflectClass(className);                  
                var obj = new componentClass(null,componentClass);
                //var class
                html += '<li><div class="hd-component" data-class_name="' + className +'">' + obj.title + '</div></li>';
            }
            $("#hd-toolbox .hd-toolbox-widgets").html(html);
        },

        updateData: function(){
            var self = this;
            var data = {};
            $(this.options.regionSelector).each(function(){
                var regionId = $(this).attr("id");
                data[regionId] = {'widgets':[]};
                $(this).find(".hd-widget-wrapper").each(function(){
                    var widgetObject = $(this).data("widget-object");
                    var widgetData = {
                        'componentClass': widgetObject.getClassName(),
                        'settings': widgetObject.getSettings()
                    };
                    data[regionId]['widgets'].push(widgetData);
                });
                
            });
            this.options.data = data;
        },

        getData: function(){
            return this.options.data;
        },

        _triggerUpdate: function(){
            this.options.onUpdate();
        },

        init: function(options){
            var self = this;
            self.options = $.extend({}, self.options, options);
            //render regions and widgets
            $(this.options.regionSelector).each(function(){
                self.regionRender(this);
            });
            //render components toolbox
            self.toolboxRender();

            //add draggable to toolbox components from admin panel
            $(this.options.componentSelector).draggable({
                connectToSortable: self.options.regionSelector,
                helper: "clone",
                revert: "invalid"
            });
            //add sortable to exist assigned components to regions
            $(self.options.regionSelector).sortable({
                connectWith: self.options.regionSelector,
                placeholder: "hd-placeholder",
                helper: 'clone',
                handle: 'a.move',
                revert: true,
                dropOnEmpty: true,
                start: function(event, ui) {
                    //alert('start');
                },
                over: function(event, ui) {
                    //alert('over');
                },
                update: function(event, ui) {
                    //alert('update');
                },
                stop: function(event, ui) {                    
                    //draggable finish here                    
                    if($(ui.item).hasClass('hd-widget-wrapper')){
                        self._triggerUpdate();
                        return;
                    }
                    //console.log($(ui.item).data('class_name'));
                    var widgetObject = self.widgetFactory($(ui.item).data('class_name'),{});
                    //console.log(widgetObject);
                    self.widgetRender(widgetObject, $(ui.item), true);
                    self._triggerUpdate();
                }
            }).disableSelection();
        }
    }
}