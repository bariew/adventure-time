<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        'import' => array(
            'ext.wunit.*'
        ),
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'wunit' => array(
                'class' => 'WUnit'
            ),
			'db' => require dirname(__FILE__).'/_db.php',
		),
	)
);
