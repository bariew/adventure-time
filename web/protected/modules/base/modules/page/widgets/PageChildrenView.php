<?php

class PageChildrenView extends Widget
{
    public $title;
    public $view;
    public function run()
    {
        if(!$page = PageItem::model()->findByAttributes(array('title'=>$this->title))){
            return false;
        }
        $this->render($this->view, array('items'=>$page->visibleChildren()->findAll()));
    }
}
