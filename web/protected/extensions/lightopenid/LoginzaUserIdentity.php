<?php

class LoginzaUserIdentity extends CBaseUserIdentity
{
	public $token;
	
	protected $_id;
	protected $_name;
	
	public function __construct($token)
	{
		$this->token = $token;
	}
	
	public function authenticate()
	{	
		// Получаем данные с loginza.ru
		// Нам нужны только provider и uid - они есть по любому.
		$data = CJSON::decode(
			file_get_contents('http://loginza.ru/api/authinfo?token=' . $this->token)
		);
		
		// Обрабатываем ошибку от Loginza которая возвращается в следующем формате
		// {"error_type":"token_validation","error_message":"Empty token value."}
		if ( isset($data['error_type']) )
		{
			$this->errorCode = self::UNKNOWN_IDENTITY;			
		}
		else
		{		
			// Иначе если все впорядке с логинзой, то ищем пользователя по provider и uid
			$model = User::model()->findByAttributes(array(
				'provider' => $data['provider'],
				'uid' => $data['uid'],	
			));
			
			// Если пользователь не найден, создаем его, статус его оставляем равным 0, то есть не зарегистрированный, чтобы
			// ему понадобилось заполнить обязательные поля			
			if ( ! $model )
			{
				$model = new User;
				$model['provider'] = $data['provider'];
				$model['uid'] = $data['uid'];
                $model['status'] = 0;
                $model['role'] = 'user';
								
				// Далее				
				if (isset($data['name']['last_name']) )
					$name = $data['name']['last_name'];

				if (isset($data['name']['first_name']) )
					$name .= ' ' . $data['name']['first_name'];
					
				$model['name'] = trim($name);
				
				$model->save();
			}
			
			$this->_id = $model['id'];
			$this->_name = trim($model['name']);
			$this->errorCode = self::ERROR_NONE;
		}
		
		return $this->errorCode == self::ERROR_NONE;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getName()
	{
		return $this->_name;
	}
}