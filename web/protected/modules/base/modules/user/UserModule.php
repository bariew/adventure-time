<?php
/**
 * UserModule class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * user module class
 * @package application.modules.user
 */
class UserModule extends BaseModule
{
	public $label = 'Users';
	public $path = array('/user/userItem/index');
    /**
     * imports own models
     */
    public function init() {
        $this->setImport(array(
            'application.modules.base.modules.user.models.*',
            'application.modules.base.modules.user.models.forms.*',
            'application.modules.base.modules.user.controllers.*',
            'application.modules.base.modules.user.components.*',
        ));
    }
    /**
     * renders admin menu
     * @return CWidget admin menu
     */
    public function adminMenu()
    {
        $items = array(
            array('label' => 'Users', 'icon' => 'list', 'url' => array('/user/userItem/index')),
            array('label' => 'Add user', 'icon' => 'plus', 'url' => array('/user/userItem/create')),
        );
        return Yii::app()->controller->widget('ext.bootstrap.widgets.BootMenu', array(
            'type' => 'list',
            'items' => $items,
        ));
    }
}