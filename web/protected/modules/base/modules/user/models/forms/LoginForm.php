<?php
/**
 * LoginForm class file.
 */

/**
 * user login form class
 * @package application.modules.user.models.forms
 */
class LoginForm extends FormModel
{
    /**
     * @var string user email
     */
    public $email;
    /**
     * @var string user password
     */
    public $password;
    /**
     * @var \UserIdentity athentication model
     */
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('email', 'required'),
            array('email', 'email'),
            // password needs to be authenticated
            array('password', 'authenticate', 'skipOnError' => true),
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate()
    {
        $this->_identity = new UserIdentity($this->email, $this->password);
        if (!$this->_identity->authenticate()) {
            if ($this->_identity->errorCode == UserIdentity::ERROR_USERNAME_INVALID) {
                $this->addError('email', 'Invalid E-mail');
            } elseif ($this->_identity->errorCode == UserIdentity::ERROR_PASSWORD_INVALID) {
                $this->addError('password', 'Wrong password');
            } elseif($this->_identity->errorMessage){
                $this->addError('password', $this->_identity->errorMessage);
            }else {
                $this->addError('password', 'Wrong pair e-mail - password.');
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->email, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 30; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        }
        else
            return false;
    }

}

