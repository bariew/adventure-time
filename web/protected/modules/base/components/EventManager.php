<?php
class EventManager
{
    private static $instance = null;
    public $events = array();

    public function set($model)
    {
        foreach($this->events as $className => $eventNames){
            if(!is_a($model, $className)){
                continue;
            }
            foreach($eventNames as $eventName => $handlers){
                foreach($handlers as $handler){
                    $model->$eventName = $handler;
                }
            }
        }
    }
    
    private function setEvents()
    {
        foreach(Yii::app()->modules as $name => $options){
            $module = Yii::app()->getModule($name);
            if(!isset($module->events)){
                continue;
            }
            
            foreach($module->events as $className => $eventNames){
                foreach($eventNames as $eventName => $handlers){
                    foreach($handlers as $handler){
                        $this->events[$className][$eventName][] = $handler;
                    }
                }
            }
        }
    }

    public static function model()
    {
        if (self::$instance === null) {
            self::$instance = new self;
            self::$instance->setEvents();
        }
        return self::$instance;
    }
    private function __construct(){}
    private function __clone(){}
}
