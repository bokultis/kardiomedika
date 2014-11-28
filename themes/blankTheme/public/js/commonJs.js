$.fn.evenOdd = function() {                       
    this.filter(":even").addClass("even");
    this.filter(":odd").addClass("odd");
};


$.fn.preventLink = function($url) { 
    this.click(function() { 
        $temp = $(this).attr('href');
        if ($temp == $url ) {
            return false;
        } else {
            return true;
        }
    });
};
/*
$.fn.colorSelect= function() {
    $(this).children().each(function(){
        var tagType = $(this).prop("tagName");
        //--init
        var errorClassName = "form_field_error";
        var defaultClassName = "defaultColor";
        var selectedClassName = "selectedColor";
        //--end  init
        if (tagType == "SELECT"){//-------------------------------------------------------start of options for select tag
            var $temp = $(this).val();
                var errorClass = $(this).hasClass(errorClassName);
                if (errorClass == false ) {
                        if ($temp !== "") {
                                $(this).removeClass(defaultClassName).addClass(selectedClassName);
                        } else {
                                $(this).removeClass(selectedClassName).addClass(defaultClassName);
                        }
                };
                $(this).change(function(){
                        $temp = $(this).val();
                        if ($temp !== "") {
                                $(this).removeClass(defaultClassName).addClass(selectedClassName);
                        } else {
                                $(this).removeClass(selectedClassName).addClass(defaultClassName);
                        }
                });
        };
        if (tagType == "INPUT") {//-------------------------------------------------------start of options for input tag
                if ($(this).hasClass(errorClassName)) {
                        $(this).keypress(function(){
                          $(this).removeClass(errorClassName);
                   });
                }
        } 
        if (tagType == "TEXTAREA") { //---------------------------------------------------start of options for textarea tag
                if ($(this).hasClass(errorClassName)) {
                        $(this).keypress(function(){
                          $(this).removeClass(errorClassName);
                   });
                }
        } 
    });
};*/
$.fn.colorSelect2= function(defaultC, selectedC) {
    $temp = $(this).attr("value");
    if ($temp !== "") {
        $(this).css({'color': selectedC});
    } else {
        $(this).css({'color': defaultC});
    }
    $(this).change(function(){
        $temp = $(this).attr("value");
        if ($temp !== "") {
            $(this).css({'color': selectedC});
        } else {
            $(this).css({'color': defaultC});
        }
    });
};
$.fn.listToRows = function(ipr) {
    /*
    nol -> number of list items
    ipr -> items per row
    nor -> number of rows
    ul css -> display:table-row; needed for it to work
    */
    $ime = $(this).attr("class");
    $ime = "." + $ime;
    var ulWidth = $($ime).css("width");
    ulWidth = parseInt(ulWidth);
    var liHeight = $($ime + " li").css("height");
    liHeight = parseInt(liHeight);
    $($ime + " li").css({"position":"absolute"});
    var nol = $($ime + " li").length;
    var nor = nol/ipr;
    nor = Math.ceil(nor);
    var A =new Array(nol);
    var b = 0;
    for (i=0; i<nor; i++) {
        A[i] = new Array(ipr);
        for (j=0; j<ipr; j++){
        A[i][j] = b;
        b++;
        }
    }
    var i, j;
    for (i=0; i<nor; i++) {
    for (j=0; j<ipr; j++){
        var pom1 = liHeight*j;
        var pom2 = ulWidth*i;
        $($ime + " li:eq(" + A[i][j] + ")").css({"marginTop":pom1 +"px","marginLeft":pom2 + "px"});
        }
    }
}; 
$.fn.mobileMenu = function() {
    $(this).click(function(){
        var openBtn = $(this).attr("class");
        var openMenu = document.getElementById(openBtn);
        if ($(openMenu).hasClass("close")){
            $(openMenu).removeClass("close").addClass("open");
        } else if($(openMenu).hasClass("open")){
            $(openMenu).removeClass("open").addClass("close");
        }
    });
    
    
};


$(document).ready(function(){
    $("form select").colorSelect2("gray", "black");
    $(".to_nav").mobileMenu();
    
   
});