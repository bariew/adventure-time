<?php
/**
 * RecoveryForm class file.
 */

/**
 * user password recovery form class
 * @package application.modules.user.models.forms
 */
class RecoveryForm extends FormModel 
{
    /**
     * @var string user email
     */
    public $email;
    /**
     * rules for validating model attributes 
     * @return array validation rules options
     */
    public function rules() {
        return array(
            array('email', 'required'),
            array('email', 'email'),
        );
    }
    /**
     * sends email with password recovery link to users email
     * @param UserItem $user user model
     * @return boolean wether mail is sent
     */
	public function sendEmail($user)
	{
        $code = $this->generateRecoveryAuthCode($user);
        $message = 'Recovery url: ' . Yii::app()->createAbsoluteUrl(
            "/user/profile/passwordChange?i={$user->id}&c={$code}"
        );
        return Yii::app()->mailManager->send(array(
           'FromName'   => Yii::app()->name,
           'Body'       => $message,
           'Subject'    => "Password recovery",
           'to'         => $user->email,
        ));
	}
    /**
     * generates recovery code for user for one day
     * @param UserItem $user user mdoel
     * @return string recovery code
     */
    public function generateRecoveryAuthCode(UserItem $user)
    {
        $email = $user->email;
        $password = $user->password;
        // link will be valid for only today
        $timestamp = mktime(0, 0, 0);
        $result = sha1($email.$password.$timestamp);
        return $result;
        //return substr($this->stringToNumber($result), 0, 8);
    }
	/*
    protected function stringToNumber($string)
    {
        $result = '';
        foreach(str_split($string) as $char){
            $result .= is_numeric($char) ? $char : ord(strtolower($char))-95;
        }
        return $result;
    }
    */
    /**
     * creates new instance of self
     * @return \self self instance
     */
    public static function model()
    {
        return new self;
    }
}