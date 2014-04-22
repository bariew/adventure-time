<?php
class ReadAction extends CAction
{
    public function run($id)
    {
        $model = $this->controller->getModel($id);
        $this->controller->createTitles($model->hasAttribute("title") ? $model->title : 'Read ' . $this->controller->modelName);
        $this->controller->render('read', compact('model'));
    }
}
?>
