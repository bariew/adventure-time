<?php

class m131228_192357_page_create extends EDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{page_item}}', array(
            'id'        => 'pk',
            'pid'       => 'INT(11) DEFAULT 1',
            'rank'      => 'INT(11) DEFAULT 0',
            'title'     => 'string',
            'brief'     => 'text',
            'content'   => 'text',
            'name'      => 'string',
            'label'     => 'string',
            'url'       => 'string',
            'layout'    => 'string',
            'visible'   => 'TINYINT(1) DEfAULT 1',
            'page_title'        => 'string',
            'page_description'  => 'text',
            'page_keywords'     => 'text'
        ));
        $this->insert('{{page_item}}', array(
            'pid'       => 0,
            'title'     => 'Pages',
            'url'       => '/',
            'layout'    => 'index',
            'visible'   => 0
        ));
    }

    public function safeDown()
    {
        $this->dropTable('{{page_item}}');
    }
}