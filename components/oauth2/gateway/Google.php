<?php


namespace app\components\oauth2\gateway;


use app\models\Connect;
use app\models\GoogleSetting;
use app\models\SettingModel;
use tinymeng\OAuth2\OAuth;
use yii\helpers\ArrayHelper;

class Google extends Gateway
{
	const SCOPE_PROFILE = 'profile';

	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

	public string $appName;
	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(GoogleSetting::SETTING_KEY_GOOGLE_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET), 'yii');

	}

	public function getScope($scope): string
	{
		return self::SCOPE_PROFILE;
	}

	public function getGrantType($grantType): string
	{
		return self::GRANT_TYPE_AUTHORIZATION_CODE;
	}

	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		$gateway = OAuth::Google([
			'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'callback' => $redirect,
			'state' => $state
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType): AuthorizeUser
	{
		$gateway = OAuth::Google([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'grant_type' => $grantType
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_GOOGLE;
		return $authorizeUser;
	}
}