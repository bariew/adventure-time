<?php
class HLists extends CFileHelper
{
    protected $files = array();
    public function __construct()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lists';
        $this->files = $this->findFiles($dir);
        foreach($this->files as $file){
            $filename = $this->getFileName($file);
            $this->$filename = include_once $file;
        }
    }
    
    protected function getFileName($string)
    {
        return preg_replace('/(.*)\/(\w+)(\.\w+)$/', '$2', $string);
    }
    
    public static function model()
    {
        return new self;
    }
}