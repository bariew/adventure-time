<?php 
class Widget extends CWidget
{
    public $module;
    public $view = 'view';

    //если не указано имя модуля, берем его из пути
    public function getModuleName()
    {
        if($this->module)
            return $this->module;
        
        $path = explode(DIRECTORY_SEPARATOR, $this->getViewPath());
        if(!$key = array_search('modules', $path))
            return 'site';

        return $path[$key+1];
    }
    //указываем путь к файлу view
    public function getPath($view=false, $ext='.php')
    {
        $theme = Yii::app()->theme->name;
        $view = $view ? $view : $this->view;
        
        if(strpos($view, '//') === 0){
            $view = substr($view, 2);
            $path = "webroot.themes.$theme.views.$view";
        }else if(strpos($view, '/') === 0){
            $view = substr($view, 1);
            $path = "webroot.$view";
        }else{
            $path = "webroot.themes.$theme.views.$this->moduleName.widgets.$view";
        }
        return Yii::getPathOfAlias($path).$ext;
    }
    // подменяем функцию render, если не хотим переписывать её на renderFile в виджетах
    public function render($view, $params=array(), $return=false)
    {
        $this->renderFile($this->getPath($view), $params, $return);
    }
	
	public function isActive($url)
	{
		return is_numeric(@strpos(Yii::app()->request->requestUri, $url));
	}
}