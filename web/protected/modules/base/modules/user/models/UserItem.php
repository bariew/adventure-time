<?php
/**
 * UserItem module UserItem class file.
 * @copyright (c) 2013, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Active Record to store user data
 * @package application.modules.user.models
 */
class UserItem extends ActiveRecord
{
	/* ATTRIBUTES AND LISTS */
    
    /**
     * temporary variable for new password value
     * @var string
     */
	public $new_password;
    /**
     * temporary variable for new password repeat value
     * @var string
     */    
    public $new_password_repeat;

    /**
     * checks wether user is not banned
     * @return boolean whether user is active
     * @example UserItem::model()->isActive() // false
     */
    public function isActive()
    {
        return $this->active ? true : false;
    }
    /**
     * user attached auth services list (Google, Facebook etc)
     * @return array user attached auth services list
     * @example UserItem::model()->getAttachedServiceList() // array()
     */
    public function getAttachedServiceList()
    {
        return CHtml::listData($this->authServices, 'id', 'service_name');
    }

    
    
    /* ACCESS SERVICE */
    
    
    /**
     * returns role list available for user models
     * @return array role=>roleTitle
     * @example UserItem::model()->roleList()
     */
    public function roleList()
    {
        return array(
            'admin'     => 'admin',
            'user'      => 'user'
        );
    }
    
    
    /* PASSWORD SERVICE */
    
    
    /**
     * encrypts password for database
     * @param string $password 
     * @return string hashed password
     * @example UserItem::model()->hashPassword('123') // '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
     */
    protected function hashPassword($password)
    {
        return sha1($password);
    }
    /**
     * encrypts given password
     * @uses UserItem::hashPassword()
     * @param string $pass password to set
     * @return string hashed password
     * @example UserItem::model()->setPassword('123') // '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
     */
    public function setPassword($pass)
    {
        return $this->password = $this->hashPassword($pass);
    }
    /**
     * validates if plain password equal to this model hashed password
     * @uses UserItem::hashPassword()
     * @param string $password password to validate
     * @return boolean if password is valid
     * @example UserItem::model()->validatePassword('')
     */
    public function validatePassword($password)
    {
        return $this->password == $this->hashPassword($password);
    }
    
    
	/* SYSTEM */
    
    
    /**
     * checks if user can login (user id active etc)
     * @return boolean if user can login
     */
    public function validateLogin()
    {
        if($this->role=='root'){
            return true;
        }
        if(!$this->isActive()){
            return false;
        }
        return true;
    }
    /**
     * searchs for users by given parameters
     * @return \CActiveDataProvider search results
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria
        ));
    }
    /**
     * rules for validating model attributes 
     * @return array validation rules
     */
	public function rules()
    {
        return array(
            array('email, name, new_password', 'required', 'except'=>array('root', 'admin', 'update')),
            array('email', 'email'),
            array('email', 'unique'),
            array('name, phone, new_password, new_password_repeat', 'length', 'max' => 255),
            array('new_password', 'compare'),
            array('role, active', 'safe', 'on'=>'root, admin'),
        );
    }
    /**
     * related ActiveRecords are available as this model attributes, named by this returned array keys
     * @return array
     */
	public function relations()
	{
        return array(
            'authServices'  => array(self::HAS_MANY, 'UserAuthService', 'user_id'),
		);
	}
    
    public function behaviors() 
    {
        return array_merge(parent::behaviors(), array(
            'imageBehavior' => array(
                'class' => 'AttachedFileBehavior',
                'field' => 'image',
                'fileFields'   => array(
                    'thumb1'    => array('adaptiveResize', 200, 200),
                    'thumb2'    => array('adaptiveResize', 100, 100)
                )
            )
        ));
    }
    /**
     * encrypts password before save
     * @uses UserItem::hashPassword()
     * @uses UserItem::setDefaultAttributes()
     * @return boolean if can save model
     */
    protected function beforeSave()
    {
        if (!parent::beforeSave())
            return false;
        if (!empty($this->new_password))
            $this->password = $this->hashPassword($this->new_password);
        return true;
    }
}
