<?php
/**
 * UserTest class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * unit test class for user module
 * @package application.tests.unit
 */
class UserTest extends UnitTesting
{
    /**
     * call actions on class init
     */
    public function init()
    {
        Yii::app()->getModule('push');
    }
    /**
     * test all defined models by their docs example code
     */
    public function testModels()
    {
        foreach(array('User', 'UserProvider', 'UserAccess') as $className){
            $this->className = $className;
            $this->allByDocExample();
        }
    }
}