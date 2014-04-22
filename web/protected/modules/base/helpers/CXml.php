<?php

class CXml 
{
    public $content;
    
    public function Tag($tagName)
    {
        return @$this->content->Document->{$tagName};
    }
    /* SYSTEM */
    
    public static function parse($path)
    {
        if(!$path){
            throw new Exception('No xml file');
        }
        $model = new self;
        if(!$model->content = @simplexml_load_file($path)){
            Yii::app()->user->setFlash('error', "Couldn't get xml file");
        };
        return $model;
    }
}