<?php
class PageLocalMenu extends Widget
{
    public $htmlOptions = array();
    public $view = 'localMenu';
    public $pages;
    
    public function run()
    {
        if(!$items = $this->getPageitems()){
            return;
        }
        $this->render($this->view, compact('items'));
    }
	
    public function getPageItems()
    {
        if(!($page = $this->controller->page) && !$this->pages){
            return array();
        }
        if((!$pages = $this->pages) && (!$pages = $page->visibleChildren()->findAll())){
            $pages = $page->parent->visibleChildren()->findAll(array('condition'=>'pid > 1'));
        };
        $path = Yii::app()->request->pathInfo;
        $items = array();
        foreach ($pages as $page){
			$items[] = array(
                'label'     => $page['title'],
                'url'       => $page['url'],
                'active'    => $page['url'] == '/'.$path,
			);            
        }
        return $items;
    }
}
