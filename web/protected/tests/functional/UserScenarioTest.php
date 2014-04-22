<?php
class UserScenarioTest extends ScenarioTesting
{
    public function testLogin()
	{
        $email = 'fake@moqod.com';
        $roles = array('root', 'admin', 'company', 'user');
        $provider_id = 1;
        Yii::app()->user->setModel(false);
        // logout and check Login link
        $login = $this->path('/logout')->page->filter('a:contains("Login")');
        $this->assertTrue($login->count() > 0, 'No login link');
        // go to login page and check title
        $this->page = $this->client->click($login->eq(0)->link());
        $this->checkTitles('Login');
        // fill and send false form; check title 
        $form = $this->page->selectButton('Log in')->form();
        $form['LoginForm[email]'] = $email;
        $form['LoginForm[password]'] = '12345';
        $this->page = $this->client->submit($form);
        $this->checkTitles('Login');
        // fill and send true form; check title 
        $form = $this->page->selectButton('Log in')->form();
        $form['LoginForm[email]'] = $email;
        $form['LoginForm[password]'] = 'xtreme789';
        // emulate login from different roles
        foreach($roles as $role){
            $user = User::model()->findByAttributes(compact('role', 'provider_id'));
            Yii::app()->user->setModel($user);
            $this->page = $this->client->submit($form);
            $this->checkTitles('dashboard');
        }
	}

    public function testMenu()
    {
        foreach($this->models['users']['native'] as $user){
            $this->setUser($user);
            if(!$page = $this->path(array('UserController', 'index'), $user->isAdmin)->page){
                return true;
            };
            $menu = $page->filter('div.sidebar a')->links();
            foreach($menu as $link){
                $this->path($link->getUri());
            }
        }
    }
}