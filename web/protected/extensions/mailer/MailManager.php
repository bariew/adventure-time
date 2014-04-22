<?php

class MailManager extends CApplicationComponent
{
    public $mailer;
    public function getContentPattern($model, $patternName)
    {
    	if(!$pattern = Yii::app()->config->get($patternName))
			$pattern = $patternName;
        $body = array();
        foreach($model->getSafeAttributeNames() as $attribute){
            $body['{{'.$attribute.'}}'] = $model->$attribute;
            if(isset($model->create_time))
                $body['{{create_time}}'] = date('d.m.Y', $model->create_time);
            if(isset($model->update_time))
                $body['{{update_time}}'] = date('d.m.Y', $model->update_time);
        }
        if(isset($model->id))
            $body['{{id}}'] = $model->id;
        $content = strtr($pattern,$body);
        return $content;
    }

    public function send($options=array())
    {
        $this->mailer = Yii::createComponent('ext.mailer.EMailer');
        switch(@$options['method']){
            case 'html': $this->mailer->IsHTML(); break;
            default: $this->mailer->IsMail();
        }
        $mailerOptions = array(
            'CharSet'   => 'UTF-8',
            'Subject'   => "New message from " . Yii::app()->name,
            'From'      => Yii::app()->name,
            'FromName'  => Yii::app()->name,
            'Body'      => 'empty email',
        );
        foreach($mailerOptions as $attribute => $value){
            $this->mailer->$attribute = isset($options[$attribute]) ? $options[$attribute] : $value;
        }
        $emails = isset($options['to']) ? $options['to'] : $this->mailer->From;
        if($model = @$options['model'] && $pattern = @$options['pattern']){ 
            $this->mailer->Body = $this->getContentPattern($model, $pattern);
        }
        if(!is_array($emails)){
            $emails = preg_split('/[\s,]+/', $emails, -1, PREG_SPLIT_NO_EMPTY);
        }
        if($separate = @$options['separate']){
            return $this->sendSeparate($emails);
        }
        foreach ($emails as $email) {
            $this->mailer->AddAddress(trim($email));
        }
        return $this->mailer->Send();
    }
    
    protected function sendSeparate($emails)
    {
        foreach($emails as $email){
            $this->mailer->AddAddress(trim($email));
            $this->mailer->Send();
            $this->mailer->ClearAddresses();
        }
        return true;
    }
}
