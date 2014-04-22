$('document').ready(function(){
    appendFileButton();
    // SIDEBAR MENU LINKS
    fastLoad('.ajaxMenu a', '.ajaxContent');
})

function appendFileButton(){
    $('input[type=file]').hide().before("<a href='#' class='file btn' onclick='$(this).next().click(); return false;'>Select file</a>");
}


function sendSearchForm(el){
    var form = $(el);
    $.post(
        form.attr('action'), 
        form.serialize(), 
        function(data){
            form.parent().find(form.data('target')).html(data);
        }
    ); 
}

function sendEmailRequest(el){
    var href = $(el).attr('href') + "?" + $(el).parents('form').serialize();
    $.get(href, function(data){
        $.colorbox({'html':data});
    });
}

var fastLink;
var fastContent;
function fastLoad(linksSelector, containerSelector){
	var links 		= $(linksSelector);
	var container 	= $(containerSelector);
	links.mouseenter(function(){
		var href = $(this).attr('href');
		$.get(href, function(data){
	        fastContent = data;
	        if(href === fastLink){
	    		container.html(fastContent);
	    		window.history.pushState(null, null, fastLink);
	    		fastContent = fastLink = null;
	        }
	    });		
	})

    links.mousedown(function(){
    	fastLink = $(this).attr('href');
    	if(fastContent){
    		container.html(fastContent);
    		window.history.pushState(null, null, fastLink);
    		fastContent = fastLink = null;
    	}
    	$(linksSelector).removeClass('active');
    	$(this).addClass('active');
    });
    links.click(function(){
    	return false;
    })
}
