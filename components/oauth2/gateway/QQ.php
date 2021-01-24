<?php


namespace app\components\oauth2\gateway;


use app\models\Connect;
use app\models\QQSetting;
use app\models\SettingModel;
use tinymeng\OAuth2\OAuth;
use yii\helpers\ArrayHelper;

class QQ extends Gateway
{

	const SCOPE_GET_USER_INFO = 'get_user_info';
	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(QQSetting::SETTING_KEY_QQ_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(QQSetting::SETTING_KEY_QQ_APP_SECRET), 'yii');
	}

	public function getScope($scope): string
	{
		return self::SCOPE_GET_USER_INFO;
	}

	public function getGrantType($grantType): string
	{
		return self::GRANT_TYPE_AUTHORIZATION_CODE;
	}

	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		$gateway = OAuth::Qq([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'unionid' => 1,
			'scope' => $scope,
			'state' => $state,
			'callback' => $redirect
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType): AuthorizeUser
	{
		$gateway = OAuth::Qq([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'grant_type' => $grantType,
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_QQ;
		return $authorizeUser;
	}
}