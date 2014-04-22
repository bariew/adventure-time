<?php

class m130504_193945_user_item extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('{{user_item}}', array(
            'id'        => 'pk',
            'email'     => 'string',
            'password'  => 'string',
            'role'      => 'VARCHAR(255) DEFAULT "user"',
            'active'    => 'TINYINT(1) DEFAULT 1',
            'create_time'=> 'INT(11)',  
            'login'     => 'string',
            'name'      => 'string',
            'phone'     => 'string',
            'address'   => 'text',
            'image'     => 'string',
            'thumb1'    => 'string',
            'thumb2'    => 'string',
            'brief'     => 'text',
            'description'   => 'text'
        ), 'ENGINE=InnoDB');
        $this->insert('{{user_item}}', array(
            'id'        => 1,
            'email'     => 'bariew@yandex.ru',
            'password'  => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
            'role'      => 'root',
            'active'    => 1,
            'login'     => '',
            'name'      => 'Admin',
            'phone'     => '',
            'address'   => '',
            'image'     => '',
            'thumb1'    => '',
            'thumb2'    => '',
            'brief'     => '',
            'description'   => ''
        ));
	}

	public function safeDown()
	{
        $this->dropTable('{{user_item}}');
	}
}