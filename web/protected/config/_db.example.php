<?php return array(
    'connectionString'      => 'mysql:host=localhost;dbname=kiki',
    'emulatePrepare'        => true,
    'username'              => 'root',
    'password'              => '',
    'charset'               => 'utf8',
    'tablePrefix'           => '',
    'enableProfiling'       => !defined('PRODUCTION'),
    'enableParamLogging'    => !defined('PRODUCTION'),
    'schemaCachingDuration' => defined('PRODUCTION') ? 3600 : 0,
);