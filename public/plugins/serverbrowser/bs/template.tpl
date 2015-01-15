<ul id="sbMenu" class="contextMenu">
    <li class="copy">
        <a href="#copy"><%=_("Copy")%></a>
    </li>
    <li class="paste">
        <a href="#paste"><%=_("Paste")%></a>
    </li>
    <li class="open">
        <a href="#open"><%=_("Open")%></a>
    </li>
    <li class="unzip">
        <a href="#unzip"><%=_("Unzip")%></a>
    </li>
</ul>

<div id="sbUploadDialog" class="modal fade" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><%=_("Click BROWSE to select file to Upload")%></h3>
    </div>
    <div class="modal-body">
        <!---<p id="sbExtList"><strong><%=_("Allowed file types:")%></strong> <span class="extList"></span></p>-->
        <form action="" method="post" enctype="multipart/form-data" >
            <input type="hidden" name="dir" id="data[dir]" />
        </form>
        <a href="#" id="sbUploadAdd"><%=_("Add File")%></a>
    </div>
    <div class="modal-footer">
        <button id="uploadButton" type="button" class="btn btn-primary"><%=_("Upload")%></button>
        <button id="closeButton" type="button" data-dismiss="modal" class="btn btn-default"><%=_("Close")%></button>
    </div>
</div>
<div class="sbContainer clearfix">
    <div class="sbToolbar btn-toolbar">
        <button class="btn upload"><%=_("Upload")%><span class="glyphicon glyphicon-upload"></span></button>
        <button class="btn new"><%=_("New Directory")%><span class="glyphicon glyphicon-folder-open"></span></button>
        <!--<button class="up btn btn-default" title="<%=_("Move Up")%>"><span class="glyphicon glyphicon-arrow-up"></span></button>-->
        <button class="btn rename"><%=_("Rename")%><span class="glyphicon glyphicon-edit"></span></button>
        <button class="btn delete"><%=_("Delete")%><span class="glyphicon glyphicon-remove"></span></button>
    </div>
    <div class="sbBreadcrumb"></div>
    <div class="sbListPanel">
        <ul class="sbList list-group"></ul>
    </div>
    <div class="statusPanel well well-sm"></div>
</div>