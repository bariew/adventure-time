<?php
class IntegrationTest extends FunctionalTesting
{
    public function testLogin()
	{
        // LOGIN ROOT
        return;
        Yii::app()->user->setModel(false);
        $this->setPath('/logout')->checkAccess();
        $this->page = $this->setPath('/login')->checkAccess()->page;
        $this->checkTitles('Login');
        // fill and send false form; check title 
        $form = $this->page->selectButton('Log in')->form();
        $rootUser = User::model()->findByAttributes(array('role'=>'root'));
        $form['LoginForm[email]'] = $rootUser->email;
        $form['LoginForm[password]'] = 'xtreme789';
        $this->page = $this->client->submit($form);
        Yii::app()->user->setModel($rootUser);
        
        // CREATE PROVIDER
        /*
        $this->page = $this->setPath('/user/userProvider/create')->checkAccess()->page;
        $form = $this->page->selectButton('Save')->form();
        $form['UserProvider[title]']    = 'testProvider';
        $form['UserProvider[address]']  = 'localhost';
        $this->page = $this->client->submit($form);
        $this->checkTitles('Providers');
         */

        
        // CREATE PROVIDER ADMIN
        /*
        $provider = UserProvider::model()->findByAttributes(array('address'=>'localhost'));
        $this->page = $this->setPath('/user/user/create')->checkAccess()->page;
        $form = $this->page->selectButton('Register')->form();
        $form['User[name]']     = 'integration';
        $form['User[email]']    = 'integration@moqod.com';
        $form['User[role]']     = 'admin';
        $form['User[provider_id]']          = $provider->id;
        $form['User[new_password]']         = '12345';
        $form['User[new_password_repeat]']  = '12345';
        $this->page = $this->client->submit($form);
        $this->checkTitles('Users and companies');
         */
        
        // CREATE PROVIDER TIERS
        $admin = User::model()->findByAttributes(array('email'=>'integration@moqod.com'));
        Yii::app()->user->setModel($admin);
        $this->page = $this->setPath('/payment/paymentTier/create')->checkAccess()->page;
        $form = $this->page->selectButton('Save')->form();
        
	}
}