<div id="ibContainer" class="clearfix">
    <div class="row">
        <div id="ibServerbrowser" class="col-xs-12 col-md-6"></div>
        <div id="ibPanel" class="col-xs-12 col-md-6">
            <div class="ibToolbar btn-toolbar">
                <div class="btn-group">
                    <button class="crop btn btn-default" title="<%=_('Crop')%>"><span class="glyphicon glyphicon-screenshot"></span></button>
                    <button class="resize btn btn-default" title="<%=_('Resize')%>"><span class="glyphicon glyphicon-resize-full"></span></button>
                    <button class="preview btn btn-default" title="<%=_('Preview')%>"><span class="glyphicon glyphicon-picture"></span></button>
                </div>
            </div>
            <div id="ibActionStatus"></div>
            <div id="ibStatusPanel"></div>
            <div id="ibPreview"></div>
            <!--<button class="crop crop_down btn btn-default btn-sm"><span class="glyphicon glyphicon-screenshot"></span> <%=_("Crop")%></button>-->

            <div id="ibCropPreview" class="modal fade" style="display: none;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><%=_("Preview")%></h4>
                </div>
                <div class="modal-body">
                    <div id="ibCropClip"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default"><%=_("Close")%></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ibResize" class="modal fade" data-focus-on="input:first" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><%=_("Resize Image")%></h4>
    </div>
    <div class="modal-body">
        <%=_("Dimensions")%>: <input type="text" name="width" value="" style="width: 40px" maxlength="4" />px x <input type="text" name="height" value="" style="width: 40px" maxlength="4" />px
    </div>
    <div class="modal-footer">
        <button id="okButton" type="button" class="btn btn-primary"><%=_("OK")%></button>
        <button id="closeButton" type="button" data-dismiss="modal" class="btn btn-default"><%=_("Close")%></button>
    </div>
</div>