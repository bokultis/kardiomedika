//update list view when one param is changed
function updateList(paramName,paramValue){
    var params = $("#listContainer").data('params');
    params[paramName] = paramValue;
    $("#listContainer").hfbList({
        'params': params
    });
}

function getEditUrl(id, lang, boxCode, clone){
    if(!boxCode){
        boxCode = '';
    }
    if(!lang || lang == null || lang == undefined){
        lang = CURR_LANG;
    }
    var entityKey = (clone)? 'clone_id': 'id';
    return sprintf('/%s/teaser/admin-teaser/edit/%s/%d/langFilter/%s/box_code/%s', CURR_LANG, entityKey, id, lang, boxCode);
}

function getDeleteUrl(id){
    return sprintf('/%s/teaser/admin-teaser/delete/id/%d',CURR_LANG,id);
}

function getItemEditUrl(id, lang, boxCode, teaserId, clone){
    if(!teaserId){
        teaserId = '';
    }           
    if(!boxCode){
        boxCode = '';
    }
    var entityKey = (clone)? 'clone_id': 'id';
    if(!lang || lang == null || lang == undefined){
        lang = CURR_LANG;
    }
    return sprintf('/%s/teaser/admin-teaser/item-edit/%s/%d/langFilter/%s/box_code/%s/teaser_id/%s', CURR_LANG,entityKey, id, lang, boxCode, teaserId);
}

function getItemDeleteUrl(id){
    return sprintf('/%s/teaser/admin-teaser/item-delete/id/%d',CURR_LANG,id);
} 

function editItemDialog(id, boxCode, teaserId, clone){
    var openDialog = function(id, boxCode){
        ajaxFormBS.dialog(getItemEditUrl(id, $("#langFilter").val(), boxCode, teaserId, clone),{
            onClose: function(success){
                if(success){
                    updateList();
                }
            },
            onContent:function(dialog){
                setInputFieldDateTimePicker(dialog);
                var boxCode = dialog.find('#data\\[box_code\\]').val();
                //additional images                
                renderImages(dialog.find('#images'),boxCode);
                
                var setImages = function(images){
                    for(var imagePath in images){
                        for(var i = 0; i < images[imagePath].length; i++){
                            var imageKey  = images[imagePath][i];
                            dialog.find('#data\\[content\\]\\[' + imageKey + '\\]').val(imagePath);
                        }                                
                    }                    
                }
                //upload ZIP package
                $("#imagePackage").filebrowserdialog({
                    "fileWebRoot": fileWebRoot,
                    "activeModule": "teaser",
                    "extensions": "zip",
                    "onSelect": function(zipFile){
                        $.post("/" + CURR_LANG + "/teaser/admin-teaser/unzip", {
                            'box_code': boxCode,
                            'zip_file': zipFile
                        },function( result ) {
                            if(!result.success){
                                var message = (result.message)? result.message: 'Unknown error';
                                $.flashMessenger(message,{
                                    autoClose:false,
                                    modal:true,
                                    clsName:"err"
                                });                                
                                return false;
                            }
                            //set image paths
                            setImages(result.images);
                        });                        
                    }
                });
                //select folder
                $("#imageFolder").filebrowserdialog({
                    "fileWebRoot": fileWebRoot,
                    "activeModule": "teaser",
                    "extensions": "/",
                    "onSelect": function(folder){
                        $.post("/" + CURR_LANG + "/teaser/admin-teaser/dir-select", {
                            'box_code': boxCode,
                            'dir': folder
                        },function( result ) {
                            if(!result.success){
                                var message = (result.message)? result.message: 'Unknown error';
                                $.flashMessenger(message,{
                                    autoClose:false,
                                    modal:true,
                                    clsName:"err"
                                });                                
                                return false;
                            }
                            //set image paths
                            setImages(result.images);
                        }); 
                    }
                });
                
                //fallback
                $('#data\\[fallback\\]').change(function(){
                    if($('#data\\[fallback\\]').is(':checked')){
                        $('.scheduler').hide() ;
                    }
                    else{
                        $('.scheduler').show();
                    }                         
                });
                $('#data\\[fallback\\]').change();                
            },
            width: 'auto',
            height: 'auto'
        });        
    }
    if(!id && !boxCode){
        boxDialog(function(boxCode){
            openDialog(null, boxCode);
        });
    }
    else{
        openDialog(id, boxCode);
    }    
}

function updateMenuItems(dialog){
    if($('#data\\[all_menu_items\\]').is(':checked')){
        $('#menu_items').hide() ;
    }
    else{
        $('#menu_items').show();
    }    
}

