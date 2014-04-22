<?php
class CreateAction extends CAction
{
    public function run()
    {
        $this->controller->createTitles('Create ' . $this->controller->modelName);
        $model = $this->controller->getModel();
        $model->scenario = Yii::app()->user->role;
        if($model->hasAttribute('category_id') && is_numeric(@$_GET['category_id'])){
            $model->category_id = @$_GET['category_id'];
        }
        if(($model->attributes = @$_POST[$this->controller->modelName]) && $model->save()){
            Yii::app()->user->setFlash('success', 'Created');
            $this->controller->redirect(array('index', 'ajax'=>$this->controller->isAjax));
        }
        $this->controller->render('_form', compact('model'));
    }
}