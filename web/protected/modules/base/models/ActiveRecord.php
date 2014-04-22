<?php
/**
 * ActiveRecord class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * base active record class for extending by appliaction models
 * @package application.models
 */
class ActiveRecord extends CActiveRecord
{

    
    /* ATTRIBUTES AND LISTS */

    public $removeAfterParent = true;
    /**
     * serialized attributes list for unserialize and serialize before save
     * @var array
     */
    public $serializedAttributes = array();
    /**
     * copies attributes values from given object
     * @param object $model object to import attribute values from
     * @param boolean $all if to import all or only safe attributes
     * @return boolean if attributes are imported
     */
	public function importAttributes($model, $all=false)
	{
		if(!$model){
            return false;
        }
        $attributes = ($all)
            ? array_keys($this->attributes)
            : $this->safeAttributeNames;

        foreach($attributes as $attribute){
            if(!isset($model[$attribute])){
                continue;
            }
            $this->$attribute = $model[$attribute];
        }
        return true;
	}
    /**
     * sets this attributes from given model attributes(aliases)
     * using array of corresponding attributes=>aliases
     * @param model $model
     * @param array $aliases
     */
    public function importAttributesAliased($model, $aliases)
    {
        foreach($aliases as $attribute=>$alias){
            $this->$attribute = $model[$alias];
        }
    }
    
    public function getSafeAttributes($addAttributes=array())
    {
        $keys = array_flip(array_merge($addAttributes, $this->safeAttributeNames));
        return array_intersect_key($this->attributes, $keys);
    }

    /**
     * unserializes model attributes
     * @param array $attributes attribute names to unserialize
     */
	public function unserializeAttributes($attributes)
	{
		foreach($attributes as $attribute){
            if(!$this->hasAttribute($attribute)){
                continue;
            }
			if($this->$attribute && is_string($this->$attribute)){
                $this->$attribute = @unserialize($this->$attribute);
            }
		}
	}
    /**
     * serializes model attributes
     * @param array $attributes attribute names to serialize
     */
	public function serializeAttributes($attributes)
	{
		foreach($attributes as $attribute){
			if(!isset($this->$attribute))
				continue;
			if(is_array($this->$attribute) || is_object($this->$attribute))
				$this->$attribute = serialize($this->$attribute);
		}
	}
    
    
    /* SYSTEM SERVICE */
    
    /**
     * gets new instance of this from database
     * @return object equal to this model but from database (refreshed)
     */
    public function refresh()
    {
        return $this->findByPk($this->primaryKey);
    }
    /**
     * adds attribute label to be nerged with attributeLabels()
     * @return array model attribute labels
     */
    public function addAttributeLabels()
    {
        return array();
    }

    
    /* SYSTEM */
    
    /**
     * searchs string in all model attributes
     * @param string $string search string
     * @return \CActiveDataProvider items relates to search query
     */
	public function globalAdminSearch($string)
	{
		$criteria = new CDbCriteria;
		foreach($this->attributes as $attribute=>$value){
			$criteria->addSearchCondition($attribute, $string, true, 'OR');
		}
		return new CActiveDataProvider($this, compact('criteria'));
	}
    
    public function searchByAttributes($attributes)
    {
        $this->attributes = $attributes;
        $criteria = new CDbCriteria();
        foreach($attributes as $attribute=>$value){
            $criteria->compare($attribute, $value, is_string($value));
        }
        return $this->find($criteria);
    }
    /**
     * rewrites parent attributeLabels method to translate labels
     * @param string $attribute attribute label to return
     * @return mixed array of attribute labels or label (if $attribute param is defined)
     * @uses ActiveRecord::addAttributeLabels() to add them to returned result
     */
    public function attributeLabels($attribute=false)
    {
        return array();
    }
    /**
     * attached models work as this model attributes
     * @return array of behaviors
     */
    public function behaviors()
    {
        return array(
            'relationsBehavior'     => array('class'=>'application.modules.base.components.ARRelationsBehavior'),
            'CAdvancedArBehavior'   => array('class' => 'application.extensions.CAdvancedArBehavior'),
        );
    }
    /**
     * runs actions after getting model data from database
     * @uses ActiveRecord::unserializeAttributes() to get unserialized attributes
     */
    protected function afterFind()
    {
        EventManager::model()->set($this);
        parent::afterFind();
        $this->scenario = @Yii::app()->user->model->role;
        $this->unserializeAttributes($this->serializedAttributes);
    }
    
    protected function afterConstruct() 
    {
        EventManager::model()->set($this);
        parent::afterConstruct();
    }
    /**
     * runs actions before model is saved
     * @return boolean if model is allowed to save
     * @uses ActiveRecord::serializeAttributes() to serialize attributes before save to db
     */
    protected function beforeSave() 
    {
        if(!parent::beforeSave()){
            return false;
        }
        $this->serializeAttributes($this->serializedAttributes);
        if($this->hasAttribute('create_time') && $this->isNewRecord){
            $this->create_time = time();
        }
        if($this->hasAttribute('update_time')){
            $this->update_time = time();
        }
        return true;
    }
    /**
     * name of database table
     * @return string
     */
    public function tableName()
    {
        $result = strtolower(
            implode('_', 
                preg_split('/(?=[A-Z])/', get_class($this), NULL, PREG_SPLIT_NO_EMPTY)
            )
        );
        return "{{{$result}}}";
    }
    /**
     * creates new instance of modelName with attributes
     * @param string $modelName model name
     * @param array $attributes model attributes
     * @return \modelName ActiveRecord
     */
    public static function create($modelName, $attributes = array())
    {
        $model = new $modelName;
        $model->importAttributes($attributes);
        return $model;
    }
    /**
     * gets instance of class
     * @param string $className
     * @return object instance of self
     */
    public static function model($className = false)
    {
        return parent::model($className ? $className : get_called_class());
    }
    /**
     * creates 'virtual' object of self
     * @param array $attributes object attributes
     * @return object self instance
     */
    public static function mock($attributes)
    {
        $model = self::model();
        foreach($attributes as $attribute=>$value){
            $model->$attribute = $value;
        }
        return $model;
    }
}