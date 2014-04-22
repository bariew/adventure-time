<?php
class ChangeAction extends CAction
{
    public function run($id)
    {
        $model = $this->controller->getModel($id);
        $model->scenario = Yii::app()->user->role;
        $model->saveAttributes($_GET['attributes']);
    }
}