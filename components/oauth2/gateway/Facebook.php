<?php


namespace app\components\oauth2\gateway;


use app\models\Connect;
use app\models\FacebookSetting;
use app\models\SettingModel;
use tinymeng\OAuth2\OAuth;

class Facebook extends Gateway
{
	const SCOPE_PUBLIC_PROFILE = 'public_profile';

	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

	public string $appName;
	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(FacebookSetting::SETTING_KEY_FACEBOOK_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET), 'yii');

	}

	public function getScope($scope): string
	{
		return self::SCOPE_PUBLIC_PROFILE;
	}

	public function getGrantType($grantType): string
	{
		return self::GRANT_TYPE_AUTHORIZATION_CODE;
	}

	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		$gateway = OAuth::Facebook([
			'scope' => $scope,
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'callback' => $redirect,
			'state' => $state,
			'response_type' => 'code'
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType = null): AuthorizeUser
	{
		$gateway = OAuth::Facebook([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_FACEBOOK;
		return $authorizeUser;
	}
}