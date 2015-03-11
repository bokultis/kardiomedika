$(document).ready(function() {
    $(".download-items-menu").click(function(e) {
        e.preventDefault();
        if (!$(this).hasClass("active")) {
            $(".download-items-menu.active").removeClass("active");
            $(this).addClass("active");
            activeDiv = $(this).attr("href");
            $(".download-items.active").removeClass("active");
            $(activeDiv).addClass("active");
        }
    });
});

