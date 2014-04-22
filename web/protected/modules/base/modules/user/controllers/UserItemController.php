<?php
/**
 * UserItemController class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * admin user controller - manages users models
 * @package application.modules.user.controllers
 */
class UserItemController extends BackendController
{
    /**
     * activates or deactivates user
     * @param integer $id user id
     * @param integer $active 1/0 
     */
    public function actionActivate($id, $active)
    {
        $user = $this->getModel($id);
        $user->saveAttributes(compact('active'));
        echo $this->widget('user.widgets.UserActivate', compact('user'), true);
    }
    /**
     * lists, serarchs and sorts all user models
     * can be used by actionRead to define current model and contact form in index view
     * @param \UserItem $model user model
     * @param \UserContactForm $contactForm contact form model
     */
    public function actionIndex($model=false, $contactForm=false)
    {
        $this->createTitles('Users');
        $dataProvider = $this->getModel()->search();
        $this->render('index', compact('dataProvider', 'model', 'contactForm'));
    }

}
