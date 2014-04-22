<?php
Yii::import('application.modules.base.modules.page.models.*');
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    public $layout = 'main';

    public $breadcrumbs = array();
	public $menu;

    private $_page;
    private $_titles = array();

    public function beforeAction($action)
    {
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->createTitles();
        return true;
    }
    public function getTitles()
    {
        return ($this->page)
            ? $this->createTitles($this->page)
            : $this->_titles;
    }

    public function getPage()
    {
        return $this->_page = ($this->_page
            ? $this->_page
            : PageItem::getCurrentPage());
    }
    /**
     * Makes general titles, keywords etc variables for view metatags and titles
     * @param mixed $source Page model or array or string Title
     */
	public function createTitles($source = false)
	{
        $source = $source ? $source : $this->action->id;
        $keys = array('title', 'page_title', 'page_keywords', 'page_description');
		return $this->_titles = (is_string($source)
            ? array_fill_keys($keys, $source)
            : @$source->getSeoAttributes());
	}
    /**
     * @return boolean if request is ajax or it has $_GET['ajax'] set as true
     */
    public function getIsAjax()
    {
        return (@$_GET['ajax'] || Yii::app()->request->isAjaxRequest) ? 1 : 0;
    }
    
    public function render($view, $data = array(), $return = false, $processOutput=false)
    {
        $render = $this->isAjax ? 'renderPartial' : 'render';
        if($this->getIsAjax() && $processOutput){
            Yii::app()->clientScript->addPackage('jquery', false);
        }
        return parent::$render($view, $data, $return, $processOutput);
    }
    
    public function getModel($id=false, $modelName=false, $attributes=array())
    {
        if($modelName == false){
            $modelName = $this->modelName;
        }
        $model = (is_numeric($id))
            ? ActiveRecord::model($modelName)->findByPk($id)
            : ActiveRecord::create($modelName);
        if(!$model){
            throw new CHttpException(404, "Page not found");
        }
        foreach($attributes as $attribute=>$value){
            $model->$attribute = $value;
        }
        return $model;
    }
	
    public function getModelName()
    {
        return str_replace('Controller', '', get_class($this));
    }
	
    public static function model()
    {
        return new self;
    }
}
