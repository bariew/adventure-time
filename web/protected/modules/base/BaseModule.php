<?php
/**
 * Module class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
Yii::import('application.modules.base.models.*');
Yii::import('application.modules.base.controllers.*');
Yii::import('application.modules.base.components.*');
Yii::import('application.modules.base.widgets.*');
Yii::import('application.modules.base.helpers.*');
Yii::import('application.modules.user.models.*');
/**
 * Base class extended by all application modules classes
 * @package application.components
 */
class BaseModule extends CWebModule
{
	public $label, $path, $group;
    
    public function init()
    {
        $this->setModules(array('user', 'page'));
    }
    
	public function adminMenu(){}
    /**
     * get absolute bath to module models directory
     * @return string path to models directory
     */
    public function getModelsPath()
    {
         return $this->basePath . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;
    }
    /**
     * gets model names list
     * @return array list of model names for this module
     */
    public function getModels()
    {
        if((!$path = $this->getModelsPath()) || !file_exists($path)){
            return array();
        }
        $files = CFileHelper::findFiles($path, array(array('php'), array(), 0));
        if(!$files){
            return array();
        }
        array_walk($files, function(&$var){
           $var = preg_replace('/(.*)\/(\w+)\.php/', '$2', $var);
        });
        return $files;
    }
}
