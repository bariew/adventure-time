<?php
class DeleteAction extends CAction
{
    public function run($id, $confirm=false)
    {
        $model = $this->controller->getModel($id);
        $this->controller->createTitles('Delete ' . $this->controller->modelName);
        if(!$this->controller->isAjax){
            if($confirm){
                $model->delete();
                $this->controller->redirect(array('index'));
            }else{
                $this->controller->render('//default/delete', compact('model'));
            }
        }else{
            $model->delete();
        }
    }
}
