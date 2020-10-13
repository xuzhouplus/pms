<?php


namespace app\filters\auth;


use yii\filters\auth\AuthMethod;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

class HeaderTokenAuth extends AuthMethod
{

	public function authenticate($user, $request, $response)
	{
		// TODO: Implement authenticate() method.
	}
}