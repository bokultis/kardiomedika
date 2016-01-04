/*! jQuery UI Widgets - v1.10.3 - 2013-05-17
* Includes: custom.combobox
**/

(function( $ ) {
    $.widget( "custom.combobox", {
        options: {
            ajaxURL: '',
            delay: 300
        },
        
        elementId: "",
        populated: false,
        
        _create: function() {
            this.wrapper = $( "<span>" )
                .addClass( "custom-combobox" )
                .insertAfter( this.element );
        
            this.elementId = $(this.element).attr("id") || this.widgetFullName + "-" + this.uuid;
            
            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },

        _createAutocomplete: function() {
            var selected = this.element.children(":selected"),
                value = selected.val() ? selected.text() : "";
            
            var that = this,
                select  = this.element;
            
            this.input = $("<input>")
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default" )
                .autocomplete({
                    delay: that.options.delay,
                    minLength: 0,
                    source: $.proxy( this, "_source" )
                })
                .tooltip({
                    tooltipClass: "ui-state-highlight"
                });
                
            var bIdx = this.elementId.indexOf(']');
            var elmId = "";
            
            if (bIdx >= 0){
                elmId = this.elementId.substring(0, bIdx) + "_value" + "]";
            } else {
                elmId = this.elementId + "_value";
            }
            this.input_value = $( "<input type='hidden'>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr({
                    id: elmId,
                    name: elmId
                    });
            
            var that = this;
            this.input.keydown(function(e) {
                var key = 0;
                if (e.keyCode) { key = e.keyCode; }
                else if (typeof(e.which)!= 'undefined') { key = e.which; }
                
                if (key != 38 && key != 40 && key != 13 && key != 9 && key != 27 && key != 37 && key != 39 && key != 17 && key != 18) {
                    if ($(select).get(0).options.length === 0 && $(this).val().length < 1 && !that.populated) {
                        that.populateList(1);
                    }
                }
            });

            this._on( this.input, {
                autocompleteselect: function( event, ui ) {
                    // push value into hidden field
                    this.input_value.val(ui.item.option.value);
                    
                    ui.item.option.selected = true;
                    this._trigger( "select", event, {
                        item: ui.item.option
                    });
                },

                autocompletechange: "_checkIfInvalid"
            });
        },

        _createShowAllButton: function() {
            var that = this,
                input = this.input,
                select  = this.element,
                options = this.options,
                wasOpen = false;

            $( "<a>" )
                .attr( "tabIndex", -1 )
//                .attr( "title", "Show All Items" )
//                .tooltip()
                .appendTo( this.wrapper )
                .button({
                    icons: {
                        custom: "custom-icon-down"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "custom-combobox-toggle" )
                .mousedown(function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
                .click(function() {
                    input.focus();

                    // Close if already visible
                    if ( wasOpen ) {
                      return;
                    } else {
                        // If options exists...
                        if ($(select).get(0).options.length > 0) {
                            // Display all results
                            input.autocomplete( "search", "" );
                        } else {
                            if (!that.populated){
                                that.populateList(0);
                            }
                        }
                    }
                });
        },

        _source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            response( this.element.children( "option" ).map(function() {
                var text = $( this ).text();
                if ( this.value && ( !request.term || matcher.test(text) ) )
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
                }) 
            );
        },

        _checkIfInvalid: function(event, ui) {
            // Selected an item, nothing to do
            if (ui.item) {
                return;
            }
            // Search for a match (case-insensitive)
            var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
        
            // push value into hidden field
            this.input_value.val(value);
            
            this.element.children("option").each(function() {
                if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                    this.selected = valid = true;
                    return false;
                }
            });
            
            // Found a match, nothing to do
            if (valid) {
                return;
            }
        },

        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        },
                
        populateList: function(ind){
            ind = ind || 0; // 0 - display all results; 1 - display result by typed value
            
            var input = this.input,
                select  = this.element,
                options = this.options;
            
            this.populated = true;
            
            $.ajax({
                type:'POST',
                url:options.ajaxURL,
                dataType:'json',
                cache:false,
                success:function(aData){
                    $(select).get(0).options.length = 0;

                    $.each(aData.response, function(i, item) {
                        $(select).get(0).options[$(select).get(0).options.length] = new Option(item.name, item.id);
                    });

                    // Display (all) results
                    input.autocomplete("search", (ind > 0) ? input.value : "" );
                },
                error:function(){
                    alert("Connection Is Not Available");
                }
            });
        },
        
        reset: function() {
            this.input.val("");
            this.input_value.val("");
            this.element.empty();
            this.populated = false;
        },
                
        setValue: function(val) {
            if (val && (typeof val == "string" || typeof val == "number")){
                this.input.val(val);
                this.input_value.val(val);
            } else {
                this.input.val("");
                this.input_value.val("");
            }
            this.populated = false;
        },
         
        setValueAndLabelName: function(val, label_val) {
            if (val && (typeof val == "string" || typeof val == "number")){
                this.input.val(label_val);
                this.input_value.val(val);
            } else {
                this.input.val("");
                this.input_value.val("");
            }
            this.populated = false;
        },
                
        getValue: function() {
            return this.input_value.val();
        },
                
        setOptions: function(options) {
            var key;

            for (key in options) {
                this.setOption(key, options[key]);
            }

            return this;
	},
                
	setOption: function(key, value) {
            this.options[key] = value;
        }
    });
})( jQuery );

