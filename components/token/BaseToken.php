<?php


namespace app\components\token;

use yii\helpers\ArrayHelper;
use yii\web\Cookie;

/**
 * Class BaseToken
 * @package app\components\token
 */
abstract class BaseToken extends \yii\base\BaseObject
{
	public abstract function encode($data);

	public abstract function decode($token);

	public abstract function expire($token);

	public abstract function delay($token);

	public function getCookieName()
	{
		return md5(static::class);
	}

	public function setCookie($token, $cookieName = null)
	{
		if (is_null($cookieName)) {
			$cookieName = $this->getCookieName();
		}
		if ($token === '') {
			\Yii::$app->response->cookies->remove($cookieName);
		} else {
			\Yii::$app->response->cookies->add(new Cookie([
				'name' => $cookieName,
				'value' => $token
			]));
		}
	}

	public function getCookie($cookieName = null)
	{
		if (is_null($cookieName)) {
			$cookieName = $this->getCookieName();
		}
		$request = \Yii::$app->request;
		if ($request->enableCookieValidation) {
			return ArrayHelper::getValue($_COOKIE, $cookieName);
		} else {
			return $request->getCookies()->getValue($cookieName);
		}
	}
}