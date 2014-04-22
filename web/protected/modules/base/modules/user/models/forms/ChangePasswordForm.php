<?php
/**
 * ChangePasswordForm class file.
 */

/**
 * form model for changing user password
 * @package application.modules.user.models.forms
 */
class ChangePasswordForm extends FormModel
{
    /**
     * @var string user password
     */
    public $password;
    /**
     * @var string password repeatition
     */
    public $password_repeat;
    /**
     * @var string old user password
     */
    public $password_old;
    
    /**
     * @var \UserItem user model
     */
    protected $userModel;
    /**
     * creates new instance if self
     * @param UserItem $userModel user model
     */
    public function __construct(UserItem $userModel)
    {
        $this->userModel = $userModel;
    }
    /**
     * rules for validating model attributes 
     * @return array validation rules options
     */
    public function rules()
    {
        return array(
            array('password', 'required'),
            array('password', 'length', 'min' => 3, 'max' => 16),
            array('password_repeat', 'compare', 'compareAttribute' => 'password'),
            array('password_old', 'checkOldPassword', 'on' => 'changePassword'),
        );
    }
    /**
     * validates old password (if it is correct)
     * @param string $attribute attribute name 
     */
    public function checkOldPassword($attribute)
    {
        if (!$this->userModel->validatePassword($this->{$attribute})) {
            $this->addError($attribute, 'error oldpass');
        }
    }
    /**
     * defines model attribute names
     * @return array attribute names
     */
    public function attributeLabels()
    {
        $labels = array(
            'password' => 'Password',
            'password_repeat' => 'Repeat password',
            'password_old' => 'Old password',
        );
		return ($labels);
    }
    /**
     * saves new password
     * @return boolean if model is saved
     */
    public function saveNewPassword()
    {
        if ($this->validate() !== true) {
            return false;
        }
        $this->userModel->setPassword($this->password);
        return $this->userModel->save(false);
    }
}
