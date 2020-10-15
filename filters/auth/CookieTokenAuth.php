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
	public function authenticate($user, $request, $response)
	{
		$token=\Yii::$app->token->cookie();

		if ($token !== null) {
			$identity = $user->loginByAccessToken($token, get_class($this));
			if ($identity === null) {
				$this->challenge($response);
				$this->handleFailure($response);
			}
			return $identity;
		}
		return null;
	}
}