function hrefId(name){
    var patterns = {
        ' ': '-',
        '(': '-',
        ')': '-',
        '/': '-',
        '\\': '-',
        '&':'-',
        '\.':'-'
    }
    for(var pattern in patterns){
        name = name.replace(new RegExp('\\' + pattern, 'g'), patterns[pattern])
    }
    return name;
}
    
function generateToc(){
    var headings = [], currLen = 0;
    
    $('#apiContent h2,#apiContent h3').each(function(){
        if($(this).hasClass('noToc')){
            return;
        }
        var id = hrefId($(this).text());
        $(this).attr('id',id);
        $(this).addClass('anchorFix')
        if($(this).prop("tagName").toLowerCase() == 'h2'){
            currLen = headings.push({
                heading: $(this),
                children: []
            });                
        }
        else{
            headings[currLen - 1].children.push($(this));
        }
    });
    var html = '<ul class="sidebar-nav nav">';
    for(var i in headings){
        html += '<li><a href="#' + hrefId(headings[i].heading.text()) + '">' + headings[i].heading.text() + '</a>';
        if(headings[i].children.length){
            html += '<ul class="nav">';
            for(var j in headings[i].children){
                html += '<li><a href="#' + hrefId(headings[i].children[j].text()) + '">' + headings[i].children[j].text() + '</a></li>';
            }
            html += '</ul>';                
        }
    }
    html += '</ul>';
    $("#apiToc").html(html);
    var sideNavPos = $('#apiToc').offset();
    $('#apiToc').affix({
        offset: {
            top: sideNavPos.top - 100
        }
    });
    var documentUrl = document.location.href.match(/(^[^#]*)/);
    $('#apiToc a').click(function(){
        window.location.href = documentUrl[0] + $(this).attr('href');
        return false;
    })
    $('body').scrollspy({
        target:'#apiToc',
        offset: 60
    });
}


$(document).ready(function(){
    //get headings - generate toc
    generateToc();
});

$(window).load(function(){
    prettyPrint();
});
