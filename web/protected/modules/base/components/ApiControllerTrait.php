<?php
/**
 * ApiControllerTrait trait file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * behavior for controller to interact with remoted devices
 * @package application.components
 */
trait ApiControllerTrait 
{
    
    /* ATTRIBUTES */
    
    public $isRemote = null;
    
    public function setIsRemote()
    {
        return $this->isRemote = preg_match('/^request\//', Yii::app()->request->pathInfo);
    }
    
    
    /* CONTROLLER INIT */
    
    public function init()
    {
        $this->setIsRemote();
        parent::init();
        if($this->isRemote){
            $this->initRemote();
        }
        
    }
    
    protected function initRemote()
    {
        //$this->setUser();
        Yii::log(json_encode(array_merge($_SERVER, array('body'  => Yii::app()->request->rawBody,))), 'info', 'api');
    }
    
    protected function setUser()
    {
        $request = Yii::app()->request;
        if(!$request->isSecureConnection){
            throw new CHttpException(400, 'Please use secure connection!');
        }
        if(!($apptoken = @$_POST['apptoken'])){
            throw new CHttpException(400, 'Missing apptoken');
        }
        if(!$project = PushProject::model()->findByAttributes(compact('apptoken'))){
            throw new CHttpException(401, 'Could not find application');
        };
        Yii::app()->user->setModel($project->company);
        $_GET['project_id'] = $project->id;
        $_GET['id'] = @$_POST['id'];
    }
    

    /* REWRITEN METHODS */
    
    public function getModel($id=false, $modelName=false, $attributes=array())
    {
        $model = parent::getModel($id, $modelName, $attributes);
        if($this->isRemote){
            $model->scenario = 'api';
        }
        return $model;
    }
    
    public function render($view = '', $data = null, $return = false, $processOutput = false) 
    {
        if(!$this->isRemote){
            return parent::render($view, $data, $return, $processOutput);
        }
        return $this->response($data);
    }

    public function renderPartial($view = '', $data = null, $return = false, $processOutput = false) 
    {
        if(!$this->isRemote){
            return parent::renderPartial($view, $data, $return, $processOutput);
        }
        return $this->response($data);
    }

    public function redirect($url, $terminate = true, $statusCode = 302) 
    {
        if(!$this->isRemote){
            return parent::redirect($url, $terminate, $statusCode);
        }
    }
    public function refresh($terminate = true, $anchor = '') 
    {
        if(!$this->isRemote){
            return parent::refresh($terminate, $anchor);
        }
    }
    public function forward($route, $exit = true)
    {
        if(!$this->isRemote){
            return parent::forward($route, $exit);
        }
    }

    
    /* RESPONSE CONSTRUCTION */
    
    protected function response($data) 
    {
        $result = $this->extractFlash();
        if (!is_array($data)) {
            $data = array($data);
        }
        foreach ($data as $key => $value) {
            if ($value instanceof CDataProvider) {
                $result[$key] = $this->extractDataProvider($value);
            } elseif ($value instanceof CModel) {
                $result[$key] = $this->extractModel($value);
            } else {
                $result[$key] = $value;
            }
        }
        $result['code'] = 200;
        $result['message']  = '';
        $result['success'] = $this->isSuccess($result);
        echo json_encode($result);
        exit;
    }

    protected function extractFlash() 
    {
        return array('alerts' => Yii::app()->user->getFlashes());
    }

    protected function extractDataProvider($dataProvider) 
    {
        $result = array(
            'data'  => array(),
            'pagination'    => array(
                'pageSize'  => $dataProvider->pagination->pageSize,
                'pageCount' => $dataProvider->pagination->pageCount,
            ),
            'totalItemCount'=> $dataProvider->totalItemCount
        );
        foreach ($dataProvider->data as $key=>$data) {
            $result['data'][$key] = $this->getModelAttributes($data);
        }
        return $result;
    }

    protected function extractModel($model) 
    {
        $result = array(
            'data' => $this->getModelAttributes($model),
            'errors' => $model->getErrors()
        );
        return $result;
    }

    protected function getModelAttributes($model) 
    {
        $result = array();
        $attributes = array_merge(array('id'), $model->safeAttributeNames);
        foreach ($attributes as $attribute) {
            $result[$attribute] = $model->$attribute;
        }
        return $result;
    }

    protected function isSuccess($data) 
    {
        $result = true;
        if (@$data['alerts']['error']) {
            return false;
        }
        array_walk($data, function($var) use(&$result) {
            if (is_array($var) && isset($var['errors']) && $var['errors']) {
                $result = false;
            }
        });
        return $result;
    }
    
    /*  EXAMPLES */
    
    
    /**
        $.post("/push/messageApi/schedule", {
            "apptoken"          : "08e615e2eff7c54543",
        })
        $.post("/push/messageApi/read", {
            "apptoken"          : "08e615e2eff7c54543",
            "id"                : "315",
        })
        $.post("/API/message/create", {
            "apptoken"              : "08e615e2eff7c54543",
            "PushMessage[title]"    : "myTitle",
            "PushMessage[text]"     : "myText",
            "PushMessage[ios][active]" : "1",
            "PushMessage[ios][environment]" : "production",
            "PushMessage[locationsArray][1][latitude]":"1",
            "PushMessage[locationsArray][1][longitude]":"2",
            "PushMessage[locationsArray][1][title]":"asd"
        })
        $.post("/API/message/update", {
            "id"                    : 320,
            "apptoken"              : "08e615e2eff7c54543",
            "PushMessage[title]"    : "myTitle",
            "PushMessage[text]"     : "myText",
            "PushMessage[ios][active]" : "1",
            "PushMessage[ios][environment]" : "production",
            "PushMessage[locationsArray][1][latitude]":"1",
            "PushMessage[locationsArray][1][longitude]":"2",
            "PushMessage[locationsArray][1][title]":"asd"
        })
        $.post("/push/messageApi/read", {
            "apptoken"          : "08e615e2eff7c54543",
            "id"                : "315",
        })
        $.post("/push/messageApi/send", {
            "apptoken"          : "08e615e2eff7c54543",
            "id"                : "316",
        })
     */
    
    public function sendCurl()
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL             => "https://xtremepush.kneesntoads.com/push/messageApi/create",
            CURLOPT_FRESH_CONNECT   => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_VERBOSE         => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => "apptoken=myToken&PushMessage[title]=myTitle&PushMessage[text]=myText&PushMessage[ios][active]=1",
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}