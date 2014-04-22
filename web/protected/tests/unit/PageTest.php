<?php
/**
 * PageTest class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * unit test class for textPage module
 * @package application.tests.unit
 */
class PageTest extends UnitTesting
{
    /**
     * call actions on class init
     */
    public function init()
    {
        Yii::app()->getModule('textPage');
    }
    /**
     * test all defined models by their docs example code
     */
    public function testModels()
    {
        foreach(array('TextPageCategory') as $className){
            $this->className = $className;
            $this->allByDocExample();
        }
    }
}