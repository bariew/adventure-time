<?php

class PageMenuWidget extends CWidget
{
    public $htmlOptions = array();
    public $criteria = array();
    
    public function run()
    {
        $this->widget('ext.bootstrap.widgets.BootMenu', array(
            'items'         => $this->getPageitems($this->criteria),
            'htmlOptions'   => $this->htmlOptions
        ));
    }
	
    public function getPageItems($criteria)
    {
        $pages = PageItem::model()->visible()->findAllByAttributes(array('pid'=>1), $criteria);
        $path = Yii::app()->request->pathInfo;
        $items = array();
        foreach ($pages as $page){
			$items[] = array(
                'label'     => $page->title,
                'url'       => $page->url,
                'active'    => is_numeric(strpos('/'.$path, $page->url))
			);            
        }
        return $items;
    }
}
