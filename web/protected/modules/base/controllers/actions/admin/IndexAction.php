<?php
class IndexAction extends CAction
{
    public function run()
    {
        $this->controller->createTitles($this->controller->module->label);
        $model = $this->controller->getModel();
        $criteria = new CDbCriteria();
        foreach(array('item_id', 'category_id', 'parent_id') as $attribute){
            if(($value = @$_GET[$attribute]) && is_numeric($value)){
                $criteria->addColumnCondition(array($attribute=>$value));
            }
        }
		$dataProvider = new CActiveDataProvider($this->controller->modelName, array(
            'criteria'      => $criteria,
			'pagination'    => array('pageSize'=>30),
            'sort'          => array('defaultOrder'=>($model->hasAttribute('title')) ? 'title':'id DESC') 
		));
        $this->controller->render('index', compact('dataProvider'));
    }
}