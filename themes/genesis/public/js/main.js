var menus = {
    'feedback':{
        'selector': '#feedback, .feedback-overlay',
        'btnSelector': '#feedbackToggle',
        'onClose': function(){
            $("html, body").removeClass("noscroll");
        },
        'onOpen': function(){
            $("html, body").addClass('noscroll');
        }
    },
    'search': {
        'selector': '#search, .search-overlay',
        'btnSelector': '#searchToggle'
    },
    'nav': {
        'selector': "nav[role='navigation'], .menu-overlay",
        'btnSelector': '#menuToggle',
        'onClose': function(){
            $("html, body").removeClass("noscroll");
        },
        'onOpen': function(){
            $("html, body").addClass('noscroll');
        }        
    }
}

function menuOpen(menu){
    //close all menus
    for(var currMenu in menus){
        if(menu != currMenu){
            menuClose(currMenu);
        }
    }
    //activate menu
    menus[menu].$el.addClass('on');
    menus[menu].$btn.addClass('on');
    if(menus[menu].onOpen){
        menus[menu].onOpen();
    }
}

function menuClose(menu){
    menus[menu].$el.removeClass('on');
    menus[menu].$btn.removeClass('on');
    if(menus[menu].onClose){
        menus[menu].onClose();
    }    
}

function menuCloseAll(){
    for(var menu in menus){
        menuClose(menu);
    }    
}

function menuToggle(menu){
    if(menus[menu].$el.hasClass('on')){
        menuClose(menu);
    }
    else{
        menuOpen(menu);
    }
}

function menuInit(){
    for(var menu in menus){
        menus[menu].$el = $(menus[menu].selector);
        menus[menu].$btn = $(menus[menu].btnSelector);
        menus[menu].$btn.data('menu',menu);
        menus[menu].$el.data('menu',menu);
        menus[menu].$btn.click(function(){
            menuToggle($(this).data('menu'));
            return false;
        });
        menus[menu].$el.click(function(e) {
            e.stopPropagation();
        });        
    }    
}

/* Jquery init */
$(document).ready(function(){
    var singleSlide = $('.bxslider li').length == 1;
    var sliderSpeed = $('.bxslider').data('speed')? $('.bxslider').data('speed'): 5000;
    //bx-slider function
    var mainSlider  = $('.bxslider').bxSlider({
        auto: !singleSlide,
        touchEnabled: true,
        pause: sliderSpeed,
        onSliderLoad: function(){
            $('.bxslider').css("visibility", "visible");
        }        
    });
    if(singleSlide){
        $('body').addClass('single-slide');
    }
    //slider might freez if resized during tranzition, so force reload
    $(window).resize(function(){
        if(mainSlider && mainSlider.reloadSlider){
            mainSlider.reloadSlider();
        }        
    });   
    //init all menus
    menuInit();
    //outside click close open menus
    $("body").click(function(){
        menuCloseAll();
    });
    //feedback tab
    $('.feedbackToggle').click(function(){
        menuToggle('feedback');
        return false;
    });
    //side menu - close button
    $(".menu-overlay, .main-menu-close").click(function(){
        menuToggle('nav');
    });
    //feedback menu close on blur
    $(".feedback-overlay").click(function(){
        menuClose('feedback');
    });
    $(".search-overlay").click(function(){
        menuClose('search');
    });
    $(".footer-toggle").click(function(){
        $("body, body > footer, .footer-toggle").toggleClass("footer-expanded");
    });

    //feedback form
    $("#horisenFeedback").ajaxSubmit({
        fieldSelector: '[name="data\\[{field}\\]"],.error-wrapper-{field}',
        onSubmit: function(data, errors){
            if(data.success){
                //reset form and close drawer
                $('#horisenFeedback').get(0).reset();
                //menuClose('feedback');
                //show message
                var message = data['message']? data['message']: 'Thank you';
                //alert(message);
                $(".feedbackContainer").addClass('flipped');
            }
            else{
                //alert(errors.join("\n"));
            }
        }
    });
    
    //video popup links
    $(".videoPopupLink").click(function() {        
        //for mobile no popup
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return true;
        }
        $.fancybox({
            'padding'           : 0,
            'autoScale'         : false,
            'transitionIn'      : 'none',
            'transitionOut'     : 'none',
            'title'             : this.title,
            'width'             : 1280,
            'height'            : 720,
            'href'              : this.href.replace(new RegExp("watch\\?v=", "i"), 'v/') + '?autoplay=1&controls=1&showinfo=0&rel=0&modestbranding=1',
            'type'              : 'swf',
            'swf'               : {
                'wmode'		: 'transparent',
                'allowfullscreen'	: 'true'
            }
        });

        return false;
    });    
});

//collapse elements
$(document).ready(function(){    
    //add arrow
    //$(".collapseContent > h3").append('<i class="fa fa-chevron-right collapseArrow"></i>');
    $(".collapseContent").each(function(){        
        $(this).data('id', Math.random().toString(36).substring(7));
        var $moreHandle = $('<a class="showMore" href="#"></a>');
        var $parentCollapse = $(this);
        $parentCollapse.append($moreHandle);
        $moreHandle.click(function(){
            //toggle status for all sibling boxes
            $(this).parents('.collapseContent').parent('.row').find('.collapseContent').each(function(){
                //check if not the originator
                if($(this).data('id') != $parentCollapse.data('id')){
                    $(this).collapsibleBox('setStatus', [!$parentCollapse.collapsibleBox('getStatus'),true]);
                }                
            });
        });        
    });
    
    $(".collapseContent").collapsibleBox({
        handleSelector: "a.showMore,h3",
        collapsibleSelector: ".collapseContentBox",
        openClass: "open",
        closedClass: "closed",
        onChange: function(el, closed){
        //console.log(el, "is closed: ", closed);
        },
        getIsClosed: function(widget){            
            //console.log("heights .collapseContent, h3, .collapseContentBox", widget.$el.height(), widget.$el.find("h3").outerHeight(true), widget.$el.find(".collapseContentBox").outerHeight(true));
            var height = widget.$el.height();
            var elementsHeight = widget.$el.find("h3").outerHeight(true) + widget.$el.find(".collapseContentBox").outerHeight(true);
            var closed = elementsHeight > height;
            return {
                "closed": closed,
                "toggable": closed 
            };
        }            
    });   
    
    //add arrow
    //$(".collapsibleSection > h2").before('<i class="fa fa-chevron-right collapseArrow"></i>');
    //$(".collapsibleSection > h3").before('<i class="fa fa-chevron-right collapseArrow"></i>');
    $(".collapsibleSection").collapsibleBox({
        handleSelector: ".collapseArrow,h2,h3",
        collapsibleSelector: ">div",
        openClass: "open",
        closedClass: "closed",
        getIsClosed: function(widget){
            return {
                toggable: true,
                closed: true
            }
        }        
    });            
});

$(document).ready(function(){
    $(".lang_selected_top a").bind('click', function(e){
        $(".lang_selector ul").fadeToggle("fast", "linear");
        e.stopPropagation();
        return false;
    });

    $('body').bind('click', function(e){
        $(".lang_selector ul").css("display", "none");
    });
});

$(window).load(function(){
    //force custom search label
    var searchText = (CURR_LANG == 'de')? 'Suche': 'Search';
    $('.gsc-search-button').val(searchText);
});