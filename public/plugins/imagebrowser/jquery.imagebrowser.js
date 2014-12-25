/**
 * jQuery Image Browser
 *
 */
(function($)
{
    include('/plugins/serverbrowser/jquery.serverbrowser.js');
    include('/plugins/serverbrowser/style.css');
    include('/plugins/jcrop/jquery.Jcrop.min.js');
    include('/plugins/jcrop/jquery.Jcrop.css');
    
    /**
     * main jquery plugin function
     */
    $.fn.imagebrowser = function(options)
    {
        var opts = {};
        var self = null;
        var imageRatio = 1;
        var enableScale = true;
        var rescaledDims = {
            maxWidth : 0,
            maxHeight : 0,
            minWidth: 0,
            minHeight: 0
        }
        //set if maxWidth = minWidth, maxHeight = minHeight
        var fixedDims = {
            width: 0,
            height: 0
        }

        if(options == 'selected'){
            self = $(this);
            var selectedFile  = self.find("#ibServerbrowser").serverbrowser('selected');
            if(selectedFile == null || selectedFile.type != 'file' || selectedFile.path == null){
                return null;
            }
            else{
                selectedFile.width = self.find("#ibPreview img").data('width');
                selectedFile.height = self.find("#ibPreview img").data('height');
                return selectedFile;
            }
        }
        //create options
        opts = $.extend({}, $.fn.imagebrowser.defaults, options);

        // return the object back to the chained call flow
        return this.each(function()
        {
            self = $(this);
            $.loadTpl(opts.template,function(tplStr){
                self.html($.tmpl(tplStr,{}));
                hideEditor();
                var options = {
                    'selectableOptions':{
                        selected: function(event, ui){
                            var selectedFile = self.find("#ibServerbrowser").serverbrowser('selected');
                            if(selectedFile == null || selectedFile.type != 'file' || selectedFile.path == null){
                                hideEditor();
                                return false;
                            }
                            else{
                                return loadImage(selectedFile.path);
                            }
                        },
                        unselected: function(event, ui){
                            hideEditor();
                        }
                    },
                    'activeModule': opts.activeModule,
                    'initPath':opts.initPath,
                    'startingSlash':opts.startingSlash
                };

                /**
                 * BUTTONS
                 */

                //CROP
                self.find("#ibServerbrowser").serverbrowser(options);
                //Toolbar
                self.find(".ibToolbar button.crop, #ibPanel button.crop").text(_("Crop")). button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_image_crop"
                    }
                })
                .click(function cropClick(){
                    $(this).blur();
                    if(jcropApi == null){
                        return false;
                    }
                    var cropInfo = jcropApi.tellSelect();
                    if(cropInfo.h == 0 || cropInfo.w == 0){
                        alert(_('Please make a cropping selection first!'));
                        return false;
                    }
                    /*var selection = {
                        destinationX: unscaleDim(cropInfo.x),
                        destinationY: unscaleDim(cropInfo.y),
                        destinationWidth: unscaleDim(cropInfo.w,'width'),
                        destinationHeight: unscaleDim(cropInfo.h,'height')
                    }*/
                    var params = calcImageProcessParams(cropInfo);
                    //console.log(params);
                    applyChanges(params);
                    return false;
                });

                //RESIZE
                self.find(".ibToolbar button.resize").text(_("Resize")).button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_image_resize"
                    }
                })
                .click(function resizeClick(){
                    $(this).blur();

                    //set starting dimensions
                    var dims = {
                        width: self.find('#ibPreview img').data('width'),
                        height: self.find('#ibPreview img').data('height')
                    }
                    if(opts.cropBox[0]){
                        dims.width = opts.cropBox[0];
                    }
                    dims = recalcResize(dims);
                    $("#ibResize input[name=width]").val(dims.width);
                    $("#ibResize input[name=height]").val(dims.height);
                    $('#ibResize input[name=width], #ibResize input[name=height]').keyup(function () {
                        this.value = this.value.replace(/[^0-9]/g,'');
                    }).blur(function(){
                        var dims = {};
                        dims[$(this).attr("name")] = parseInt($(this).val());
                        dims = recalcResize(dims);
                        $("#ibResize input[name=width]").val(dims.width);
                        $("#ibResize input[name=height]").val(dims.height);
                    });

                    $("#ibResize").dialog({
                        resizable: false,
                        height:140,
                        modal: true,
                        buttons: [
                            {
                                text: _("OK"),
                                click: function() {
                                    var dims = recalcResize({
                                        width: $("#ibResize input[name=width]").val(),
                                        height: $("#ibResize input[name=height]").val()
                                    });

                                    var selection = {
                                        sourceX: 0,
                                        sourceY: 0,
                                        sourceWidth: self.find('#ibPreview img').data('width'),
                                        sourceHeight: self.find('#ibPreview img').data('height'),
                                        destinationX: 0,
                                        destinationY: 0,
                                        destinationWidth: dims.width,
                                        destinationHeight: dims.height
                                    };

                                    applyChanges(selection);
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: _("Cancel"),
                                click: function() {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    });
                    return false;
                });

                //PREVIEW
                self.find(".ibToolbar button.preview").text(_("Preview")). button({
                    text:true,
                    icons:{
                        primary:"toolbar_icon icon_image_preview"
                    }
                })
                .click(function previewClick(){
                    $(this).blur();
                    $("#ibCropPreview").dialog({
                        title: _("Preview"),
                        resizable: false,
                        open: function(event, ui) {
                            $(this).dialog({width: 'auto', height: 'auto'});
                        }
                    });
                    return false;
                });

            });
        });

        function recalcResize(dims){
            var ratio = 1;
            var origWidth = self.find('#ibPreview img').data('width');
            var origHeight = self.find('#ibPreview img').data('height');
            if(parseInt(dims.width)){
                ratio = parseInt(dims.width) / origWidth;
                dims.height = Math.round((origHeight * ratio));
            }
            else if(parseInt(dims.height)){
                ratio = parseInt(dims.height) / origHeight;
                dims.width = Math.round((origWidth * ratio));
            }
            else{
                dims.width = origWidth;
                dims.height = origHeight;
            }

            return dims;
        }

        function applyChanges(selection){
            selection.sourcePath = self.find('#ibPreview img').data("path");
            $.post(opts.processUrl + '/active_module/' + opts.activeModule,selection,function(data){
                if(data.success){
                    //reload files list
                    self.find("#ibServerbrowser").serverbrowser('reload');
                    //loadImage(self.find('#ibPreview img').data("path"));
                    $.flashMessenger(data.message);
                }
                else{
                    $.flashMessenger(data.message,{
                        clsName:"err"
                    });
                }
            });

            return true;
        }

        var jcropApi = null;

        function hideEditor(){
            self.find(".ibToolbar").hide();
            self.find("#ibPreview img").remove();
            //close dialog
            $("#ibCropPreview").dialog('close');
            //remove crop preview
            $("#ibCropPreview img").remove();
            //hide crop clip
            $("#ibCropClip").hide();
            //clean dimensions
            self.find("#ibStatusPanel").text("");
            releaseImage();
        }

        function showEditor(){
            cropImage();
            self.find(".ibToolbar").show();
            //display dimensions
            var img = self.find("#ibPreview img");
            if(img){
                var statusText = [];
                if(opts.cropBox[0]){
                    statusText.push(_("Max width") + ": " + opts.cropBox[0] + "px");
                }
                if(opts.cropBox[1]){
                    statusText.push(_("Max height") + ": " + opts.cropBox[1] + "px");
            }
               if(opts.minSize[0]){
                    statusText.push(_("Min width") + ": " + opts.minSize[0] + "px");
        }
                if(opts.minSize[1]){
                    statusText.push(_("Min height") + ": " + opts.minSize[1] + "px");
                }
                statusText.push(_("Original") + ": " + img.data('width') + "x" + img.data('height') + "px");
                self.find("#ibStatusPanel").html(statusText.join(" | "));
            }
        }

        function scaleImage(image){
            //scale for preview
            if(enableScale && (
                $(image).width() > self.find("#ibPreview").width() ||
                $(image).height() > self.find("#ibPreview").height()
            )){
                var xRatio = $(image).width() / self.find("#ibPreview").width();
                var yRatio = $(image).height() / self.find("#ibPreview").height();
                //apply scale
                imageRatio = (xRatio > yRatio) ? xRatio : yRatio;
                $(image).height(scaleDim($(image).data("height")));
                $(image).width(scaleDim($(image).data("width")));
                rescaledDims.maxWidth = rescale(opts.cropBox[0]);
                rescaledDims.maxHeight = rescale(opts.cropBox[1]);
                rescaledDims.minWidth = rescale(opts.minSize[0]);
                rescaledDims.minHeight = rescale(opts.minSize[1]);
            }
            else{
                imageRatio = 1;
            }
        }

        /**
         * Get dimentsion done after scale/rescale
         */
        function rescale(original){
            return unscaleDim(Math.floor(scaleDim(original)));
        }

        /**
         * Calculate cropping information for server processing
         */
        function calcImageProcessParams(cropInfo){
            var params = {
                sourceX: unscaleDim(cropInfo.x),
                sourceY: unscaleDim(cropInfo.y),
                sourceWidth: unscaleDim(cropInfo.w,'width'),
                sourceHeight: unscaleDim(cropInfo.h,'height'),
                destinationX: 0,
                destinationY: 0
            }
            //both dims are fixed
            if(fixedDims.width && fixedDims.height){
                params.destinationWidth = fixedDims.width;
                params.destinationHeight = fixedDims.height;
            }
            //width is fixed
            else if(fixedDims.width){
                params.destinationWidth = fixedDims.width;
                params.destinationHeight = params.sourceHeight * (fixedDims.width / params.sourceWidth);
            }
            //height is fixed
            else if(fixedDims.height){                
                params.destinationHeight = fixedDims.height;
                params.destinationWidth = params.sourceWidth * (fixedDims.height / params.sourceHeight);
            }
            //none is fixed
            else{
                params.destinationWidth = params.sourceWidth;
                params.destinationHeight = params.sourceHeight;                
            }

            return params;
        }

        /**
         * Load image preview
         */
        function loadImage(path){
            //delete old img
            hideEditor();
            //create new img
            var img = document.createElement("img");
            document.getElementById('ibPreview').appendChild(img);
            $(img).hide();
            //create cropPreview
            var cropImg = document.createElement("img");
            document.getElementById('ibCropClip').appendChild(cropImg);
            $(cropImg).hide();
            //set new source
            var src = opts.fileWebRoot + path + "?" + new Date().getTime();
            self.find("#ibPreview img").attr("src", src).data("path",path).load(function(){
                if($(this).height() > 0 && $(this).width() > 0){
                    $(this).data("height",$(this).height());
                    $(this).data("width",$(this).width());
                    //scale for preview
                    scaleImage(this);
                    //crop preview
                    $("#ibCropPreview img").attr("src", src);
                    $(img).show();
                    showEditor();
                }
                else{
                    $(this).remove();
                }
            })
            return true;
        }

        function releaseImage(){
            //unhook jcrop
            if(jcropApi != null){
                jcropApi.release();
                jcropApi.destroy();
                jcropApi = null;
            }
            //unhook resizable
            //self.find("#ibPreview img").resizable( "destroy" );
            self.find("#ibActionStatus").text("");
        }

        function showCropStatus(c)
        {
            self.find("#ibActionStatus").text(sprintf(_("Cropping rectangle: %dx%d. Click CROP to apply."),unscaleDim(c.w,'width'),unscaleDim(c.h,'height')));

            var processInfo = calcImageProcessParams(c);
            //set cropping clip
            $('#ibCropClip')
                .width(processInfo.destinationWidth)
                .height(processInfo.destinationHeight);
            $('#ibCropPreview img').css({
                marginLeft: '-' + processInfo.sourceX + 'px',
                marginTop: '-' + processInfo.sourceY + 'px'
            });
            //show crop clip
            $("#ibCropClip").show();
            $('#ibCropPreview img').show();
        }

        function scaleDim(x){
            return x / imageRatio;
        }

        function unscaleDim(x, snapToOriginal){
            var result = x * imageRatio;
            if(snapToOriginal == null || fixedDims.height || fixedDims.width){
                return result;
            }
            if(snapToOriginal != 'height'){
                snapToOriginal = 'width';
            }
            switch(snapToOriginal){
                case 'height':
                    if(rescaledDims.maxHeight && result >= rescaledDims.maxHeight){
                        return opts.cropBox[1];
                    }
                    if(rescaledDims.minHeight && result <= rescaledDims.minHeight){
                        return opts.minSize[1];
                    }
                    break;
                case 'width':
                    if(rescaledDims.maxWidth && result >= rescaledDims.maxWidth){
                        return opts.cropBox[0];
                    }
                    if(rescaledDims.minWidth && result <= rescaledDims.minWidth){
                        return opts.minSize[0];
                    }
                    break;
            }
            return result;
        }


        function cropImage(){
            releaseImage();
            self.find("#ibActionStatus").text(_("Drag mouse over image to draw cropping rectangle."));
            self.find('#ibPreview img').Jcrop({
                onChange: showCropStatus,
                onSelect: showCropStatus
                //maxSize:opts.cropBox,
                //boxWidth: 320,
                //boxHeight: 380
            },
            function() {
                jcropApi = this;
                if(!opts.cropBox){
                    return;
                }
                //set proportional cropping
                var cropBox = [];
                //var scaledWidth = parseInt(self.find('#ibPreview .jcrop-holder img').css('width'));
                var origWidth = self.find('#ibPreview img').data('width');
                var origHeight = self.find('#ibPreview img').data('height');
                //max size
                if(opts.cropBox[0]){
                    cropBox[0] = scaleDim(opts.cropBox[0]);
                    cropBox[1] = scaleDim(opts.cropBox[1]);
                }
                var minSize = [];
                if(opts.minSize[0]){
                    minSize[0] = scaleDim(opts.minSize[0]);
                    minSize[1] = scaleDim(opts.minSize[1]);
                }
                //set fixed sizes
                fixedDims.width = 0;fixedDims.height = 0;
                if(opts.cropBox[0] == opts.minSize[0] && opts.cropBox[0] > 0){
                    fixedDims.width = opts.cropBox[0];
                }
                if(opts.cropBox[1] == opts.minSize[1] && opts.cropBox[1] > 0){
                    fixedDims.height = opts.cropBox[1];
                }
                //set cropping options
                if(fixedDims.width > 0 && fixedDims.height > 0){
                    //flexible cropping with aspect ratio
                    jcropApi.setOptions({
                        'aspectRatio': fixedDims.width / fixedDims.height
                    });
                }
                else if(fixedDims.width == 0 && fixedDims.height == 0){
                    //fixed cropping with boundries
                    jcropApi.setOptions({
                        'maxSize': cropBox,
                        'minSize': minSize
                    });
                }

                if( (opts.cropBox[0] && opts.cropBox[0] < origWidth) ||
                    (opts.cropBox[1] && opts.cropBox[1] < origHeight)){

                    var x2 = opts.cropBox[0]?opts.cropBox[0]:100;
                    var y2 = opts.cropBox[1]?opts.cropBox[1]:100;
                    var x1 = Math.round(scaleDim((origWidth - x2) / 2));
                    var y1 = Math.round(scaleDim((origHeight - y2) / 2));


                    jcropApi.setOptions({
                        setSelect: [x1, y1, scaleDim(x2) + x1, scaleDim(y2) + y1]
                    });

                }
            }
            );
        }

        function resizeImage(){
            releaseImage();
            self.find("#ibActionStatus").text(_("Drag lover left handler to resize."));
            self.find("#ibPreview img").resizable({
                resize: function(event, ui) {
                    self.find("#ibActionStatus").text(sprintf(_("Current dimensions: %dx%d. Click APPLY to resize."),ui.size.width,ui.size.height));
                },
                aspectRatio:true,
                helper: "ui-resizable-helper"
            });
        }
    };

    /**
     * default options
     */
    $.fn.imagebrowser.defaults =
    {
        'template':'/plugins/imagebrowser/template.tpl',
        'initPath':'/',
        'processUrl':'/' + CURR_LANG + '/admin/file-server/process-image',
        'cropBox':[0,0],
        'minSize':[0,0],
        'startingSlash':true
    };


})(jQuery);   // pass the jQuery object to this function