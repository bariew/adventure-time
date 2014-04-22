<?php
/**
 * PushTest class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * unit test class for push module
 * @package application.tests.unit
 */
class PushTest extends UnitTesting
{
    /**
     * call actions on class init
     */
    public function init()
    {
        Yii::app()->getModule('push');
    }
    /**
     * test all defined models by their docs example code
     */
    public function testModels()
    {
        foreach(array('PushMessage', 'PushMessageIos', 'PushMessageAndroid', 'PushProject', 'PushAction', 'PushLocation', 'PushDevice') as $className){
            $this->className = $className;
            $this->allByDocExample();
        }
    }
    
    public function testPushMessage()
    {
        return;
        $this->clearMocks();
        $testData = array(
            
        );
        $model = $this->getMockMessage($data['attributes']);
    }
    
    public function testScheduleBehaviorPeriod()
    {
        $time = strtotime("1978-07-06 02:00:00");
        $timezone = "+0000";
        //echo $time;exit;
        $testData = array(
            // send type
            array('attributes'=>array(), 'result'=>true),
            array('attributes'=>array('send_type'=>0), 'result'=>false),
            // daily
            array('attributes'=>array('starttime'   => '1978-07-08 02:00:00'), 'result'=>false), // starttime after
            array('attributes'=>array('starttime'   => '1978-07-06 02:01:00', 'period_start'  => '02:01'), 'result'=>false), // starttime after
            array('attributes'=>array('endtime'     => '1978-07-06 02:00:00'), 'result'=>false),  // endtime expired
            array('attributes'=>array('starttime'   => '1978-07-05 23:00:00',  'endtime'     => '1978-07-06 05:00:00'), 'result'=>true, 'skipSchedule'=>true), // inside between days period
            array('attributes'=>array('starttime'   => '1978-07-05 23:00:00',  'endtime'     => '1978-07-08 01:00:00'), 'result'=>false, 'skipSchedule'=>true), // inside between days period
            // monthly
            array('attributes'=>array('starttime'   => '1978-05-06 02:00:00', 'recurring'=>'Monthly'), 'result'=>true),
            array('attributes'=>array('starttime'   => '1978-05-05 02:00:00', 'recurring'=>'Monthly'), 'result'=>false),
            array('attributes'=>array('starttime'   => '1978-05-07 02:00:00', 'recurring'=>'Monthly'), 'result'=>false),
            // weekly
            array('attributes'=>array('starttime'   => '1978-06-29 02:00:00', 'recurring'=>'Weekly'), 'result'=>true),
            array('attributes'=>array('starttime'   => '1978-06-30 02:00:00', 'recurring'=>'Weekly'), 'result'=>false),
            // weekdays
            array('attributes'=>array('starttime'   => '1978-06-29 02:00:00', 'recurring'=>'Weekdays'), 'time'=>strtotime("1978-07-03 02:00"), 'result'=>true),
            array('attributes'=>array('starttime'   => '1978-06-29 02:00:00', 'recurring'=>'Weekdays'), 'time'=>strtotime("1978-07-02 02:00"), 'result'=>false),
            array('attributes'=>array('starttime'   => '1978-06-29 02:00:00', 'recurring'=>'Weekdays'), 'time'=>strtotime("1978-07-01 02:00"), 'result'=>false),
            // devicetimezone
            array('attributes'=>array('starttime'   => '1978-07-06 05:00:00', 'timezone'=>''), 'timezone'=>"+0330", 'result'=>true), // device is already 5.00
            array('attributes'=>array('starttime'   => '1978-07-06 06:00:00', 'timezone'=>''), 'timezone'=>"+0330", 'result'=>false, 'skipSchedule'=>true),// device is not yet 6.00
            // timezone - no need to check as starttime is kept in gmt
        );
        $this->clearMocks();
        foreach($testData as $key=>$data){
            // testing checkPeriod method
            $model = $this->getMockMessage($data['attributes']);
            $checkTime = isset($data['time']) ? $data['time'] : $time;
            $checkTimezone = isset($data['timezone']) ? $data['timezone'] : $timezone;
            $this->assertTrue(($model->scheduleBehavior->checkPeriod($checkTime, $checkTimezone) == $data['result']), 
                "checkPeriod -- $key -- " . http_build_query($data['attributes']));
            if(@$data['skipSchedule']){
                continue;
            }
            // testing schedule criteria
            $this->assertTrue($model->save(), "Could not save the schedule model");
            $model = $model->refresh();
            $criteria = $model->scheduleBehavior->searchCriteria($checkTime);
            $this->assertTrue((!$model->find($criteria) == !$data['result']),
                "scheduleCriteria  -- $key -- " . http_build_query($data['attributes']));
            $this->assertTrue($model->delete(), "Could not delete the schedule model");
        }
    }
    
    private function getMockMessage($addAttributes=array())
    {
        return PushMessage::mock(array_merge(array(
            "project_id"=> 3,
            "title"     => "mock model",
            "text"      => "mock model",
            "ios"       => array("active"=>1, 'environment'=>"production"),
            'send_type' => 1,
            'recurring' => 'Daily',
            //'weekday'   => 4,
            'timezone'  => '+0000',
            'starttime' => '1978-07-06 02:00:00',
            'endtime'   => '1978-08-06 23:59:00',
            'period_start'  => '02:00',
            'period_end'    => '23:59',
        ), $addAttributes));
    }
    
    private function clearMocks()
    {
        return PushMessage::model()->deleteAllByAttributes(array("title"=>"mock model"));
    }
}