<?php
class PageItemController extends BackendController
{
    public $categoryName = 'PageItem';

    public function actions()
    {
        return array_merge(parent::actions(), array(
            'treeMove'      => 'ext.artree.actions.TreeMoveAction',
            'treeCreate'    => 'ext.artree.actions.TreeCreateAction',
            'treeUpdate'    => 'ext.artree.actions.TreeUpdateAction',
            'treeDelete'    => 'ext.artree.actions.TreeDeleteAction',
        ));
    }

    public function actionIndex()
    {
        $this->createTitles('Site pages');
		$dataProvider = new CActiveDataProvider($this->categoryName, array(
			'pagination' => array('pageSize'=>30)
		));
        $this->render('index', compact('dataProvider'));
    }

    public function actionUpdate($id)
    {
    	$this->createTitles('Update page');
        if(!$model = CActiveRecord::model($this->categoryName)->findByPk($id))
			throw new CHttpException(404);

        if (($model->attributes = @$_POST[get_class($model)]) && $model->save()) {
            Yii::app()->user->setFlash('success', 'Saved.');
            $this->refresh();
        }
		$this->render('_form', compact('model'), false, true);
    }

}
