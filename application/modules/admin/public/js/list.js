//update list view when one param is changed
function updateList(paramName,paramValue){
    var params = $("#listContainer").data('params');
    params[paramName] = paramValue;
    $("#listContainer").hfbList({
        'params': params
    });
}

function initList(url,params,processFunc){
    if(!processFunc){
        var processFunc = function(me,data){
        }
    }
    $(document).ready(function(){
        //init list view
        var order = '';
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

        //default callback function when table body is obtained
        var tableProcessFunc  = function(me,data){
            me.find('tr').hover(
            function(){
                $(this).find('ul.hcms_menu_actions').show();
            },
            function(){
                $(this).find('ul.hcms_menu_actions').hide();
            })
            processFunc(me,data);
        }

        $("#listContainer").hfbList({
            'tpl': $('#records_tpl'),
            'url': url,
            'params': params,
            'process': tableProcessFunc,
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
    });

}

