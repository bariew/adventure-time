<?php
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name'=>'console app',
    'commandMap' => array(
        'migrate' => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_migrate.php',
        'cron' => array(
            'class' => 'ext.W3CronCommand.W3CronCommand',
        ),
    ),
    'modules'   => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_modules.php',
    'components' => array(
        'db' => require dirname(__FILE__) . DIRECTORY_SEPARATOR . '_db.php',
        'mailManager' => array(
            'class' => 'ext.mailer.MailManager',
        ),
    ),
);