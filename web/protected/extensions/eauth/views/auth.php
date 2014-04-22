<?php foreach ($services as $name => $service): ?>
	<?php $attached = in_array($service->id, $attachedServices); ?>
    <li class="authService <?php echo "{$service->id}_lc" . ($attached ? ' active' : '') ; ?>">
        <?php echo CHtml::link(
                '<span class="ico"></span>' . ucfirst($service->id),
                "/login?service={$name}", 
                array(
                	'class' => $service->id, 
                	'onclick'=> 'Javascript: eauthClick(this); return false;',
                	'title' => $attached ? "Remove service" : "Attach service" 
				)
           ); 
        ?>
    </li>
<?php endforeach; ?>
<script>
    function eauthClick(el) {
        var options = {
            google:{width:880, height:520},
            yandex:{width:900, height:550},
            twitter:{width:900, height:550},
            facebook:{width:585, height:290},
            vkontakte:{width:585, height:350},
        }
        var id = $(el).attr('class');
        var redirect_uri, url = redirect_uri = el.href;
        
        url += (url.indexOf('?') >= 0 ? '&' : '?') + 'redirect_uri=' + encodeURIComponent(redirect_uri);
        url += '&js';
        
        var centerWidth = ($(window).width() - options[id].width) / 2;
        var centerHeight = ($(window).height() - options[id].height) / 2;
        
        popup = window.open(url, "yii_eauth_popup", "width=" + options[id].width + ",height=" + options[id].height + ",left=" + centerWidth + ",top=" + centerHeight + ",resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes");
        popup.focus();
    };
</script>
