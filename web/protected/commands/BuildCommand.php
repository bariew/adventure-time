<?php

class BuildCommand extends CConsoleCommand
{
	public function run($args=false)
	{
        $this->buildNotify($args);
	}
    
    private function buildNotify()
    {
        if(!$admins = User::model()->findAllByAttributes(array('role'=>'root'))){
            echo 'No admins found';
            return true;
        }
        $emails = CHtml::listData($admins, 'email', 'email');
        echo  Yii::app()->mailManager->send(array(
           'FromName'   => Yii::app()->name,
           'Body'       => 'Time: '. Yii::app()->dateFormatter->format('dd.MM.yyyy HH:mm', time()),
           'Subject'    => 'New build on '. Yii::app()->name,
           'to'         => $emails,
        ))
        ? 'Successful emailing' : 'Error emailing';
    }
}