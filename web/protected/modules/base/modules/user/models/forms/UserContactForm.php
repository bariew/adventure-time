<?php
/**
 * UserContactForm class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * user contact by email form class
 * @package application.modules.user.models.forms
 */
class UserContactForm extends FormModel 
{
    /**
     * @var string user email
     */
    public $email;
    /**
     * @var string mail subject
     */
    public $subject;
    /**
     * @var string mail content
     */
    public $message;
    /**
     *
     * @var \UserItem user model
     */
	public $user;
    /**
     * rules for validating model attributes 
     * @return array validation rules options
     */
    public function rules() {
        return array(
            array('message', 'required', 'message' => '{attribute} is required'),
            array('subject', 'length','max' => 255),
            array('message', 'length','max' => 65535),
           // array('email', 'email', 'allowEmpty' => false),
        );
    }
    /**
     * defines model attribute names
     * @return array attribute names
     */
    public function attributeLabels() {
        $labels = array(
            'email' 	=> 'E-mail',
            'subject' 	=> 'Subject',
            'message' 	=> 'Your message',
            'user[name]' 	=> 'Your message',
        );
		return ($labels);
    }
    /**
     * sends contact form email to user
     * @param boolean $validate if model need to be validated
     * @return wether email is sent
     */
    public function send($validate = true)
    {
       if ($validate AND !$this->validate()){
            return false;
       }
       
       return Yii::app()->mailManager->send(array(
           'FromName'   => Yii::app()->name,
           'Body'       => $this->message,
           'Subject'    => $this->subject,
           'to'         => $this->email,
        ));
     }
}
