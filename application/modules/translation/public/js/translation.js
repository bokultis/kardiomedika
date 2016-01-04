/**
 * setup custom grid for extension
 */
function translationCustomGrid(gridId, pagerId){
    $( "#dialog_import_xls" ).dialog({
        autoOpen: false,
        height: 210,
        width: 400,
        modal: true,
        buttons: {
            Cancel: function() {
                    $( this ).dialog( "close" );
            }
        },
        close: function() {
            $(".error").remove();
            $("#xls").val("");
        }

    });
//    jQuery(gridId).jqGrid('bindKeys', {"onEnter":function( rowid ) { alert("You enter a row with id:"+rowid)} } ); 
    jQuery(gridId).jqGrid('navGrid',pagerId,
    {//options
        edit:true,
        add:true,
        del:true,
        search:true,
        refresh: true,
        cloneToTop:true
    },
    {//edit options
        closeAfterEdit: true,
        closeOnEscape:true,
        afterSubmit : jqGridAfterSubmit
    },
    {// add options
       closeAfterAdd: true,
       closeOnEscape:true,
        afterSubmit : jqGridAfterSubmit
    },
    {// del options
        reloadAfterSubmit:false,
        jqModal:false,
        closeOnEscape:true,
        afterSubmit : jqGridAfterSubmit
    }, 
    {// search options
        closeOnEscape:true,
        multipleSearch:false
    },
    {// view options
    } 
    ).navButtonAdd(pagerId,{
        caption:"Columns",
        title: "Reorder Columns",
        onClickButton: function(){
            jQuery(gridId).jqGrid('columnChooser',
                {
                    title:"test",
                }
            );
        },
        position:"first"
    })
    .navButtonAdd(gridId + '_toppager',{
        caption:"Columns",
        title: "Reorder Columns",
        onClickButton: function(){
            jQuery(gridId).jqGrid('columnChooser',
                {
                    title:"test"
                }
            );
        },
        position:"first"
    })
    .navButtonAdd(gridId + '_toppager',{
        caption:"Export to excel",
        title: "Export to excel",
        buttonicon:"ui-icon-exel-export",
        onClickButton: function(){
             exportToExcel(gridId);
        },
        position:"last"
    })
    .navButtonAdd(gridId + '_toppager',{
        caption:"Import from excel",
        title: "Import from excel",
        buttonicon:"ui-icon-exel-import",
        onClickButton: function(){
             importFromExcel(gridId);
        },
        position:"last"
    })
  
    function exportToExcel(gridId, id){
        var grid_params =   jQuery.toJSON(jQuery(gridId).jqGrid('getGridParam','postData')); 
//        alert(grid_params);
        ajaxDialogForm.dialog('/'+CURR_LANG + "/translation/admin/export-to-excel?grid_params="+urlencode(grid_params));
        return;
    }
    function importFromExcel(gridId, id){
            $( "#dialog_import_xls" ).dialog( "open" );
            return false;
    }
//default on close action for ajax dialog
    ajaxDialogForm.dialogOptions.onClose = function(success, data){
        if(success){
//            alert(data.data.toSource());
//            jQuery(gridId).trigger("reloadGrid");
            location.href= '/'+CURR_LANG  + "/translation/admin/save-excel/data/"+urlencode(jQuery.toJSON(data.data)); //+text(language_id)
        }
    }
//    ajaxDialogForm.dialogOptions.onBeforeSave = function(dialog){
//        var selectedForExport = $("#data\\[language_id\\]").val();
//        if(selectedForExport == null){
//            alert("Select the column you want to export")
//            return false;
//        }else{
//           return true; 
//        }
////        jQuery.inArray("John", arr)
//        
//    }
    ajaxDialogForm.dialogOptions.width = "450";
    ajaxDialogForm.dialogOptions.height = "400";
    ajaxDialogForm.dialogOptions.saveCaption = "Export";

    jQuery("#uploadXlsForm").submit(function(){
                return AIM.submit(this,{
                    'onStart' : function(){
                        if($("#xls").val() == ""){
                            $.flashMessenger("Please choose file.","warn");
                            return false;
                        }
//                        ajaxLoader.show();
                        $(".error").remove();
                        return true;
                    },
                    'onComplete' : function(data){
                        console.log(data);
                        eval("var data = " + data + ";");
                        if(data['success']){
//                            ajaxLoader.hide();
                            jQuery(gridId).trigger("reloadGrid");
                            $.flashMessenger(data['message'],"ok");
                            $("#dialog_import_xls").dialog('close');
                        }
                        else{
                            var errors = {};
                            ajaxDialogForm.parseErrors('data',null,data['message'],errors);
                            for(var field in errors){
                                var errorUl = '<ul class="error">';
                                for(var i = 0; i < errors[field].length; i++){
                                    errorUl += '<li>' + errors[field][i] + '</li>';
                                }
                                errorUl += '</ul>';
                                $("#xls").parent().append(errorUl);
                            }
                        }
//                        var params = $("#listContainer").data('params');
//                        $("#listContainer").hfbList({
//                            'params': params
//                        });
                    }
                }
            );
    });
}

function urlencode(str) {
    str = escape(str);
    str = str.replace('+', '%2B');
    str = str.replace('%20', '+');
    str = str.replace('*', '%2A');
    str = str.replace('/', '%2F');
    str = str.replace('@', '%40');
    return str;
}
