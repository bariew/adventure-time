<?php

class UnitTesting extends CDbTestCase
{
    public $className, $tester;
    
    /* COMMON TESTING */
    
    public function allByDocExample()
    {
        $this->tester = new DocTester($this->className);
        foreach($this->tester->methods as $methodDoc){
            if($methodDoc->getDeclaringClass()->name != $this->className){ // do not test inherited methods
                continue;
            }
            if(!is_array($methodDoc->tags) || array_diff(array('return', 'example'), array_keys($methodDoc->tags))){
                continue;
            }
            $this->tester->byExample($methodDoc->name);
        }
    }
    
    /* SYSTEM */
    
    public function init(){}
    
    public function __construct()
    {
        Yii::setPathOfAlias('webroot', dirname(__FILE__).'/../../');
        Yii::import('application.tests.extensions.DocTest.*');
        $this->init();
    }
}