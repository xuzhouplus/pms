<?php


namespace app\components;


use app\components\token\BaseToken;

class TokenComponent extends \yii\base\Component
{
	/**
	 * @var string
	 */
	public string $type = 'jwt';
	/**
	 * @var $handler BaseToken
	 */
	private BaseToken $handler;

	public function init()
	{
		parent::init(); // TODO: Change the autogenerated stub
		$tokenClass = __NAMESPACE__ . '\token\\' . ucfirst($this->type) . 'Token';
		if (!class_exists($tokenClass)) {
			throw new \Exception('Token component type is not found:' . $this->type);
		}
		$this->handler = \Yii::createObject($tokenClass);
	}

	/**
	 * @param $data
	 * @return array
	 */
	public function encode($data): array
	{
		return $this->handler->encode($data);
	}

	/**
	 * @param $token
	 * @return array
	 */
	public function decode($token): array
	{
		return $this->handler->decode($token);
	}

	public function delay($token = null)
	{
		return $this->handler->delay($token);
	}

	public function expire($token)
	{
		$this->handler->expire($token);
	}

	public function cookie($token=null,$options=[]){
		if(is_null($token)){
			return $this->handler->getCookie();
		}
		return $this->handler->setCookie($token,$options);
	}
}