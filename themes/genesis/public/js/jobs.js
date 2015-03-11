$(document).ready(function() {
    if(!window.location.hash) {
        return;
    }
    $(window.location.hash + ' h2').click();    
});

