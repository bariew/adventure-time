<?php
/**
 * UserIdentity class file.
 */
Yii::import('application.modules.base.modules.user.models.*');
/**
 * identity component for authenticate user by email and password
 * @package application.modules.user.components
 */
class UserIdentity extends CUserIdentity 
{
    /**
     * @var integer temp user id
     */
    protected $_id;
    /**
     * authenticates user
     * @return boolean if user is athenticated
     */
    public function authenticate() 
    {
        $username = strtolower($this->username);
        $user = UserItem::model()->find('LOWER(email)=?', array($username));
        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if (!$user->validatePassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else if(!$user->validateLogin()){
            $this->errorMessage = "User is deactivated";
        }else {
            $this->_id = $user['id'];
            $this->username = trim($user['name']);
            $this->errorCode = self::ERROR_NONE;
        }

        return $this->errorCode === self::ERROR_NONE;
    }
    /**
     * gets user id
     * @return integer user id
     */
    public function getId() {
        return $this->_id;
    }
    /**
     * sets user id
     * @param type $id
     * @return integer $id user id
     */
    public function setId($id)
    {
        return $this->_id = $id;
    }
}

