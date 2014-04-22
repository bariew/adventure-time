<?php

class TreeHeirBehavior extends PTARBehavior
{
    /**
     * @var string owner extended models ids array
     */
    public $branch      = 'branch';
    public $ancestors   = 'ancestors';
    public $inheritAttributes = array();
    /**
     * @var ARTreeBehavior owner tree behavior
     */
    private $heir;
    private $parent;
    private static $usedTitles = array();

    public function getHeirs()
    {
        return $this->owner->findAll($this->getHeirCriteria());
    }

    public function getAllHeirs()
    {
        return $this->owner->findAll($this->getAllHeirCriteria());
    }

    protected function getAllHeirCriteria()
    {
        return new CDbCriteria(array('condition'=>"{$this->ancestors} LIKE '%,{$this->owner->primaryKey},%'"));
    }

    protected function getHeirCriteria()
    {
        return new CDbCriteria(array('condition'=>"{$this->branch} LIKE '%/{$this->owner->primaryKey}/'"));
    }

    public function afterSave($event)
    {
        parent::afterSave($event);
        if($this->owner->isNewRecord){
            $this->createHeirChildren();
        }else{
            $this->updateHeirs();
        }
    }

    /* UPDATE */

    public function updateHeirs()
    {
        if($this->attributesChanged(array('pid', 'rank'))){
            $this->moveHeirs();
        }
        if($this->attributesChanged($this->inheritAttributes)){
            foreach($this->getHeirs() as $heir){
                $this->legate($heir, false, false);
            }
        }
        return $this;
    }

    /* CREATE CHILDREN */

    public function createHeirChildren()
    {
        if(!$parent = $this->owner->tree->getParent()){
            return $this;
        }
        foreach($parent->treeInheritage->getHeirs() as $parentHeir){
            $this->legate(false, $parentHeir);
        }
        return $this;
    }

    /* MOVE */

    public function moveHeirs()
    {
        $oldPid     = $this->oldAttributes['pid'];
        $newPid     = $this->owner->pid;
        $newRank    = $this->owner->rank;
        return ($newPid == $oldPid)
            ? $this->simpleMove($newRank)
            : $this->treeMove($oldPid, $newPid);
    }

    protected function simpleMove($rank)
    {
        foreach($this->getHeirs() as $heir){
            $this->parent = $this->getNewModel(array("id"=>$heir->pid, "rank"=>$rank));
            $this->heir = $heir;
            $this->saveHeir();
        }
        return $this;
    }

