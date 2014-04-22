<?php
/**
 * UserMenuWidget class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * widget for user navbar menu
 * @package application.modules.user.widgets
 */
class UserMenuWidget extends CWidget
{
    /**
     * renders user navigation bar menu
     */
    public function run()
    {
        $this->widget('ext.bootstrap.widgets.BootNavbar', array(
            'fixed' => false,
            'fluid' => true,
            'brand' => "<img src='/themes/default/assets/img/logo.png' />",
            'brandUrl' => '/',
            'collapse' => false,
            'items' => array(
                array(
                    'class' => 'ext.bootstrap.widgets.BootMenu',
                    'htmlOptions' => array('class' => 'pull-right'),
                    'items' => $this->getUsers()
                ),
                array(
                    'class' => 'ext.bootstrap.widgets.BootMenu',
                    'htmlOptions' => array('class' => 'pull-right'),
                    'items' => $this->getAdminItems(),
                ),
            ),
            'htmlOptions'=>array('class'=>'container')
        ));
    }
	/**
     * defines user menu items
     * @return array user menu items
     */
    protected function getUsers()
    {
       $path = Yii::app()->request->pathInfo;
       return array(
           Yii::app()->user->isGuest  
            ? array(
                'label'     => 'Login',
                'linkOptions'=>array('class'=>'colorbox'),
                'url'       => '/login',
                'active'    => $path == 'login'
            )
            : array(
                'label'     => 'Logout',
                'url'       => '/logout',
                'active'    => $path == 'logout'
            )  
            /*: array(
                'label' => Yii::app()->user->name,
                'url' => '#',
                'items' => array(
                    array('label' => 'My profile', 'url' => array('/user/profile/view')),
                    array('label' => 'Logout', 'url' => array('/user/profile/logout'))
                ),
            ),*/
        );
    }
    /**
     * defines admin menu items
     * @return array admin menu items
     */
    private function getAdminItems()
    {
        if(!Yii::app()->user->level('admin')){
            return array();
        }
        $result = array(
            array('label' => 'Users', 'url' => array("/user/user/index")),
            array('label' => 'Pages', 'url' => array("/textPage/textPageCategory/index")),
        );

        return $result;
    }
    /**
     * gets modules menu items from each Module $path attribute
     * @return array modules menu items
     */
    protected function getModulesItems()
    {
        $items = array();
        foreach (Yii::app()->getModules() as $m => $val) {
        	// do not show menu item with no path provided
            if((!$module = Yii::app()->getModule($m)) || !$module->path)
                continue;
			$active = ($this->owner->module->id == $module->id);
			$item = array(
                'label'     => $module->label,
                'url'       => $module->path,
                'active'    => $active,
			);
			// you may group few modules in one dropdown menu item
			if($module->group){
				if($active){
					$items[$module->group]['active'] = true;
				};
				$items[$module->group]['label'] = $module->group;
				$items[$module->group]['items'][] = $item;
			}else{
				$items[] = $item;
			}
        }
        return $items;
    }
}
