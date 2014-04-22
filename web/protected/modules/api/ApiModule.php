<?php
/**
 * ApiModule class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Api module class
 * @package application.modules.api
 */
class ApiModule extends BaseModule
{
    /**
     * @var string module visible name
     */
	public $label = 'Api';
    /**
     * inits module, imports its components
     */
    public function init() 
    {
        $this->setImport(array(
            'application.modules.node.models.*',
            'application.modules.node.components.*',
            'application.modules.node.controllers.*',
        ));
    }
    /**
     * returns menu items
     */
    public function adminMenu()
    {
    }
}