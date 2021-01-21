<?php


namespace app\controllers;


use app\models\Admin;
use Yii;
use yii\base\UserException;
use yii\web\Cookie;

class AdminController extends RestController
{
	public array $except = [
		'login'
	];
	public array $optional = [
		'auth',
		'logout'
	];
	public array $verbs = [
		'add' => ['POST'],
		'login' => ['POST'],
		'logout' => ['POST'],
		'auth' => ['POST'],
	];

	/**
	 * @return array
	 * @throws UserException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionAdd()
	{
		$admin = Admin::add(Yii::$app->request->getBodyParams());
		return $this->response($admin->getAttributes(['account', 'avatar', 'type', 'status', 'created_at', 'updated_at']));
	}

	/**
	 * @return array
	 * @throws UserException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionEdit()
	{
		$admin = Admin::add(Yii::$app->request->getBodyParams());
		return $this->response($admin->getAttributes(['account', 'avatar', 'type', 'status', 'created_at', 'updated_at']));
	}

	/**
	 * @return array
	 * @throws UserException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDel()
	{
		Admin::del(Yii::$app->request->getBodyParam('id'));
		return $this->response(null, null, 'Delete succeed');
	}

	/**
	 * @return array
	 * @throws UserException
	 */
	public function actionLogin()
	{
		$account = Yii::$app->request->getBodyParam('account');
		$password = Yii::$app->request->getBodyParam('password');
		$admin = Admin::login($account, $password);
		if (empty($admin)) {
			throw new UserException('Login failed');
		}
		$adminAttributes = $admin->getAttributes(['uuid', 'type', 'avatar', 'account']);
		$adminAttributes['token'] = $admin->generateAccessToken();
		Yii::$app->token->cookie($adminAttributes['token'], ['httpOnly' => true]);
		return $this->response($adminAttributes, null, 'Login succeed');
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function actionAuth()
	{
		$admin = Yii::$app->user->identity;
		if (empty($admin)) {
			throw new UserException('Get auth info failed');
		}
		Yii::$app->token->delay($admin->token);
		$adminAttributes = $admin->getAttributes(['uuid', 'type', 'avatar', 'account']);
		return $this->response($adminAttributes, null, 'Get auth info succeed');
	}

	/**
	 * @return array
	 */
	public function actionLogout()
	{
		$admin = Yii::$app->user->identity;
		if ($admin) {
			Yii::$app->token->expire($admin->token);
		}
		return $this->response(null, null, 'Logout succeed');
	}

	/**
	 * @return array
	 * @throws \yii\base\Exception
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionAuthorize()
	{
		$request = Yii::$app->request;
		$authorizeUrl = Admin::getAuthorizeUrl($request->getQueryParam('type'), $request->getQueryParam('scope'));
		return $this->response($authorizeUrl, null, 'Logout succeed');
	}

	/**
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionConnect()
	{
		$request = Yii::$app->request;
		$type = $request->getQueryParam('type');
		$authorizeUrl = Admin::getAuthorizeUser($type, Yii::$app->oauth2->getGrantType($type, 'authorization_code'));
		return $this->response($authorizeUrl, null, 'Logout succeed');
	}
}