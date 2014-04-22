<?php
/**
 * class extended by models - categories, having models-items
 * CategoryModel deletes its items in method afterDelete()
 */
class CategoryModel extends ActiveRecord
{
    /**
     * gets all category items from other models by its relations (HAS_MANY and HAS_ONE)
     * @return array category items
     */
    public function getCategoryItems()
    {
        $result = array();
        if(!method_exists($this, 'relations')){
            return $result;
        }
        foreach($this->relations() as $items=>$relation)
        {
            $relationType = $relation[0]; 
            switch($relationType){
                case self::HAS_MANY:
                    $result = array_merge($result, $this->$items);
                case self::HAS_ONE:
                    $result[] = $this->$items;
                break;
            }
        }
        return $result;
    }
    
    public function relations()
    {
        return array();
    }
    
    public function afterDelete()
    {
        foreach($this->getCategoryItems() as $item){
            if(!is_object($item)){
                continue;
            }
            $item->delete();
        }
        return true;
    }
}