    protected function treeMove($oldPid, $newPid)
    {
        $oldParent = $this->owner->findByPk($oldPid);
        $newParent = $this->owner->findByPk($newPid);
        if(!$commonParent = $this->getCommonParent($oldParent, $newParent)){
            return $this;
        };
        $movingBranch = str_replace($commonParent->url, "/", $this->oldAttributes['url']);
        $targetBranch = str_replace($commonParent->url, "/{$commonParent->id}/", $newParent->url);
        foreach($commonParent->treeInheritage->getAllHeirs() as $commonHeir){
            $criteria = new CDbCriteria();
            $criteria->addCondition("{$this->branch} LIKE '%{$targetBranch}'")
                ->addSearchCondition("url", $commonHeir->url);
            if(!$targetHeir = $this->owner->find($criteria)){
                continue;
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition("{$this->branch} LIKE '%{$movingBranch}'")
                ->addSearchCondition("url", $commonHeir->url);
            $heir = $this->owner->find($criteria);
            //echo "{$heir->id}, {$targetHeir->id}";exit;
            $this->legate($heir, $targetHeir);
        }
    }

    protected function getCommonParent($model1, $model2)
    {
        $ids1 = explode("/", preg_replace('/^\/(.*)\/$/', '$1', $model1->url));
        $ids2 = explode("/", preg_replace('/^\/(.*)\/$/', '$1', $model2->url));
        return ($commonIds = array_intersect_assoc($ids1,$ids2))
            ? $this->owner->findByPk(end($commonIds)) : false;
    }

    /* DELETE */

    public function afterDelete($event)
    {
        $this->deleteHeirs();
        parent::afterDelete($event);
    }

    public function deleteHeirs()
    {
        foreach($this->getHeirs() as $heir){
            $heir->delete();
        }
        return $this;
    }

    public function unlegate($ancestor_id)
    {
        $this->removeAncestor($ancestor_id);
        if(!$this->get('branch') && ($ancestor = $this->getAncestor())){
            $ancestor->treeInheritage->legate($this->owner);
        }
        $this->unlegateChildren($ancestor_id);
        $this->owner->save(false);
    }

    public function unlegateChildren($ancestor_id)
    {
        $items = $this->owner->tree->children;
        foreach($items as $item){
            $item->treeInheritage->unlegate($ancestor_id);
        }
    }


    /* ANCESTOR */

    public function getAncestors()
    {
        if(!$ids = $this->getAncestorIds()){
            return array();
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $ids);
        $criteria->order = "FIELD(t.id,".implode(',',$ids).")";
        return $this->owner->findAll($criteria);
    }

    public function getAncestor()
    {
        if(!$id = $this->getAncestorId()){
            return false;
        }
        return $this->owner->findByPk($id);
    }

    public function getAncestorId()
    {
        return ($ids = $this->getAncestorIds()) ? end($ids) : false;
    }

    private function getAncestorIds()
    {
        $ids = explode(',', $this->get('ancestors'));
        return array_filter($ids, function($var){
            return is_numeric($var);
        });
    }

    public function setAncestor($ancestor_id)
    {
        $this->removeAncestor($ancestor_id);
        $ids = $this->getAncestorIds();
        $ids[] = $ancestor_id;
        $this->setAncestors($ids);
    }

    private function removeAncestor($id)
    {
        if(!$ids = $this->getAncestorIds()){
            return true;
        }
        if($this->inBranch($id)){
            $this->set('branch', "");
            $id = $this->getAncestorId();
            //$this->setAncestors(array());
        }
        $pos = array_search($id, $ids);
        if(!is_numeric($pos)){
            return true;
        }
        unset($ids[$pos]);
        return $this->setAncestors($ids);
    }

    private function inBranch($id)
    {
        return is_numeric(strpos($this->get('branch'), "/{$id}/"));
    }

    private function setAncestors($ids)
    {
        $ancestors = ($ids) ? ",".implode(",", $ids)."," : "";
        $this->set('ancestors', $ancestors);
    }


    /* LEGATE */

    public function legate($heir, $parent=false, $withChildren=true)
    {
        $this->heir = $heir ? $heir : $this->getNewModel();
        $this->parent = $parent;
        if(!$this->canLegate()){
            return $this;
        }
        $this->legateAttributes()->saveHeir();
        return $withChildren ? $this->legateChildren() : $this;
    }

    /**
     * avoids loop heritage
     * @return bool whether legation is available
     */
    private function canLegate()
    {
        if($this->heir->url && strpos($this->heir->url, $this->owner->url) === 0){
            return false; // parent inheritance forbidden
        }
        if($this->isNew($this->owner) && $this->isNew($this->parent)){
            return false;
        }
        if($pid = @$this->parent->id){
            if(@self::$usedTitles[$pid][$this->get('title')]){
                return false;
            }
            self::$usedTitles[$pid][$this->get('title')] = true;
        }
        return true;
    }
    protected function legateChildren()
    {
        $heirChildren = $this->heir->tree->getChildren(array('index'=>'title'));
        $ancestorChildren = $this->owner->tree->getChildren(array('order'=>'rank'));
        foreach($ancestorChildren as $child){
            $child->treeInheritage->legate(@$heirChildren[$child->title], $this->heir);
        }
        return $this;
    }

    protected function legateAttributes()
    {
        $this->heir->treeInheritage->setAncestor($this->owner->id);
        $this->heir->{$this->branch}
            = (($branch = @$this->parent->{$this->branch}) ? $branch : '/') . $this->owner->primaryKey . '/';
        foreach($this->inheritAttributes as $attribute){
            $this->heir->$attribute =
                ($this->heir->isNewRecord || ($this->heir->$attribute == $this->getOldAttribute($attribute)))
                ? $this->owner->$attribute
                : $this->heir->$attribute;
        }
        return $this;
    }

    protected function saveHeir()
    {
        if($this->parent){
            $this->heir->tree->move($this->parent->primaryKey, $this->owner->rank);
        }else{
            $this->heir->save(false);
        }
        return $this;
    }
}