function editDialog(id, clone){
    var openDialog = function(id, boxCode){
        ajaxFormBS.dialog(getEditUrl(id, $("#langFilter").val(), boxCode, clone),{
            onClose: function(success){
                if(success){
                    updateList();
                }
            },
            onContent:function(dialog){
                $('#data\\[all_menu_items\\]').change(function(){
                    updateMenuItems(dialog);
                });                
                $('#data\\[menu_item_ids\\]\\[\\]').chosen();                
                updateMenuItems(dialog);
                
            },
            width: 'auto',
            height: 'auto'
        });        
    }
    if(!id){
        boxDialog(function(boxCode){
            openDialog(null, boxCode);
        });
    }
    else{
        openDialog(id);
    }
}

function boxDialog(cb){
    $("#boxDialog" ).dialog({
        modal: true,
        buttons: {
            "OK": function() {
                if(!$("#dialogBoxCode").val()){
                    return false;
                }
                $( this ).dialog("close");
                cb($("#dialogBoxCode").val());
            },
            Cancel: function() {
                $( this ).dialog("close");
            }
      }    
    });
}

//default callback function when table body is obtained
function tableProcessFunc(me,data){
    me.find('tr').hover(
        function(){
            $(this).find('ul.hcms_menu_actions').show();
        },
        function(){
            $(this).find('ul.hcms_menu_actions').hide();
    });

    //teasers actions
    $("#listContainer").find("a.itemAdd").click(function(){
        editItemDialog(null, $(this).data('box_code'), $(this).data('id'));
        return false;
    });        
    $("#listContainer").find("a.edit").click(function(){
        editDialog($(this).data('id'));
        return false;
    });
    $("#listContainer").find("a.delete").click(function(){
        if(confirm(deleteConfirmation)){
            $.get(getDeleteUrl($(this).data('id')), function(result){
                if(result["success"]){
                    $.flashMessenger(result['message'],{clsName:"ok"});
                    $("#listContainer").hfbList();
                }
                else{
                    $.flashMessenger(result['message'],{
                        autoClose:false,
                        modal:true,
                        clsName:"err"
                    });
                }
            });
        }
        return false;
    });
    $("#listContainer").find("a.clone").click(function(){
        editDialog($(this).data('id'), true);
        return false;
    });

    $("#listContainer").find("a.preview").click(function(){
        var self = $(this);
        $("#previewDialog" ).dialog({
            modal: true,
            buttons: {
                "OK": function() {
                    //popup preview
                    if(!$("#preview_dt").val()){
                        return false;
                    }
                    var previewUrl = '/' + $("#langFilter").val() + '/teaser/preview/index/preview_teaser_id/' + self.data('id') + '/preview_dt/' + $('#preview_dt').val();
                    window.open(previewUrl, 'PREVIEW', "width=1024,height=800,scrollbars=no,resizable=yes,menubar=no");
                    $( this ).dialog("close");
                },
                Cancel: function() {
                    $( this ).dialog("close");
                }
        }    
        });        
        return false;
    });
    
    //items actions
    $("#listContainer").find("a.itemEdit").click(function(){
        editItemDialog($(this).data('id'));
        return false;
    });
    $("#listContainer").find("a.itemDelete").click(function(){
        if(confirm(deleteConfirmation)){
            $.get(getItemDeleteUrl($(this).data('id')), function(result){
                if(result["success"]){
                    $.flashMessenger(result['message'],{clsName:"ok"});
                    $("#listContainer").hfbList();
                }
                else{
                    $.flashMessenger(result['message'],{
                        autoClose:false,
                        modal:true,
                        clsName:"err"
                    });
                }
            });
        }
        return false;
    });
    $("#listContainer").find("a.itemClone").click(function(){
        editItemDialog($(this).data('id'), null, null, true);
        return false;
    });    
    //sort items
    $("#listContainer table.itemList tbody").sortable({
        items: 'tr:not([data-status="fallback"])',
        placeholder: "sortable-placeholder",
        update: function( event, ui ) {
            $parentTable = $(ui.item).parent('tbody');
            var sortOrder = [];
            $parentTable.find('tr').each(function(){
                sortOrder.push($(this).data('id'));
            });
            $.post("/" + CURR_LANG + "/teaser/admin-teaser/reorder", {
                'teaser_id': $parentTable.data('id'),
                'items': sortOrder.join(',')
            },function( result ) {
            });
        }
    });
    
    $("#listContainer .sliderCollapser").click(function(){
        if($(this).hasClass('show')){
            $(this).removeClass('show').addClass('hide');
        }
        else{
            $(this).removeClass('hide').addClass('show');
        }
    })
}

//pull image path from content
function getItemImage(item){
    var boxCode = item['box_code'];    
    if(!boxes[boxCode] || !boxes[boxCode]['params'] || !boxes[boxCode]['params']['images']){
        return false;
    }
    var imageKeys = boxes[boxCode]['params']['images'];
    for(var key in imageKeys){}
    //get the last - smallest image
    if(item['content'][key]){
        return item['content'][key];
    }    
    return false;
}

