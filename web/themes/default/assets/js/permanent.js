$(document).ready(function(){
    initDocument();
});
$(document).bind('ajaxSuccess', function(){
   initDocument();
});
function initDocument(){
    $('.colorbox').colorbox();
}

/* AJAX FORMS */

//submit form - send files with ajax
$(document).on('submit', '#colorbox form:not(.noajax), form.ajax', function(event){
    event.preventDefault();
    ajaxFormUpload($(this), $(this).attr('action'), $(this));
    return false;
});
(function($){
    $.fn.maxWidth = function(){
        return Math.max.apply(Math, $(this).map(function(){ return $(this).width(); }).get());
    };
})(jQuery);
function ajaxFormUpload(form, action, target){
    form.css('opacity', 0.5);
    if(typeof(runProgressBar) == "function"){
        runProgressBar(form);
    }
    var formData = new FormData($(form)[0]);
    $.ajax({
        url: action, //server script to process data
        type: 'POST',
        xhr: function() { // custom xhr
            return $.ajaxSettings.xhr();
        },
        //Ajax events
        //beforeSend: beforeSendHandler,
        success: function(data){
            target.parent().removeClass('hide').show();
            target.replaceWith(data);
            $.colorbox.resize({width: $("#cboxLoadedContent *").maxWidth()});
            appendFileButton("", "file");
            form.css('opacity', 1);
        },
        error: function(){
            alert("Could not upload data");
            form.css('opacity', 1);
        },
        data: formData,
        //Options to tell JQuery not to process data or worry about content-type
        cache: false,
        contentType: false,
        processData: false
    });
}


/* url HISTORY */
// use for history browsing after history.pushState({url:url, target:target}, "", url)
$(window).on("popstate", function(event) {
    var state = event.originalEvent.state;
    if (state && state.url && state.targetSelector) {
        $.get(state.url, function(data){
            $('body').find(state.targetSelector).html(data);
        })
    }
});



/* PRELOADER */


var Preloader = {
    'id'        : 'preloader',
    'create'    : function(){
        if(this.isReady()){
            return;
        }
        var background = document.createElement('div');
        background.id = this.id;
        $(background).css({
            'position'  : 'fixed',
            'top'       : 0,
            'left'      : 0,
            'right'     : 0,
            'bottom'    : 0,
            'background-color' : '#fff',
            'z-index'   : 99,
            '-ms-filter':'"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"',
            'filter'    : 'alpha(opacity=50)',
            '-moz-opacity'  : '0.5',
            '-khtml-opacity': '0.5',
            'opacity'       : '0.5'
        });
        var image = document.createElement('div');
        image.id = 'status';
        $(image).css({
            'width'     : '200px',
            'height'    : '200px',
            'position'  : 'absolute',
            'left'      : '50%',
            'top'       : '50%',
            'background-image'      : 'url(/themes/default/assets/img/preloader.gif)',
            'background-repeat'     : 'no-repeat',
            'background-position'   : 'center',
            'margin'                : '-100px 0 0 -100px',
            'text-align'            : 'center'
        });
        background.appendChild(image); 
        document.body.appendChild(background);
    },
    'remove'    : function(){
        if(!this.isReady()){
            return;
        }
        $("#"+this.id).remove();
    },
    'run'       : function(){
        preloader = this;
        preloader.create();
        $(document).ajaxComplete(function(){
            preloader.remove();
        });
    },
    'isReady'   : function(){
        return document.getElementById(this.id);
    }
}


/*$('a:not(.btn)').live('mousedown', function(){
    $(document).ajaxStart(function(){
        Preloader.run();
    });  
})*/


/* SCROLL TOP */

$(document).ready(function() {
    $(window).scroll(function() {
        if(($(this).scrollTop() > 100)){
            if($('#goTop').length) return;
            var div = document.createElement('h3');
            div.id = 'goTop';
            document.body.appendChild(div);
            $('#goTop').css({
                top         : '-7px',
                opacity     : '0.15',
                position    : 'fixed',
                left        : '5px',
                cursor      : 'pointer'
            }).addClass("fa fa-arrow-circle-up");
        }else{
            $('#goTop').remove();
        }
    });
    $(document).on('click', '#goTop', function() {
        $('html, body').stop().animate({
            scrollTop: 0
        }, 500, function() {
            $('#goTop').remove();
        });
    });
});

/* MAPS */

var cities = {
    'IzhevskMap':{
        latitude:53.226365, 
        longitude:56.870504,
        title:'"Мебель в интерьер"<br>Ижевск, ул. Майская, 51, офис 16'
    }
};

//Карта 2gis
function loadMap(el){
    // Создаем объект карты, связанный с контейнером: 
    var myMap = new DG.Map(el.id);
    var latitude = el.latitude;
    var longitude = el.longitude;
    var title = el.title;
    // Устанавливаем центр карты: 
    myMap.setCenter(new DG.GeoPoint(latitude,longitude)); 
    // Устанавливаем коэффициент масштабирования: 
    myMap.setZoom(16); 
    // Добавляем элемент управления коэффициентом масштабирования: 
            myMap.controls.add(new DG.Controls.Zoom()); 
 
            // Создаем балун: 
    var myBalloon = new DG.Balloons.Common({ 
        // Местоположение на которое указывает балун: 
        geoPoint: new DG.GeoPoint(latitude,longitude), 
        // Текст внутри балуна: 
        contentHtml: title 
    }); 
    // Создаем маркер: 
    var myMarker = new DG.Markers.Common({ 
        // Местоположение на которое указывает маркер (в нашем случае, такое же, где и балун): 
        geoPoint: new DG.GeoPoint(latitude,longitude), 
        // Функция, которая будет вызвана при клике по маркеру: 
        clickCallback: function() { 
            // Если балун еще не был добавлен: 
            if (! myMap.balloons.getDefaultGroup().contains(myBalloon)) { 
                // Добавить балун на карту: 
                myMap.balloons.add(myBalloon); 
            } else { 
            // Если балун уже был добавлен на карту, но потом был скрыт: 
                // Показать балун: 
                myBalloon.show(); 
            } 
        } 
    }); 
    // Добавить маркер: 
    myMap.markers.add(myMarker);
    //document.getElementById('myMapId').id = ''; 
};