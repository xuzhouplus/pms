<?php


namespace app\components\oauth2\gateway;


use app\models\Connect;
use app\models\GitHubSetting;
use app\models\SettingModel;
use tinymeng\OAuth2\OAuth;

class GitHub extends Gateway
{
	const SCOPE_USER = 'user';

	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

	public string $appName;
	public string $appId;
	public string $appSecret;

	public function init()
	{
		$this->appName = \Yii::$app->app->setting(GitHubSetting::SETTING_KEY_GITHUB_APPLICATION_NAME);
		$this->appId = \Yii::$app->app->setting(GitHubSetting::SETTING_KEY_GITHUB_APP_ID);
		$this->appSecret = SettingModel::decrypt(\Yii::$app->app->setting(GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET), 'yii');

	}

	public function getScope($scope): string
	{
		return self::SCOPE_USER;
	}

	public function getGrantType($grantType): string
	{
		return self::GRANT_TYPE_AUTHORIZATION_CODE;
	}

	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		$gateway = OAuth::Github([
			'scope' => $scope,
			'application_name' => $this->appName,
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'callback' => $redirect,
			'state' => $state
		]);
		return $gateway->getRedirectUrl();
	}

	public function getUserInfo($grantType): AuthorizeUser
	{
		$gateway = OAuth::Github([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'grant_type' => $grantType,
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_GITHUB;
		return $authorizeUser;
	}
}