<?php

class ManyToManyWidget extends CWidget
{
    public $view = 'manyToMany';
    public $model;
    public $relation;
    
    public function run()
    {
        $this->render($this->view, array(
            'selected'  => $this->getSelected(),
            'all'       => $this->getAll()
        ));
    }
    
    protected function getAll()
    {
        if((!$relations = $this->model->relations()) || (!$relationData = @$relations[$this->relation])){
            return array();
        }
        $modelName = $relationData[1];
        $title = $modelName::model()->hasAttribute('title')
            ? 'title'
            : 'name';
        return CHtml::listData($modelName::model()->findAll(), 'id', $title);
    }
    
    protected function getSelected()
    {
        return ($post = @$_POST[get_class($this->model)][$this->relation])
            ? $post
            : CHtml::listData($this->model->{$this->relation}, 'id', 'id');
    }
}