<?php
/**
 * ImageRemoveAction class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * admin model image delete action
 * @package application.controllers.actions.admin
 */
class ImageRemoveAction extends CAction
{
    /**
     * runs model image delete action
     * @param integer $id model id
     */
    public function run($id)
    {
        $model = $this->controller->getModel($id);
        $model->scenario = 'image';
        if($model->imageBehavior->deleteFiles()){
            echo "Successful";
        }else{
            echo "Couldn't delete image";
        }
    }
}