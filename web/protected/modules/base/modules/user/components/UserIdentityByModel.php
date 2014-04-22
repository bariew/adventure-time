<?php
/**
 * UserIdentityByModel class file.
 */
Yii::import('application.modules.base.modules.user.models.UserItem');
/**
 * identity component for authenticate user by given model
 * @package application.modules.user.components
 */
class UserIdentityByModel extends CUserIdentity
{
    /**
     * @var \UserItem temp user model
     */
    public $model;
    /**
     * @var integer temp user id
     */
    protected $_id;
    /**
     * constructs class instance
     * @param UserItem $model 
     */
    public function __construct(UserItem $model)
    {
        $this->model = $model;
    }
    /**
     * authenticates user
     * @return boolean if user is athenticated
     */
    public function authenticate() 
    {
        if ($this->model!==null) {
            $this->_id = $this->model->id;
            $this->username = trim($this->model->name);
            $this->errorCode = self::ERROR_NONE;
        } else {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return $this->errorCode === self::ERROR_NONE;
    }
    /**
     * gets user id
     * @return integer user id
     */
    public function getId() 
    {
        return $this->_id;
    }
}

