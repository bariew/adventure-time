<?php
/**
 * ProfileController class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * default user controller class
 * @package application.modules.user.controllers
 */
class ProfileController extends FrontendController
{
    /**
     * defines standart redirect url (fater login, registration etc)
     * @return string redirect url
     */
	public function getLoginRedirect()
	{
    	return Yii::app()->params['loginRedirect'];
	}
    /**
     * renders model view
     * @throws CHttpException 404
     */
    public function actionView()
    {
    	$this->createTitles("Your profile");
        if (!$model = Yii::app()->user->model)
            throw new CHttpException(404);
		$model->scenario = 'update';		
        if (($model->attributes = @$_POST[get_class($model)]) && $model->validate() && $model->save()) {
            Yii::app()->user->setFlash('success', 'Saved');
            $this->refresh();
        }
		$model->new_password = "";
        $this->render('_form', compact('model'));
    }
    /**
     * logs user in
     */
    public function actionLogin()
    {
        $this->createTitles('Login');
        $this->serviceLogin();
        $LoginForm = new LoginForm();
        if (($LoginForm->attributes = @$_POST['LoginForm']) && $LoginForm->validate()){
            Yii::app()->user->setFlash('success', 'You are logged in');
            $LoginForm->login();
            $this->refresh();
        }
        $User = Yii::app()->user->model;
        $this->render('login', compact('LoginForm', 'User'));
    }
    /**
     * athenticates user by auth service (Google, Facebook etc)
     * @uses Yii::app()->eauth
     * @return boolean if user is athenticated
     */
    public function serviceLogin()
    {
        if (!$service = Yii::app()->request->getQuery('service')) {
            return false;
        }
        $authIdentity = Yii::app()->eauth->getIdentity($service);
        $authIdentity->redirectUrl = $this->getLoginRedirect();
        $authIdentity->cancelUrl = $this->createAbsoluteUrl('/login');
        if (!$authIdentity->authenticate()){
            return false;
        }
        $authService = new UserAuthService();
        if ($authService->authenticate($authIdentity)) {
            $duration = 3600 * 24 * 30; // 30 days
            Yii::app()->user->login($authService->identity, $duration);
            $authIdentity->redirect();
        }else{
            $authIdentity->cancel();
        }
    }
    /**
     * logs user out
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        Yii::app()->user->setFlash('success', "You are logged out now");
        $this->redirect('/');
    }
    /**
     * registers user and logs him in
     */
    public function actionRegistration()
    {
        $this->createTitles('Registration');
        $RegistrationForm = new RegistrationForm();
        if (($RegistrationForm->attributes = @$_POST[get_class($RegistrationForm)]) && $RegistrationForm->save()){
            if ($RegistrationForm->login()){
                Yii::app()->user->setFlash('success', 'Thank you!');
//				$this->redirect($this->getLoginRedirect());
            }else{
            	$this->refresh();
            }
        }
        $User = Yii::app()->user->model;
        $this->render('registration', compact('RegistrationForm', 'User'));
    }
    /**
     * provides password recovery form ands send recovery link to users email
     */
    public function actionPasswordRecovery()
    {
        $this->createTitles('Password recovery');
        $RecoveryForm = new RecoveryForm();
        if (($RecoveryForm->attributes = @$_POST['RecoveryForm']) && $RecoveryForm->validate()) {
            if (!$user = UserItem::model()->findByAttributes(array('email'=>$RecoveryForm->email))){
                Yii::app()->user->setFlash('error', 'User with this email is not registered yet.');
            }elseif($RecoveryForm->sendEmail($user)){
            	Yii::app()->user->setFlash('success', 
                    'The link to password recovery has been sent to your email. It will be available till the end of the day');
            }else{
                Yii::app()->user->setFlash('error', "Couldn't send email");
            }
        }
        $this->render('passwordRecovery', compact('RecoveryForm'));
    }
    /**
     * provides user password change form checking athentication code sent to users email
     * by this->actionPasswordRecovery()
     * @param integer $i user id
     * @param string $c authentication code
     */
    public function actionPasswordChange($i, $c)
    {
        $this->createTitles('Password change');
        $user = CActiveRecord::model('User')->findByPk($i);
        $code = RecoveryForm::model()->generateRecoveryAuthCode($user);
        if ($code == null || $code!=$c) {
            $this->redirect(array('/error'));
        }

        $ChangePasswordForm = new ChangePasswordForm($user);
        if (($ChangePasswordForm->attributes = @$_POST['ChangePasswordForm']) && $ChangePasswordForm->validate()) {
            $ChangePasswordForm->saveNewPassword();
            $loginForm = new LoginForm();
            $loginForm->email = $user->email;
            $loginForm->password = $ChangePasswordForm->password;
            if ($loginForm->validate() && $loginForm->login()) {
                Yii::app()->user->setFlash('success', 'Password has been changed.');
                $this->redirect('/');
            } else {
                Yii::app()->user->setFlash('error', CHtml::errorSummary($loginForm));
                $this->redirect('login');
            }
        }
        $User = Yii::app()->user->model;
        $this->render('passwordChange', compact('ChangePasswordForm', 'User'));
    }
}
