<?php
/**
 * UserAuthService class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * AR model to store data for user auth services (Google, facebook etc)
 * @package application.modules.user.models
 */
class UserAuthService extends ActiveRecord
{
    
    /* ATTRIBUTES AND LISTS */
    
    const ERROR_NOT_AUTHENTICATED = 3;
    /**
     * @var UserIdentity used in authentication process
     */
    public $identity;
    /**
     * @var object EOAuth model returned by login process 
     */
    public $service;
    /**
     * gets service name label for current model
     * @return string service name
     */
    public function getServiceName()
    {
        $services = array(
            'google' => 'Google',
            'yandex' => 'Yandex',
            'vkontakte' => 'VKontakte',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
        );
        return isset($services[$this->service_name]) ? $services[$this->service_name] : 'Unknown';
    }
    
    
    /*  AUTHENTICATE */
    
    /**
     * checks if there is a record in db with this auth service id
     * creates new one and its related UserItem record if missed == registers new user with this auth service id
     * @param string $service service name
     * @return if user is athenticates
     */
    public function authenticate($service)
    {
        $this->service = $service;
        $this->identity = new UserIdentity('', '');
        if (!$service->isAuthenticated) {
            $this->identity->errorCode = self::ERROR_NOT_AUTHENTICATED;
            return false;
        }
        // find self record
        $authService = self::model()->findByAttributes(array(
            'service_id' => $this->service->id,
            'service_name' => $this->service->serviceName,
        ));
        // create if missed
        if (!$authService){
            $authService = new self;
            $authService->service_id = $this->service->id;
            $authService->service_name = $this->service->serviceName;
        }
        // create new UserItem if no related user
        if (!$user = $authService->user) {
            $user = $this->createUserItem();
            $authService->user_id = $user->id;
            $authService->save(false);
        }
        // create and check this identity->athenticate
        $this->identity->setId($user->id);
        $this->identity->username = trim($user->name);
        $this->identity->errorCode = UserIdentity::ERROR_NONE;
        return !$this->identity->errorCode;
    }
    /**
     * creates new UserItem related to this instance
     * @return \UserItem user model
     */
    protected function createUserItem()
    {
        if($user = Yii::app()->user->model){
            return $user;
        }
        $user = new UserItem();
        if(method_exists($this->service, 'getAttribute')){
            $user->name = @$this->service->getAttribute('name');
            $user->email = @$this->service->getAttribute('email');
        }
        
        $user->save(false);
        return $user;
    }

    
    /* SYSTEM */
    
    /**
     * related ActiveRecords are available as this model attributes, named by this returned array keys
     * @return array
     */
	public function relations()
	{
        return array(
            'user' => array(self::BELONGS_TO, 'UserItem', 'user_id'),
		);
	}
    /**
     * defines DB table name for this class models
     * @return string table name
     */
	public function tableName()
	{
		return '{{user_authservice}}';
	}
}
