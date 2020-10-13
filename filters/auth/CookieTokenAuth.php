<?php


namespace app\behaviors\authenticators;


use yii\filters\auth\AuthMethod;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

class CookieTokenAuth extends AuthMethod
{
private $cookie='';
	public function authenticate($user, $request, $response)
	{
		if($request->enableCookieValidation){
			$authHeader=ArrayHelper::getValue($_COOKIE,$this->cookie);
		}else {
			$authHeader = $request->getCookies()->getValue($this->cookie);
		}
		if ($authHeader !== null) {
			$identity = $user->loginByAccessToken($authHeader, get_class($this));
			if ($identity === null) {
				$this->challenge($response);
				$this->handleFailure($response);
			}
			return $identity;
		}
		return null;
	}
}