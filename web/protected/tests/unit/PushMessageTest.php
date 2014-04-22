<?php

class PushMessageTest extends UnitTesting
{
    public function init()
    {
        Yii::app()->getModule('push');
        $this->className = 'PushMessage';
    }
    public function testTest()
    {
        $this->allByDocExample();
    }
}