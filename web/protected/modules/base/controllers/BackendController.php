<?php

class BackendController extends Controller
{
    public $layout = '//layouts/admin_menu';
    public $searchBar = true;
    
	public function beforeAction($action)
	{
		if(!parent::beforeAction($action)){
			return false;
		}
		if(!Yii::app()->user->level('admin')){
			throw new CHttpException(403, 'Access denied');
		}
		return true;
	}
	
    public function actions()
    {
        return array(
            'create'    => 'application.modules.base.controllers.actions.admin.CreateAction',
            'read'      => 'application.modules.base.controllers.actions.admin.ReadAction',
            'update'    => 'application.modules.base.controllers.actions.admin.UpdateAction',
            'delete'    => 'application.modules.base.controllers.actions.admin.DeleteAction',
            'index'     => 'application.modules.base.controllers.actions.admin.IndexAction',
            'change'    => 'application.modules.base.controllers.actions.admin.ChangeAction',
            'imageRemove'=>'application.modules.base.controllers.actions.admin.ImageRemoveAction',
            'imageUpload'=>array(
                'class'         => 'ext.redactor.actions.ImageUpload',
                'uploadPath'    => Yii::getPathOfAlias('webroot.files'),
                'uploadUrl'     => '/files',
                'uploadCreate'  => true,
                'permissions'   => 0777,
            ),
            'fileUpload'=>array(
                'class'         => 'ext.redactor.actions.FileUpload',
                'uploadPath'    => Yii::getPathOfAlias('webroot.files'),
                'uploadUrl'     => '/files',
                'uploadCreate'  => true,
                'permissions'   => 0777,
            ),
        );
    }    
    public function getMenu()
    {
        return array();
    }
    
	public function actionAdminSearch($adminSearch)
	{
		$model = new $this->modelName();
        $this->createTitles("{$this->modelName}: search results");
		if(!isset($model))
			return;
		if(isset($_GET['id'])){
			$_GET['category_id'] = $_GET['id'];
			unset($_GET['id']);
		}
		$model->importAttributes(@$_GET, true);
		$dataProvider = $model->globalAdminSearch($adminSearch);
		$dataProvider->pagination->pageSize = 30;
		$this->render('index', compact('dataProvider'));
	}
}
