<?php
class UpdateAction extends CAction
{
    public function run($id)
    {
        $model = $this->controller->getModel($id);
        $model->scenario = Yii::app()->user->role;
        $this->controller->createTitles($model->hasAttribute("title") ? $model->title : 'Update ' . $this->controller->modelName);
        if(($model->attributes = @$_POST[$this->controller->modelName]) && $model->save()){
            Yii::app()->user->setFlash('success', 'Saved');
            $this->controller->refresh();                
        }
        $this->controller->render('_form', compact('model'));
    }
}