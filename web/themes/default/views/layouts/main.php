<!DOCTYPE html>
<!--[if lt IE 7 ]><html lang="en" class="no-js ie6"><![endif]-->
<!--[if IE 7 ]><html dir="ltr" lang="ru" class="no-js ie7 index"><![endif]-->
<!--[if IE 8 ]><html dir="ltr" lang="ru" class="no-js ie8 index"><![endif]-->
<!--[if IE 9 ]><html dir="ltr" lang="ru" class="no-js ie9 index"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html dir="ltr" lang="ru" class="no-js index"><!--<![endif]-->

<head>
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<?php
        $assetManager = Yii::app()->assetManager;
        $baseUrl = $assetManager->publish(Yii::getPathOfAlias('webroot.themes.'.Yii::app()->theme->name.'.assets'));
        Yii::app()->clientScript->registerCoreScript('jquery')
            ->registerCssFile($baseUrl  . '/js/colorbox/colorbox.css')
            ->registerCssFile($baseUrl  . '/css/bootstrap.css')
            ->registerCssFile($baseUrl  . '/css/style.css')
            ->registerScriptFile($baseUrl  . '/js/colorbox/jquery.colorbox.js')
            ->registerScriptFile($baseUrl  . '/js/permanent.js')
            ->registerScriptFile($baseUrl  . '/js/situational.js')

            ->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type', null)
            ->registerMetaTag(@$this->titles['page_keywords'], 'Keywords')
            ->registerMetaTag(@$this->titles['page_description'], 'Description')
        ;
        echo CHtml::tag('title', array(), @$this->titles['page_title']);
        echo CHtml::linkTag('shortcut icon', 'image/x-icon', '/favicon.ico');
        echo CHtml::linkTag('icon', 'image/x-icon', '/favicon.ico');
    ?>
</head>
  <body>
    <?php $this->widget('MainMenuWidget'); ?>
    <div class="container">
        <?php echo $content;?>
    </div>
  </body>
</html>