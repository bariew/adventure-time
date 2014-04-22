<?php

class m130528_092811_user_authservice extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('{{user_authservice}}', array(
            'id'            => 'pk',
            'user_id'       => 'INT(11)',
            'service_name'  => 'string',
            'service_id'    => 'string',
        ));
	}

	public function safeDown()
	{
        $this->dropTable('{{user_authservice}}');
	}
}