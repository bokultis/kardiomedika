    var menuTree = {};
    var params = {};
    
    function menuListToTree(rows){
        menuTree = {};
        for(var i in rows){
            var row = rows[i];
            menuTree[row.id] = row;
            if(!menuTree[row.parent_id]){
                menuTree[row.parent_id] = {'items':{}};
            }            
            if(!menuTree[row.parent_id].items){
                menuTree[row.parent_id].items = {};
            }
            menuTree[row.parent_id].items[row.id] = menuTree[row.id];
        }
    }
    
    function renderItems(items){
        if(jQuery.isEmptyObject(items)){
            return '';
        }
        var rows = [];
        for(var i in items){
            rows.push(items[i]);
        }
        rows.sort(function(a, b){
            return a.ord_num - b.ord_num;
        });
        return $.fn.hfbList.tmpl ('items_tpl',{'rows': rows});
    }    
    
    function getEditUrl(id,lang,menu){
        if(!lang || lang == null || lang == undefined){
            return sprintf('/%s/cms/admin-menu/menu-edit/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,CURR_LANG,menu);
        }
        return sprintf('/%s/cms/admin-menu/menu-edit/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,lang,menu);
    }
    
    function getDeleteUrl(id,lang,menu){
        if(!lang || lang == null || lang == undefined){
            return sprintf('/%s/cms/admin-menu/menu-delete/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,CURR_LANG,menu);
        }
        return sprintf('/%s/cms/admin-menu/menu-delete/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,lang,menu);
    }
    
    function getMenuEditUrl(){
        return sprintf('/%s/cms/admin-menu/edit-main-menu/',CURR_LANG);
    }
    function editDialog(id, parent_selected){
            ajaxDialogForm.dialog(getEditUrl(id,$("#langFilter").val(),$("#menuFilter").val()),{
                onContent: function(dialog){
                    parent_selected = (parent_selected != '')?parent_selected:$('#parent_selected').val();
                    populateMenuItem(menuItemData, parent_selected, id);
                },
                onClose: function(success){
                    //firebug fix
                    if(success){
                        updateList();
//                        $("#listContainer").tableDnD();
                    }
                },
                width: 'auto',
                height: 'auto'
            });
    }
    
    function deleteDialog(id){
         if(!window.confirm("Are you sure you want to delete this menu item?\nThis operation has no undo. We recommend unpublishing this content instead of deleting.")){
                return false;
            }
            $.ajax({
                type: "POST",
                data: {"id":id},
                url: getDeleteUrl(id,$("#langFilter").val(),$("#menuFilter").val()),
                success: function(data) {
                    if(data['success']){
                        if(data['message']){
                            $.flashMessenger(data['message'],{clsName:"ok"});
                        }
                        //reload list
                        updateList();
                    }
                    else{
                        if(data['message']){
                            $.flashMessenger(data['message'],{clsName:"err"});
                        }
                    }
                },
                error: function(data) {
                    alert(_('An error has occured retrieving data!'));
                }
            })
            return false;            
    }
    
    function populateMenuItem(menuItemData, selected, id){
        var sel = $("#data\\[parent_id\\]");
        sel.empty();
        sel.append('<option value="0">' + 'No Parent ' + '</option>');
        for (var i=0; i< menuItemData.length; i++) {
            var sele = (menuItemData[i].id == selected)? ' selected="selected" ' : '';
            var styleSelfMenu = ''; 
            var selfMenu = '';
            if(menuItemData[i].id == id){
                styleSelfMenu = 'font-style: italic;';
                selfMenu = ' disabled="disabled" ';
            }
            sel.append('<option value="' + menuItemData[i].id + '"'+ sele + selfMenu + ' style="'+styleSelfMenu+'padding-left:'+ menuItemData[i].level * 15 +'px">' + menuItemData[i].name + '</option>');
        }
    }
    
    function populateMenu(menuFilterData, selected){
        var sel = $("#menuFilter");
        sel.empty();
        for (var i=0; i< menuFilterData.length; i++) {
          var sele = (menuFilterData[i].code == selected)? 'selected="selected"' : '';
          sel.append('<option value="' + menuFilterData[i].code + '"'+ sele +'>' + menuFilterData[i].name + '</option>');
        }
    }
    
    function editMainMenu(){
        $.ajax({
            url: getMenuEditUrl(),
            type: "POST",
            data: $("#menuForm").serialize(), 
            success: function(res){
                if(res.success){
                    populateMenu(res.menu, res.selected)
                    $("#inputNewMenu").hide();
                    $("#newMenu").show();
                    updateList('menuFilter', res.selected);
                    $.flashMessenger(res.message,{clsName:"ok"});
                }else{
                    var errors = {};
                    ajaxDialogForm.parseErrors('data',null,res['message'],errors);
                    for(var field in errors){
                        var errorUl = '<ul class="error">';
                        for(var i = 0; i < errors[field].length; i++){
                            errorUl += '<li>' + errors[field][i] + '</li>';
                        }
                        errorUl += '</ul>';
                        $(ajaxDialogForm.jqId(field)).parent().append(errorUl);
                    }
                }
            }
        });
    }
    
    //update list view when one param is changed
    function updateList(paramName,paramValue){
        params[paramName] = paramValue;
        renderTree();
    }
    
    function renderTree(){
        $.get(listUrl, params, function(data){
            menuItemData = data.rows;
            menuListToTree(data.rows);
            if (!renderItems(menuTree)){
                
                $("#menuTree").html($("#noMenuInfo").html());
            } else {
                $("#menuTree").html(renderItems(menuTree[0]['items']));
            
                $("#menuTree").find("a.edit").click(function(){
                    editDialog($(this).data('id'), '');
                    return false;
                });
                $("#menuTree").find("a.add").click(function(){
                    editDialog('',$(this).data('id'));
                    return false;
                });
                $("#menuTree").find("a.delete").click(function(){
                    deleteDialog($(this).data('id'));
                    return false;
                });  
            }         
        });        
    }
    
    //init select filter
    $(document).ready(function(){
        renderTree();
        $("#newMenu").click(function(){
            $("#newMenu").hide();
            $("#inputNewMenu").show();
            return false;
        })
        $("#cancelMenu").click(function(){
            $("#inputNewMenu").modal("hide");
            $("#newMenu").show();
            $(".error").remove();
            return false;
        })
        $("#saveMenu").click(function(){
            $(".error").remove();
            editMainMenu();
            return false;
        })
        $("#langFilter").change(function(){
            updateList('langFilter',$(this).val());
            $("#listContainer").tableDnD();
        });
        $("#menuFilter").change(function(){
            updateList('menuFilter',$(this).val());
            $("#listContainer").tableDnD();
        });
        $(".add").click(function(){
            editDialog(null);
            return false;
        });
    });
    

