$(document).ready(function(){
    $('.mainMenu > li').each(function(index) {
        $(this).addClass('menu'+index);
    });
});


$(window).load(function() {
    
    $("nav ul.mainMenu li a").not("li.active").bind("mouseenter",function(){
        hideActiveMenu();
    });
    $("nav ul.mainMenu li.active a").bind("mouseenter",function(){
        showActiveMenu();
    });

    $("nav ul.mainMenu").bind("mouseleave",function(){
        showActiveMenu();
    });
    
});
function hideActiveMenu(){
        $("nav ul.mainMenu li.active").find("ul").css("display","none");    
    }
    
function showActiveMenu(){
    $("nav ul.mainMenu li.active").find("ul").css("display","block");
}

