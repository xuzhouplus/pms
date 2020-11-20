<?php


namespace app\filters\auth;


use yii\filters\auth\AuthMethod;

class CookieTokenAuth extends AuthMethod
{
	public function authenticate($user, $request, $response)
	{
		$token = \Yii::$app->token->cookie();
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