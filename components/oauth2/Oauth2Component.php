<?php


namespace app\components\oauth2;


class Oauth2Component extends \yii\base\Component
{
	/**
	 * @param $type
	 * @param $scope
	 * @return mixed|string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getScope($type, $scope): string
	{
		/**
		 * @var $gateway Alipay
		 */
		$gateway = \Yii::createObject(__NAMESPACE__ . '\\' . ucfirst($type));
		return $gateway->getScope($scope);
	}

	/**
	 * @param $type
	 * @param $grantType
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getGrantType($type, $grantType): string
	{
		/**
		 * @var $gateway Alipay
		 */
		$gateway = \Yii::createObject(__NAMESPACE__ . '\\' . ucfirst($type));
		return $gateway->getGrantType($grantType);
	}

	/**
	 * @param $type
	 * @param $scope
	 * @param $redirect
	 * @param $state
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getAuthorizeUrl($type, $scope, $redirect, $state)
	{
		/**
		 * @var $gateway Alipay
		 */
		$gateway = \Yii::createObject(__NAMESPACE__ . '\\' . ucfirst($type));
		return $gateway->getAuthorizeUrl($scope, $redirect, $state);
	}

	/**
	 * @param $type
	 * @param $grantType
	 * @return AuthorizeUser
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getUserInfo($type, $grantType)
	{
		/**
		 * @var $gateway Alipay
		 */
		$gateway = \Yii::createObject(__NAMESPACE__ . '\\' . ucfirst($type));
		return $gateway->getUserInfo($grantType);
	}
}