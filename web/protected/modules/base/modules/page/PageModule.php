<?php
class PageModule extends BaseModule
{
	public $label = 'Страницы';
	public $path = array('/page/pageItem/index');

    public function init() 
    {
        $this->setImport(array(
            'application.modules.base.modules.page.models.*',
            'application.modules.base.modules.page.components.*',
        ));
    }
    
    public function adminMenu()
    {
        return PageItem::model()->find()->tree->menuWidget();
    }
}