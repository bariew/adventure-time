<?php
class UserTest extends FunctionalTesting
{
    public function testLogin()
	{
        /*$email = 'fake@moqod.com';
        $roles = array('root', 'admin', 'company', 'user');
        $provider_id = 1;
        Yii::app()->user->setModel(false);
        // logout and check Login link
        $this->setPath('/logout')->checkAccess();
        $login = $this->setPath('/')->checkAccess()->page->filter('a:contains("Login")');
        $this->assertTrue($login->count() > 0, 'No login link');
        // go to login page and check title
        $this->page = $this->client->click($login->eq(0)->link());
        $this->checkTitles('Login');
        // fill and send false form; check title 
        $form = $this->page->selectButton('Login')->form();
        $form['LoginForm[email]'] = $email;
        $form['LoginForm[password]'] = '12345';
        $this->page = $this->client->submit($form);
        $this->checkTitles('Login');
        // fill and send true form; check title 
        $form = $this->page->selectButton('Login')->form();
        $form['LoginForm[email]'] = $email;
        $form['LoginForm[password]'] = 'xtreme789';
        // emulate login from different roles
        foreach($roles as $role){
            if(!$user = User::model()->findByAttributes(compact('role', 'provider_id'))){
                throw new Exception("User $role not found");
            };
            Yii::app()->user->setModel($user);
            $this->page = $this->client->submit($form);
            $this->checkTitles('Dashboard', $user->id);
        }*/
	}

    public function testMenu()
    {
        foreach($this->models['users']['native'] as $role=>$user){
            if(!$user){
                echo "Cannot find user with role {$role}";exit;
            }
            $this->setUser($user);
            if(!$page = $this->setPath('/user/user/index')->checkAccess(Yii::app()->user->level('company'))->page){
                return true;
            };
            $menu = $page->filter('div.sidebar a')->links();
            foreach($menu as $link){
                $this->setPath($link->getUri())->checkAccess();
            }
        }
    }
}