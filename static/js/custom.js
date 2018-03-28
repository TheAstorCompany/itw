// DOM Ready
$(function() {

    var $el, leftPos, newWidth;
        $mainNav2 = $("#mainnavbar");

    /*
        mainnavbar
    */
    
    $mainNav2.append("<li id='magic-line'></li>");
    
    var $magicLine = $("#magic-line");
    
    $magicLine
        .width($(".current_page_item").width())
        .height($mainNav2.height())
        .css("left", $(".current_page_item a").position().left)
        .data("origLeft", $(".current_page_item a").position().left)
        .data("origWidth", $magicLine.width())
        .data("origColor", $(".current_page_item a").attr("rel"));
                
    $("#mainnavbar a").hover(function() {
        $el = $(this);
        leftPos = $el.position().left;
        newWidth = $el.parent().width();
        $magicLine.stop().animate({
            left: leftPos,
            width: newWidth,
            backgroundColor: $el.attr("rel")
        })
    }, function() {
        $magicLine.stop().animate({
            left: $magicLine.data("origLeft"),
            width: $magicLine.data("origWidth"),
            backgroundColor: $magicLine.data("origColor")
        });    
    });
    
    /* Kick IE into gear */
    $(".current_page_item a").mouseenter();
    
});