// DOM Ready
$(function() {

    var $el, leftPos, newWidth;
        $mainNav2 = $("#mainnavbar");

    /*
        mainnavbar
    */
    var url = window.location.toString();

    if (url.match(/\/Front|\/Auth/) ==  null) { 
    	$mainNav2.append("<li id='magic-line'></li>");
    	var $magicLine = $("#magic-line");
    }
    
    
    
    try {
	    $magicLine
	        .width($(".current_page_item").width())
	        .height($mainNav2.height())
	        .css("left", $(".current_page_item a").position().left)
	        .data("origLeft", $(".current_page_item a").position().left)
	        .data("origWidth", $magicLine.width())
	        .data("origColor", $(".current_page_item a").attr("rel"));
    } catch (e) {};
                
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
    
    /* imitate jquery tabs */
    if ($('#tabs').length) {
    	$('#tabs').addClass('ui-tabs ui-widget ui-widget-content ui-corner-all');
    	$('#tabs ul').first().addClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all');
    	
    	$('#tabs ul').first().find('li').addClass(
    			'ui-state-default ui-corner-top'
    	).mouseenter(
    		function() {
    			$(this).addClass('ui-state-hover');
    		}
    	).mouseleave(
    		function() {
    			$(this).removeClass('ui-state-hover');
    		}
    	);
    	
    	$('#selected_tab').addClass('ui-tabs-selected ui-state-active');
    	$('#selected_tab a').attr('href', 'javascript:void(0);');
    }
    
});