//render all defined images input boxes
function renderImages(container, boxCode){
    container.find(".customImage").remove();
    if(!boxes[boxCode] || !boxes[boxCode]['params'] || !boxes[boxCode]['params']['images']){
        return;
    }
    var imagesHtml = $.fn.hfbList.tmpl('images_tpl',{
        "images": boxes[boxCode]['params']['images'],
        "data"  : itemData
    });
    container.html(imagesHtml);
    container.find(".customTeaserImage").each(function(){
        var options = {
            "fileWebRoot": fileWebRoot,
            "activeModule": "teaser",
            "readonly": false
        };
        var customImageData = boxes[boxCode]['params']['images'][$(this).data("id")];
        var imgOptions = (customImageData['options'])? customImageData['options']: {};
        options = $.extend({}, options, imgOptions);
        $(this).imagebrowserdialog(options);
    });
}

//DT picker setup
function setInputFieldDateTimePicker(dialog){
    dialog.find("#data\\[start_dt\\]").datetimepicker({
        showOn: "button",
        buttonImage: "/modules/teaser/images/item/icon-calendar.svg",
        buttonImageOnly: true,
        dateFormat: picker,
        timeFormat: 'hh:mm',
        separator: ' ',
        onClose: function(dateText, inst) {

        }
    });
    dialog.find("#data\\[end_dt\\]").datetimepicker({
        dateFormat: picker,
        showOn: "button",
        buttonImage: "/modules/teaser/images/item/icon-calendar.svg",
        buttonImageOnly: true,
        timeFormat: 'hh:mm',
        separator: ' ',
        onClose: function(dateText, inst) {

        }
    });
 }
 
 //init DOM
$(document).ready(function(){
    $(".hcms_content #start_dt").datetimepicker({
        showOn: "button",
        buttonImage: "/modules/teaser/images/item/icon-calendar.svg",
        buttonImageOnly: true,
        dateFormat: picker,
        timeFormat: 'hh:mm',
        separator: ' ',
        onClose: function(dateText, inst) {

        }
    });
    $(".hcms_content #end_dt").datetimepicker({
        showOn: "button",
        buttonImage: "/modules/teaser/images/item/icon-calendar.svg",
        buttonImageOnly: true,
        dateFormat: picker,
        timeFormat: 'hh:mm',
        separator: ' ',
        onClose: function(dateText, inst) {

        }
    });
    $("#preview_dt").datetimepicker({
        showOn: "button",
        buttonImage: "/modules/teaser/images/item/icon-calendar.svg",
        buttonImageOnly: true,
        dateFormat: picker,
        timeFormat: 'hh:mm',
        separator: ' ',
        onClose: function(dateText, inst) {

        }
    });    
    $("#langFilter").change(function(){
        updateList('langFilter',$(this).val());
    });
    $("#filter").click(function(){
        var params = $("#listContainer").data('params');
        params['name'] = $('#name').val();
        params['start_dt'] = $('#start_dt').val();
        params['end_dt'] = $('#end_dt').val();
        params['fallback'] = $('#fallback').val();
        params['box_code'] = $('#box_code').val();
        params['menu_item_id'] = $('#menu_item_id').val();
        $("#listContainer").hfbList({
            'params': params
        });
        return false;
    })
    $(".add").click(function(){
        editDialog(null);
        return false;
    });
    
    //init list view
    var order = '', params = {};
    //find default order
    var ordering = $(".asc,.desc");
    if(ordering.length > 0){
        order = 'desc';
        if($(ordering).hasClass('asc')){
            order = 'asc';
        }
        order = $(ordering).data("column") + " " + order;
    }
    params = $.extend({}, params, {'order':order});    

    $("#listContainer").hfbList({
        'tpl': $('#records_tpl'),
        'url': listUrl,
        'params': params,
        'process': function(me,data){
            tableProcessFunc(me, data);
        },
        'pager': $('#pager')
    });
    
    //init search box
    $('#searchFilter').keypress(function(e) {
        if (e.keyCode == '13') {
            e.preventDefault();
            updateList("searchFilter",$(this).val());
        }
    });
    
    //search button
    $("#searchExecute").click(function(){
        updateList("searchFilter",$('#searchFilter').val());
        return false;
    });
    
    $('.sort').click(function(){
        var order = 'asc';
        if($(this).hasClass('asc')){
            order = 'desc';
        }
        $('.sort').removeClass('asc').removeClass('desc');
        $(this).addClass(order);
        updateList("order",$(this).data("column") + " " + order);
        return false;
    });
    
    //boxes lists
    $('#box_code').ddslick({
        onSelected: function(data){
            $('#box_code').val(data.selectedData.value);
        }
    });
    $('#dialogBoxCode').ddslick({
        height: '300px',
        onSelected: function(data){
            $('#dialogBoxCode').val(data.selectedData.value);
        }
    });    
});