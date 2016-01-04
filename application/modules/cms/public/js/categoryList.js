    //update list view when one param is changed
    function updateList(paramName,paramValue){
        var params = $("#listContainer").data('params');
        params[paramName] = paramValue;
        $("#listContainer").hfbList({
            'params': params
        });
    }
    
    function getEditUrl(id, setId, language){
        return sprintf('/%s/cms/admin-category/category-edit/id/%d/set_id/%d/language/%s',CURR_LANG, id, setId, language);
    }
    
    function getDeleteUrl(id){
        return sprintf('/%s/cms/admin-category/category-delete/id/%d',CURR_LANG, id);
    }
    
    function getModulePageTypes(id){
        return sprintf('/%s/cms/admin-category/get-module-page-types/id/%s', CURR_LANG, id);
    }
    
    function getSetEditUrl(language){
        return sprintf("/%s/cms/admin-category/set-edit/language/%s", CURR_LANG, language);
    }
    
    function getCategoryEditUrl(){
        return sprintf('/%s/cms/admin-category/edit-main-category/',CURR_LANG);
    }
    
    function editSetDialog(){
        ajaxFormBS.dialog(getSetEditUrl($("#langFilter").val()),{
            onContent: function(dialog){
                $("#data\\[module\\]").change(function(){
                    modulePageTypes($(this).val());
                });                
            },
            onClose: function(res, data){                                
               if(res){
                   populateCategory(data.set, data.selected)
                   updateList('categorySetFilter', data.selected);
               }
            },
            width: '200px',
            height: 'auto'
        });
    }
    
    function editDialog(id){
        ajaxFormBS.dialog(getEditUrl(id, $("#categorySetFilter").val(), $("#langFilter").val()),{
            onContent: function(dialog){
                if(!$('#data-data-show_pic').attr("checked")){
                    $('.show_picture').css('display', 'none');
                }
                $('#data-data-show_pic').change(function() {
                    if($(this).is(':checked')) {
                        $('.show_picture').css('display', 'block');
                    } else {
                        $('.show_picture').css('display', 'none');
                    }
                });
                var fileInput = dialog.find("#data\\[data\\]\\[picture\\]");
                
                fileInput.each(function(){
                    var varId = $(this).data("var_id");                    
                    //assign options
                    var opts = {};
                    opts.fileWebRoot = fileWebRoot;
                    opts.activeModule = "cms";
                    $(this).imagebrowserdialog(opts);
                });
                
                populateCategoryItem(categoryItemData, $('#parent_selected').val() );

                $("#data\\[name\\]").blur(function(){
                    if($(this).val() != '' && $("#data\\[url_id\\]").val() == ''){
                        $.post("/" + CURR_LANG + "/cms/admin-category/url-id",{"name":$(this).val(),"lang":$("#langFilter").val()},
                        function(data){
                            $("#data\\[url_id\\]").val(data.url_id);
                        });
                    }
                });
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
    
    function populateCategoryItem(categoryItemData, selected){
        var thisId = $("#data\\[id\\]").val();
        var sel = $("#data\\[parent_id\\]");
        sel.empty();
        sel.append('<option value="0">' + 'No Parent ' + '</option>');
        if(categoryItemData){
            for (var i=0; i< categoryItemData.length; i++) {
                if(thisId == "" || thisId != categoryItemData[i].id){
                    var sele = (categoryItemData[i].id == selected)? 'selected="selected"' : '';
                    sel.append('<option value="' + categoryItemData[i].id + '"'+ sele +' style="padding-left:'+ categoryItemData[i].level * 15 +'px">' + categoryItemData[i].name + '</option>');
                }
            }
        }
    }
    
    function populateCategory(categoryFilterData, selected){
        var sel = $("#categorySetFilter");
        sel.empty();
        for (var i=0; i< categoryFilterData.length; i++) {
          var sele = (categoryFilterData[i].id == selected)? 'selected="selected"' : '';
          sel.append('<option value="' + categoryFilterData[i].id + '"'+ sele +'>' + categoryFilterData[i].name + '</option>');
        }
    }
    
    function populatePageTypes(pageTypes){
        var sel = $("#data\\[page_type_id\\]");
        sel.empty();
        for (var i=0; i< pageTypes.length; i++) {
          sel.append('<option value="' + pageTypes[i].id + '">' + pageTypes[i].name + '</option>');
        }
    }

    function categorySetExist(){
        var sel = $("#categorySetFilter");
        if(sel.val() != ""){
            return true;
        }else{
            return false;
        }
    }
    
//    function editMainCategory(){
//        $.ajax({
//            url: getCategoryEditUrl(),
//            type: "POST",
//            data: $("#categoryForm").serialize(),
//            success: function(res){
//                if(res.success){
//                    populateCategory(res.set, res.selected)
//                    
//                    updateList('categorySetFilter', res.selected);
//                    $.flashMessenger(res.message,{clsName:"ok"});
//                }else{
//                    var errors = {};
//                    ajaxFormBS.parseErrors('data',null,res['message'],errors);
//                    for(var field in errors){
//                        var errorUl = '<ul class="error">';
//                        for(var i = 0; i < errors[field].length; i++){
//                            errorUl += '<li>' + errors[field][i] + '</li>';
//                        }
//                        errorUl += '</ul>';
//                        $(ajaxFormBS.jqId(field)).parent().append(errorUl);
//                    }
//                }
//            }
//        });
//    }
    
    function modulePageTypes(id){
        $.ajax({
            url: getModulePageTypes(id),
            type: "GET",
            success: function(res){
                if(res.success){
                    populatePageTypes(res.page_types);
                }else{
                    var sel = $("#data\\[page_type_id\\]");
                    sel.empty();
                    sel.append('<option value="">' + res['message'] + '</option>');
                    $.flashMessenger(res.message,{clsName:"err"});
                    var errors = {};
//                    ajaxFormBS.parseErrors('data',null,res['message'],errors);
//                    for(var field in errors){
//                        var errorUl = '<ul class="error">';
//                        for(var i = 0; i < errors[field].length; i++){
//                            errorUl += '<li>' + errors[field][i] + '</li>';
//                        }
//                        errorUl += '</ul>';
//                        $(ajaxFormBS.jqId(field)).parent().append(errorUl);
//                    }
                }
            }
        });
    }
    
    function deleteCategory(id){
        $.ajax({
            url: getDeleteUrl(id),
            type: "POST",
            data: {id:id},
            success: function(res){
                if(res.success){
                    $.flashMessenger(res.message,{clsName:"ok"});
                    updateList();                    
                }else{
                    $.flashMessenger(res.message,{clsName:"ok"});
                }                
            }
        })
    }
    //init select filter
    $(document).ready(function(){
        initList('/' + CURR_LANG + '/cms/admin-category/category-list/?categorySetFilter='+$("#categorySetFilter").val(),{},function(me,data){
            $("#listContainer").find("a.edit").click(function(){
                editDialog($(this).data('id'));
                return false;
            });
            $("#listContainer").find("a.delete").click(function(){
                deleteCategory($(this).data('id'));
                return false;
            })
        });
//        $("#newCategory").click(function(){
//            $("#newCategory").hide();
//            $("#inputNewCategory").show();
//            return false;
//        });
//        $("#cancelCategory").click(function(){
//            $("#inputNewCategory").hide();
//            $("#newCategory").show();
//            $(".error").remove();
//            return false;
//        });
        $("#saveCategory").click(function(){
            
            $("#openModal").find(".error").remove();
            editMainCategory();
            $("#openModal").modal("hide");
            return false;
        })
        $("#categorySetFilter").change(function(){
            updateList('categorySetFilter',$(this).val());
        });
        
        
        
        $("#langFilter").change(function(){
            updateList('language',$(this).val());
        });
        $(".add").click(function(){
            if( categorySetExist() ){
                editDialog(null);
                return false;
            }else{
                alert(_("Please select Category Set."));
                return false;
            }
        });
        $(".addSet").click(function(){            
            editSetDialog();
        });
    });
    

