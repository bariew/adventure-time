<?php

return array(
    'native'    => array(
        'root'      => User::model()->findByAttributes(array('role'=>'root')),
        'admin'     => User::model()->findByAttributes(array('role'=>'admin', 'provider_id'=>1)),
        'company'   => User::model()->findByAttributes(array('role'=>'company', 'provider_id'=>1)),
        'user'      => User::model()->findByAttributes(array('role'=>'user', 'provider_id'=>1))
    ),
    'foreign'   => array(
        
    ),
    'fake'     => array(
        'root'      => false
    )
);
?>
