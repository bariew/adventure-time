<?php
/**
 * UserActivate class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * widget for user activate/deactivate button
 * @package application.modules.user.widgets
 */
class UserActivate extends Widget
{
    /**
     * @var \User user model to activate/deactivate
     */
    public $user;
    /**
     * renders widget button
     */
    public function run()
    {
        $this->render('userActivateButton', array('user'=>$this->user));
    }
}
