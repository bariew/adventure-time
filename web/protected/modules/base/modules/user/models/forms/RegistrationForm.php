<?php
/**
 * RegistrationForm class file.
 */

/**
 * user password registration form class
 * @package application.modules.user.models.forms
 */
class RegistrationForm extends UserItem
{
    /**
     * @var string password repeatition
     */
    public $password_repeat;
    /**
     * rules for validating model attributes 
     * @return array validation rules options
     */
    public function rules()
    {
        return array(
            array('name, email, password, password_repeat', 'required'),
            array('email', 'email'),
            array('email', 'unique'),
            array('name', 'length', 'max' => 255),
            array('password', 'compare'),
        );
    }
    /**
     * runs action before model save
     * changes password to its hash
     * @return boolean if model can be saved
     */
    public function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }
        $this->password = $this->hashPassword($this->password);
        return true;
    }
    /**
     * runs actions after model validation
     * clears password fields if not valid
     */
    protected function afterValidate()
    {
        parent::afterValidate();

        if ($this->hasErrors()) {
            $this->password = null;
            $this->password_repeat = null;
        }
    }
    /**
     * log user in by model
     * @return boolean wether user is authenticated
     */
    public function login()
    {
        $identity = new UserIdentityByModel($this);
        if ($identity->authenticate()) {
            $duration = 3600 * 24 * 30;
            Yii::app()->user->login($identity, $duration);
            return true;
        }
        return false;
    }

    public function tableName()
    {
        return "{{user_item}}";
    }
}
