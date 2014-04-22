<?php

class DocTester extends CDbTestCase
{
    public $className;
    public $reflection;
    public $methods = array();
    
    
    /* TESTING */
    
    public function byExample($methodName)
    {
        if(!$this->checkTags($methodName, array('return', 'example'))){
            return false;
        };
 
        $examples = $this->methods[$methodName]->tags['example'];
        $return = $this->methods[$methodName]->tags['return'][0];
        
        foreach($examples as $example){
            try{
                $result = eval('return '. $example['code'].';');
            }catch(Exception $e){
                return $this->assertTrue(0==1, "{$e->getCode()} {$e->getMessage()} in {$this->className}::{$methodName}()");
            };
            $this->assertTrue(
                 $this->checkExample($result, $example['result'], $return['type']), 
                 "Error in {$this->className}::{$methodName}(). Evaluated result: " . print_r($result, true)
            );            
        }
    }
    
    protected function checkTags($methodName, $tagName)
    {
        $tags = is_array($tagName)
            ? $tagName 
            : array($tagName);
        foreach($tags as $tag){
            if(!isset($this->methods[$methodName]->tags[$tag])){
                return false;
            }            
        }
        return true;
    }
    
    protected function checkExample($resultActual, $resultExpected, $returnType)
    {
        return ($resultExpected !== "")
            ? ($this->stringToValue($resultExpected) == $resultActual)
            : ($this->getType($resultActual) === $returnType);
    }
    
    protected function getType($var)
    {
        $result = gettype($var);
        return ($result === 'object')
            ? get_class($var) 
            : $result;
    }
    
    protected function stringToValue($string)
    {
        return eval("return {$string};");
    }
    
    /* GETTERS */
    
    public function getModel()
    {
        return new $this->className();
    }
    

    /* PREPARE */
    
    protected function processMethods()
    {
        foreach($this->reflection->getMethods() as $method){
            $this->methods[$method->name] = new MethodParser($this->className, $method->name);
        }
    }
    
    /* BASE */
    
    public function __construct($className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $className;
        $this->processMethods();
    }

    public function __get($attribute)
    {
        if(isset($this->$attribute)){
            return $this->$attribute;
        }
        $methodName = 'get' . ucfirst($attribute);
        if(method_exists($this, $methodName)){
            return $this->$methodName();
        }
        throw new Exception('Method or property does not exist');
    }
}