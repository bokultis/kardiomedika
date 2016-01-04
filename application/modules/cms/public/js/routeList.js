    function getEditUrl(id,lang,path){        
        if((!lang || lang == null || lang == undefined) && module!='admin-redirect'){
            return sprintf('/%s/cms/'+module+'/edit/id/%d/langFilter/%s/path/%s',CURR_LANG,id,CURR_LANG,path);
        }
        return sprintf('/%s/cms/'+module+'/edit/id/%d/langFilter/%s/path/%s',CURR_LANG,id,lang,path);
    }
    
    function getDeleteUrl(id){
        return sprintf('/%s/cms/'+module+'/delete/id/%d',CURR_LANG,id);
    }
    
    function editDialog(id){
            ajaxDialogForm.dialog(getEditUrl(id,$("#langFilter").val(),$("#pathFilter").val()),{
                onContent: function(dialog){
                },
                onClose: function(success){
                    //firebug fix
                    if(success){
                        updateList();
                    }
                },
                width: 'auto',
                height: 'auto'
            });
    }
    
    
    //init select filter
    $(document).ready(function(){
        initList(listUrl,{},function(me,data){
            $("#listContainer").find("a.edit").click(function(){
                editDialog($(this).data('id'), '');
                return false;
            })
            $("#listContainer").find("a.add").click(function(){
                editDialog('');
                return false;
            })
            $("#listContainer").find("a.delete").click(function(){
                if(confirm(_("Are you sure you want to delete this route")+" ?")){
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
            });
            $("#langFilter").change(function(){
                updateList('langFilter',$(this).val());
            });
            $("#pathFilter").change(function(){
                updateList('pathFilter',$(this).val());
            });
            $(".add").click(function(){
                editDialog(null);
                return false;
            });
    });
    

