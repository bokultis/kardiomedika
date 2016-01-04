/**
 * Flash messagenger global function
 */
function flashMessageAdd(message,messageType){
    $.flashMessenger(message,{
        clsName:messageType,
        modal:false,
        autoClose:true,
        position: "top",
        positionMargin: 50,
        wait:500
    });
}

(function ($, window, undefined){
    'use strict';
    
    var $doc = $(document),
    Modernizr = window.Modernizr,
    collapsedMenu = true;

    $(document).ready(function(){
        //flash messanger - parse static html
        $("#flashMessenger p").each(function(){
            flashMessageAdd($(this).text(), $(this).attr('class'));
        });

        //MAIN - METRO MENU
        $('nav.top-bar').metromenu();

        $("footer .toggle").click(function () {
            $(this).toggleClass("off");
            $(".leftCol").toggleClass("off");
            $(".rightCol").toggleClass("full");
        });
    });

    // Hide address bar on mobile devices (except if #hash present, so we don't mess up deep linking).
    if (Modernizr.touch && !window.location.hash) {
        $(window).load(function ()
        {
            setTimeout(function ()
            {
                window.scrollTo(0, 1);
            }, 0
            );
        }
        );
    }

}
)(jQuery, this);
