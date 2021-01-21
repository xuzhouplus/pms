<?php


namespace app\components\oauth2;


use app\models\AlipaySetting;
use app\models\Connect;
use tinymeng\OAuth2\OAuth;
use yii\helpers\ArrayHelper;

class Alipay extends Gateway
{
	//接口权限值
	const SCOPE_AUTH_USER = 'auth_user';//以auth_user为scope发起的网页授权，是用来获取用户的基本信息的（比如头像、昵称等）。但这种授权需要用户手动同意，用户同意后，就可在授权后获取到该用户的基本信息
	const SCOPE_AUTH_BASE = 'auth_base';//以auth_base为scope发起的网页授权，是用来获取进入页面的用户的userId的，并且是静默授权并自动跳转到回调页的。用户感知的就是直接进入了回调页（通常是业务页面）
	//授权方式
	const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';//表示换取使用用户授权码code换取授权令牌access_token
	const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';//表示使用refresh_token刷新获取新授权令牌

	public $appId;
	public $appPrimaryKey;
	public $alipayPublicKey;

	public function init()
	{
		$this->appId = \Yii::$app->app->setting(AlipaySetting::SETTING_KEY_APPID);
		$this->appPrimaryKey = \Yii::$app->app->setting(AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY);
		$this->alipayPublicKey = \Yii::$app->app->setting(AlipaySetting::SETTING_KEY_ALIPAY_PUBLIC_KAY);

	}

	/**
	 * 获取权限值
	 * @param $scope
	 * @return mixed
	 * @throws \Exception
	 */
	public function getScope($scope): string
	{
		$scopes = [
			'auth_user' => self::SCOPE_AUTH_USER,
			'auth_base' => self::SCOPE_AUTH_BASE,
		];
		return ArrayHelper::getValue($scopes, $scope);
	}

	/**
	 * 获取授权方式
	 * @param $grantType
	 * @return string
	 * @throws \Exception
	 */
	public function getGrantType($grantType): string
	{
		$grantTypes = [
			'authorization_code' => self::GRANT_TYPE_AUTHORIZATION_CODE,
			'refresh_token' => self::GRANT_TYPE_REFRESH_TOKEN,
		];
		return ArrayHelper::getValue($grantTypes, $grantTypes);
	}

	/**
	 * 获取授权跳转地址
	 * @param $scope
	 * @param $redirect
	 * @param $state
	 * @return string
	 * @throws \Exception
	 * @link https://opendocs.alipay.com/open/263/105808
	 */
	public function getAuthorizeUrl($scope, $redirect, $state): string
	{
		if (!in_array($scope, [self::SCOPE_AUTH_BASE, self::SCOPE_AUTH_USER])) {
			throw new \Exception('scope参数错误');
		}
		$gateway = OAuth::Alipay([
			'app_id' => $this->appId,
			'pem_private' => $this->appPrimaryKey,
			'pem_public' => $this->alipayPublicKey,
			'scope' => $scope,
			'callback' => $redirect,
			'state' => $state
		]);
		return $gateway->getRedirectUrl();
	}

	/**
	 * 获取用户信息，接口把获取access_token和获取用户信息合并在了一起
	 * @param $grantType
	 * @return AuthorizeUser
	 * @link https://opendocs.alipay.com/apis/api_9/alipay.system.oauth.token
	 */
	public function getUserInfo($grantType): AuthorizeUser
	{
		$gateway = OAuth::Alipay([
			'app_id' => $this->appId,
			'pem_private' => $this->appPrimaryKey,
			'pem_public' => $this->alipayPublicKey,
			'grant_type' => $grantType,
		]);
		$userInfo = $gateway->userInfo();
		$authorizeUser = new AuthorizeUser($userInfo);
		$authorizeUser->type = Connect::CONNECT_TYPE_ALIPAY;
		return $authorizeUser;
	}
}