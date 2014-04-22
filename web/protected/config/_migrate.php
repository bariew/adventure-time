<?php
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return  array(
    'class' => 'ext.migrate.EMigrateCommand',
    'migrationPath' => 'application.migrations',
    'migrationTable' => '{{migrations}}',
    'applicationModuleName' => 'core',
    'connectionID' => 'db',
);