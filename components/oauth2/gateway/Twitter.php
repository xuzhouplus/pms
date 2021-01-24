<?php


namespace app\components\oauth2\gateway;


use app\models\Connect;
use app\models\SettingModel;
use app\models\TwitterSetting;
use tinymeng\OAuth2\OAuth;

class Twitter extends Gateway
{
	const SCOPE_PUBLIC_PROFILE = 'public_profile';

	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

	public string $appName;
	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(TwitterSetting::SETTING_KEY_TWITTER_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET), 'yii');

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
		$gateway = OAuth::Twitter([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'callback' => $redirect,
			'state' => $state
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType = null): AuthorizeUser
	{
		$gateway = OAuth::Twitter([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_TWITTER;
		return $authorizeUser;
	}
}