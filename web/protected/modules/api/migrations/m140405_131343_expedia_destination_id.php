<?php

class m140405_131343_expedia_destination_id extends EDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{expedia_destination_id}}', array(
            'id'        => 'pk',
            'destination_id'       => 'string',
            'name'     => 'string',
            'city'     => 'string',
            'state'     => 'string',
            'country'   => 'string',
            'latitude'  => 'string',
            'longitude' => 'string',
            'type'      => 'TINYINT(1) DEFAULT 2'
        ));
        
        
    }

    public function safeDown()
    {
        $this->dropTable('{{expedia_destination_id}}');
    }
}