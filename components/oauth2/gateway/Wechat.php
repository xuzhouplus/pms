<?php


use app\components\oauth2\gateway\AuthorizeUser;
use app\models\Connect;
use app\models\SettingModel;
use app\models\WechatSetting;
use tinymeng\OAuth2\OAuth;

class Wechat extends \app\components\oauth2\gateway\Gateway
{
	const SCOPE_SNSAPI_LOGIN = 'snsapi_login';
	const RESPONSE_TYPE_CODE = 'code';

	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(WechatSetting::SETTING_KEY_WECHAT_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(WechatSetting::SETTING_KEY_WECHAT_APP_SECRET), 'yii');

	}

	public function getScope($scope): string
	{
		return self::SCOPE_SNSAPI_LOGIN;
	}

	public function getGrantType($grantType): string
	{
		return self::GRANT_TYPE_AUTHORIZATION_CODE;
	}

	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		$gateway = OAuth::wechat([
			'scope' => $scope,
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'callback' => $redirect,
			'state' => $state,
			'response_type' => self::RESPONSE_TYPE_CODE
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType): AuthorizeUser
	{
		$gateway = OAuth::wechat([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'grant_type' => $grantType,
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_WECHAT;
		return $authorizeUser;
	}
}