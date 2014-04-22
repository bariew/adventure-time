<?php
class ActiveRecordBehavior extends CActiveRecordBehavior
{
    /**
     * gets name of the behavior, listing its owner behaviors
     * and comparing ro itself
     * @return string name of this behavior
     */
    public function getName()
    {
        foreach(array_keys($this->owner->behaviors()) as $name){
            if($this->owner->$name == $this){
                return $name;
            }
        }
    }
    
    protected function get($attributeName)
    {
        return $this->owner->{$this->$attributeName};
    }

    protected function set($attributeName, $value)
    {
        return $this->owner->{$this->$attributeName} = $value;
    }
}
