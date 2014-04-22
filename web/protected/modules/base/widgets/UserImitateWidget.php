<?php 
class UserImitateWidget extends CWidget
{
    public $view = 'userImitate';

    public function run()
    {
        if($user_id = @$_POST['UserImitateWidget']['user_id']){
            $this->setUser($user_id);
        }
        $users = UserItem::model()->findAll();
        $user = $this->getUser();
        Yii::app()->user->setModel($user);
        $this->render($this->view, array(
            'user'      => $user,
            'userList'  => CHtml::listData($users, 'id', 'name')
        ));
    }
    
    protected function getUser()
    {
        return ($user_id = @Yii::app()->user->getState('user_id'))
            ? UserItem::model()->findByPk($user_id)
            : Yii::app()->user->model;
    }
    
    protected function setUser($user_id)
    {
        return Yii::app()->user->setState('user_id', $user_id);
    }
}