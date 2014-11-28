

$(document).ready(function(){
    var dialog = ajaxLoader.getDialog();
    $(dialog).ajaxStart(function() {
        ajaxLoader.show();
    }).ajaxStop(function() {
        ajaxLoader.hide();
    });

    //assign loader to all standard links
    /*
    $("a").each(function(){
        var href = $(this).attr("href");
        if(href != "" && href != "#"){
            $(this).click(function(){
                ajaxLoader.show();
            });
        }
    });*/
});

var ajaxLoader = (function() {

    return { // public interface
        disabled: false,
        getDialog: function(){
            var div = $("#ajaxLoaderDialog");
            if(div){
                return div;
            }
            else{
                var id = 'ajaxLoaderDialog';
                var div = document.createElement("div");
                div.id = id;
                div.innerHTML = '<p align="center"><img src="/media/imgs/facebook-loader.gif" alt="Loading..." /></p>';
                div.style.display = 'none';
                document.body.appendChild(div);
                this.div = div;
                return $(div);
            }
        },
        show: function () {
            if(this.disabled){
                return;
            }
            var self = this.getDialog();
            //open dialog
            self.dialog({
                bgiframe: true,
                width: 240,
                height: 150,
                modal: true
            });
        },
        hide: function(){
            if(this.disabled){
                return;
            }
            this.getDialog().dialog('close');
        }
    }
})();