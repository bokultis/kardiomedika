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

<div id="sbUploadDialog" class="uploadDialog modal fade in" title='<%=_("Click BROWSE to select file to Upload")%>' style="display: none">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><%=_("Click BROWSE to select file to Upload")%></h4>
    </div>
    <div class="modal-body">      
        <p id="sbExtList"><strong><%=_("Allowed file types:")%></strong> <span class="extList"></span></p>         
        <form action="" method="post" enctype="multipart/form-data" >      
            <input type="hidden" name="dir" id="data[dir]" />
        </form>      
        <a href="#" id="sbUploadAdd"><%=_("Add File")%></a>    
    </div>
    <div class="modal-footer">   
        <button id="uploadButton" type="button" class="btn btn-primary">Upload</button>      
        <button id="closeButton" type="button" data-dismiss="modal" class="btn btn-default">Close</button>   
    </div>

</div>
<div class="sbContainer clearfix">
    <div class="sbToolbar">
        <button class="btn sb-component upload"><%=_("Upload")%><i class="fa fa-upload"></i></button>
        <button class="btn sb-component new"><%=_("New Directory")%><i class="fa fa-folder-open"></i></button>
        <button class="btn sb-component up"><%=_("Move Up")%><i class="fa fa-long-arrow-up"></i></button>
        <button class="btn sb-component rename"><%=_("Rename")%><i class="fa fa-pencil-square-o"></i></button>
        <button class="btn sb-component delete"><%=_("Delete")%><i class="fa fa-times"></i></button>  
    </div>
    <div class="sbBreadcrumb"></div>
    <div class="sbListPanel">
        <ul class="sbList"></ul>
    </div>
    <div class="statusPanel"></div>
</div>