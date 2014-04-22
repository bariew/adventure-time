<?php
class ARRelationsBehavior extends CActiveRecordBehavior
{
    private $_relations = false;
    public $_parents = false;
    
    /* BELONGS TO */
    
    public function getParents()
    {
        $result = array();
        if($this->_parents !== false){
            return $this->_parents;
        }
        if(!$relations = @$this->relations[$this->key('BELONGS_TO')]){
            return $this->_parents = array();
        }
        foreach($relations as $options){
            $result[] = $this->owner->{$options->name};
        }
        return $this->_parents = $result;
    }
    
    /* HAS MANY */
    
    public function deleteChildren()
    {
        if(!$relations = @$this->relations[$this->key('HAS_MANY')]){
            return true;
        }
        
        foreach($relations as $name=>$relation){
            $model = CActiveRecord::model($relation->modelName);
            if(!$model->removeAfterParent){
                continue;
            }
            foreach($this->owner->{$name} as $child){
                $child->delete();
            }
        }
        return true;
    }
    
    /* MANY TO MANY */
    /**
     * delete all rows from relations data table for this ActiveRecord 
     * @return boolean
     */
    protected function deleteManyToMany()
    {
        if(!$relations = @$this->relations[$this->key('MANY_MANY')]){
            return;
        }
        foreach($relations as $options){
            return Yii::app()->db->createCommand("DELETE FROM {$options->tableName} WHERE {$options->attributeName} = {$this->owner->id}")->execute();
        }
    }
    
    
    /* OWNER SYSTEM */

    public function afterDelete($event) 
    {
        parent::afterDelete($event);
        $this->deleteManyToMany();
        $this->deleteChildren();
    }
    
    /* RELATIONS PARSING */
    
    public function getRelations()
    {
        if($this->_relations !== false){
            return $this->_relations;
        }
        if(!$relations = @$this->owner->relations()){
            return $this->_relations = array();
        }
        $result = array();
        foreach($relations as $name=>$options){
            $data = $this->parseRelation($name, $options);
            $result[$data->type][$name] = $data;
        }
        return $this->_relations = $result;
    }
    
    public function parseRelation($name, $options)
    {
        $type = $options[0];
        $result = array('name'=>$name, 'type'=>$type);
        switch($type){
            case $this->key('MANY_MANY'):
                $dbOptions = preg_split('/(?=[\(])/', $options[2]);
                $result['tableName'] = $dbOptions[0];
                $result['attributeName'] = preg_replace('/\((\S+),.+/', '$1', $dbOptions[1]);
                break;
            default: 
                $result['modelName'] = $options[1];
                $result['attributeName'] = $options[2];
        }
        
        return (object) $result;
    }
    
    public function key($name)
    {
        return constant("CActiveRecord::{$name}");
    }
}
