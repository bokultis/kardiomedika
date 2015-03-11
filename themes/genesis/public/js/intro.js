var menus = {
    'search': {
        'selector': '#search',
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


//slider init
var revapi;
jQuery(document).ready(function() {    
    //sequence slider
    var sliderSpeed = $('#sequence').data('speed')? $('#sequence').data('speed'): 5000;
    var options = {
        autoPlay: true,
        autoPlayDelay: 5000,
        pauseOnHover: false,
        nextButton: true,
        preloader: ".intro-load",
        hidePreloaderUsingCSS: true,
        hidePreloaderDelay: 50,
        navigationSkip: true,
        preventReverseSkipping: false,
        fadeFrameWhenSkipped: false,
        keyEvents: {
            left: "prev", 
            right: "next"
        }        
    };
    var sequence = $("#sequence").sequence(options).data("sequence");

    sequence.afterLoaded = function(){
        $(".sequence-next").fadeIn(500);
    };
    $(document).mousewheel(function(event, delta) {
        var left = $(".toggle").parent(".contactContainer").css("left");
        left = parseInt(left);
        if (delta === -1) {
            sequence.next();
        } else if (delta === 1) {
            sequence.prev();
        }
    });    
        
    //init all menus
    menuInit();    
    //outside click close open menus
    $("body").click(function(){
        menuCloseAll();
    });
    //side menu - close button
    $(".menu-overlay, .main-menu-close").click(function(){
        menuToggle('nav');
    });
    //disable mouse wheel in side menu
    $("nav[role='navigation']").mousewheel(function(event){
        event.stopPropagation();
    });     
});	//end ready

$(window).load(function(){
    //force custom search label
    var searchText = (CURR_LANG == 'de')? 'Suche': 'Search';
    $('.gsc-search-button').val(searchText);
});