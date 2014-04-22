<?php
return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'useStrictParsing' => true,
    'rules' => array(
        /* ADMIN */
        '/admin' => 'page/pageItem/index',
        
        /* USER */
        '/login' => 'user/profile/login',
        '/logout' => 'user/profile/logout',
        
        '/api/<_a>'=>'api/apiDefault/<_a>',
        /* ALL */
        '/<_m>/<_c>/<_a>'=>'<_m>/<_c>/<_a>',
        //'/<_c>/<_a>'=>'<_c>/<_a>',
        
        /* PAGES */
        '/<route:\S+>'  => 'page/default/view',
        '/'             => 'page/default/view',
    ),
);