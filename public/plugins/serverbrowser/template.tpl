<ul id="sbMenu" class="contextMenu">
    <li class="copy">
        <a href="#copy">Copy</a>
    </li>
    <li class="paste">
        <a href="#paste">Paste</a>
    </li>
    <li class="open">
        <a href="#open">Open</a>
    </li>
    <li class="unzip">
        <a href="#unzip">Unzip</a>
    </li>
</ul>
<div id="sbUploadDialog" class="uploadDialog" title="Click BROWSE to select file to Upload" style="display: none">
    <form action="" method="post" enctype="multipart/form-data" >
        <input type="hidden" name="dir" id="data[dir]" />
    </form>
    <a href="#" id="sbUploadAdd">Add File</a>
</div>
<div class="sbContainer clearfix">
    <div class="sbToolbar">
        <button class="upload">Upload</button>
        <button class="new">New Directory</button>
        <button class="up">Move Up</button>
        <button class="rename">Rename</button>
        <button class="delete">Delete</button>        
    </div>
    <div class="sbBreadcrumb"></div>
    <div class="sbListPanel">
        <ul class="sbList"></ul>
    </div>
    <div class="statusPanel"></div>
</div>