<?php

class PTARBehavior extends CActiveRecordBehavior
{
    protected $_name;
    protected static $newIds = array();

    public $oldAttributes = array();

    public function afterSave($event)
    {
        if($this->owner->isNewRecord){
            $this->setNew();
        }
        parent::afterSave($event);
    }

    public function afterFind($event)
    {
        parent::afterFind($event);
        $this->oldAttributes = $this->owner->attributes;
    }

    protected function setNew()
    {
        self::$newIds[] = $this->owner->id;
    }

    public function isNew($model)
    {
        return in_array($model->id, self::$newIds);
    }

    protected function getNewModel($attributes = array())
    {
        $className = get_class($this->owner);
        $model = new $className();
        foreach($attributes as $name=>$value){
            $model->$name = $value;
        }
        return $model;
    }

    public function find($criteria, $all=false, $array=false)
    {
        $get = ($array)
            ? ($all ? 'queryAll'    : 'queryRow')
            : ($all ? 'findAll'     : 'find');
        return ($array)
            ? $this->owner->getCommandBuilder()
                ->createFindCommand($this->owner->tableSchema, $criteria)
                ->$get()
            : $this->owner->$get($criteria);
    }

    protected function get($attributeName)
    {
        return $this->owner->{$this->attr($attributeName)};
    }
	
	protected function getAll($attributeNames)
	{
		$result = array();
		foreach($attributeNames as $attributeName){
			$result[$attributeName] = $this->get($attributeName);
		}
		return $result;
	}
	
    protected function getOldAttribute($attributeName)
    {
        return @$this->oldAttributes[$this->attr($attributeName)];
    }

    protected function set($attributeName, $value)
    {
        $this->owner->{$this->attr($attributeName)} = $value;
        return $this;
    }

    public function attr($attributeName)
    {
        return isset($this->$attributeName) ? $this->$attributeName : $attributeName;
    }

    public function attributesChanged($attributes)
    {
        foreach($attributes as $attribute){
            if($this->get($attribute) !== $this->getOldAttribute($attribute)){
                return true;
            }
        }
        return false;
    }

    public function getName()
    {
        if($this->_name){
            return $this->_name;
        }
        foreach($this->owner->behaviors() as $name=>$options){
            if($this->owner->$name == $this){
                return $this->_name = $name;
            }
        }
        return false;
    }
}