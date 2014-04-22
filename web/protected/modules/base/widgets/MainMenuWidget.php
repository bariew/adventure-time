<?php

class MainMenuWidget extends CWidget
{
    public function run()
    {
        $this->widget('ext.bootstrap.widgets.BootNavbar', array(
            'fixed' => false,
            'fluid' => true,
            'brand' => CHtml::image('/themes/default/assets/img/logo_small.png') . " " . Yii::app()->name,
            'brandUrl' => '/',
            'collapse' => false,
            'items' => array(
                array(
                    'class' => 'ext.bootstrap.widgets.BootMenu',
                    'htmlOptions' => array('class' => 'pull-right'),
                    'items' => $this->getUserItems()
                ),
                array(
                    'class' => 'ext.bootstrap.widgets.BootMenu',
                    'htmlOptions' => array('class' => 'pull-right'),
                    'items' => $this->getModulesItems(),
                ),
            ),
        ));
    }
	
    protected function getUserItems()
    {
       return Yii::app()->user->isGuest
       	? array(array(
            'label'     => 'Login',
            'url'       => '/login',
            'active'    => $this->owner->id == 'profile',
            'linkOptions'	=> array('class'=>'colorbox')
        ))
        : array(
               array(
                   'label'     => "Nodes",
                   'url'       => "/nodes",
                   'active'    => ($this->owner->module->id == 'node'),
               ),
               array(
                'label' => Yii::app()->user->name,
                'url' => '#',
                'items' => array(
                    array('label' => 'My profile', 'url' => array('/user/profile/view')),
                    array('label' => 'Logout', 'url' => array('/user/profile/logout'))
                ),
            ),
        );
    }
    
    protected function getModulesItems()
    {
        $items = array();
		if(!Yii::app()->user->level('admin')){
			return $items;
		}
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