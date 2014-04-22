<?php
/**
 * WebUser class file.
 */
Yii::import('application.modules.base.modules..user.models.*');
/**
 * Yii user component
 * @package application.modules.user.components
 */
class WebUser extends CWebUser
{
    /**
     * @var \UserItem user model from DB
     */
    private $_model = null;
    /**
     * rated user roles list
     * @var array role values
     */
    public static $roleValues = array('root'=>0, 'admin'=>1, 'company'=>2, 'user'=>3, 'guest'=>4);
    /**
     * gets current user role
     * @return string role name
     */
    public function getRole()
    {
        return (($user=$this->getModel())
            ? $user['role'] : 'guest');
    }
    /**
     * checks if current user role equal or 'better' than gieven one
     * used for checkin user access
     * @param string $role role name
     * @return boolean if user role is equal or greater to given role
     */
    public function level($role)
    {
        return self::$roleValues[$this->role] <= self::$roleValues[$role];
    }
    /**
     * gets user model from database
     * @return \UserItem user model
     */
    public function getModel()
    {
        if($this->_model){
            return $this->_model;
        }
        if (!$this->isGuest AND $this->_model === null) {
            $this->_model = CActiveRecord::model('UserItem')->findByPk($this->id);
        }
        return $this->_model;
    }
    /**
     * sets user model by force (eg for testing purposes)
     * @param \UserItem $model user model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }
}