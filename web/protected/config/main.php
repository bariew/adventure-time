<?php
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    /***  NAME OF APPLICATION  ***/
    'name' => 'Adventure time',
    'preload' => array('log', 'user'),
    'charset' => 'UTF-8',
    'sourceLanguage' => 'en',
    'language' => 'en',

    /***  THEME  ***/
    'theme' => 'default',
    
    'aliases' => array(
        'hotellook.composer.api' => 'ext.hotellook-yii-api',
    ),
    /***  IMPORT  ***/
    'import' => array(
        'application.modules.base.*',
    ),
    'modules' => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_modules.php',
    //'onBeginRequest' => array('GroupComponent', 'start'),
    /***  APPLICATION COMPONENTS  ***/
    'components' => array(
        'clientScript' => array(
          'class' => 'ext.minify.EClientScript',
          'combineScriptFiles' => false, 
          'combineCssFiles' => false, 
          'optimizeCssFiles' => true,  // @since: 1.1
          'optimizeScriptFiles' => true,   // @since: 1.1
            'packages'=>array(
                'jquery'=>array(
                    'baseUrl'=> '/themes/default/assets/js',
                    'js'=>array('jquery.js'),
                )
            ),
        ),
        'user' => array(
            'class' => 'user.components.WebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/user/profile/login'),
        ),
        'assetManager' => array(
            'class' => 'CAssetManager',
            'linkAssets' => true
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
        'urlManager' => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_urles.php',
        'errorHandler' => array(
            'errorAction' => 'page/default/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error', //, trace, info
                ),
                array(
                    'class'     => 'CFileLogRoute',
                    'levels'    => 'info, error',
                    'categories'=> 'api',
                    'logFile'   => 'api.log'
                ),
            ),
        ),
        'mailManager' => array(
            'class' => 'ext.mailer.MailManager',
        ),
        'db' => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_db.php',
        'bootstrap' => array(
            'class'         => 'ext.bootstrap.components.Bootstrap',
            'coreCss'       => false,
            'responsiveCss' => false,
            'plugins'       => array(
                'alert',
                'button',
                'carousel',
                'collapse',
                'dropdown',
                'model',
                'popover',
                'tab',
                'typeahead',
                'transition'    => false,
                'tooltip'       => array(
                    'selector'      => 'a.tooltip',
                    'options'       => array(
                        'placement'     => 'bottom',
                    ),
                ),
            ),
        ),
        'eauth' => array(
            'class' => 'ext.eauth.EAuth',
            'popup' => true,
            'services' => array(
                'google' => array(
                    'class' => 'GoogleOpenIDService',
                ),
                'yandex' => array(
                    'class' => 'YandexOpenIDService',
                ),
            ),
        ),
        'loid' => array(
            'class' => 'ext.lightopenid.loid',
        ),
        'hotellookApi' => array(
            'class' => 'ext.hotellook-yii-apicomposer.Agent',
            'host' => 'http://hotellook.com/api',
            'login' => "",
            'token' => "",
        ),
        'curl' => array(
            'class' => 'ext.Curl',
            'options' => array()
        ),
    ),
    /***  PARAMS  ***/
    'params' => array(
        'adminEmail'        => 'bariew@yandex.ru',
        'loginRedirect'     => '/',
        'ean'   => array(
            "Application"   => "adventure-time", 
            "Key"           => "mr73wuprne48yf6y53bymyvt",
            "Shared Secret" => "tYjzjsMz"
        ),
        "travelpayouts" => array(
            "token"     => "2f4be0779d7fcd9370d8b4c102b3d606",
            "marker"    => "31241"
        ),
    ),
);
