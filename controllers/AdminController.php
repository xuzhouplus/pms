<?php


namespace app\controllers;


use app\models\Admin;
use app\models\Connect;
use app\models\Identity;
use Yii;
use yii\base\UserException;

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
		$accessToken = $admin->generateAccessToken();
		$adminAttributes['token'] = $accessToken['token'];

		$identity = new Identity();
		$identity->uuid = $accessToken['uuid'];
		$identity->token = $accessToken['token'];
		$identity->admin = $admin;
		Yii::$app->user->login($identity);

		Yii::$app->token->cookie($adminAttributes['token'], ['httpOnly' => true]);
		return $this->response($adminAttributes, null, 'Login succeed');
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function actionAuth()
	{
		$identity = Yii::$app->user->identity;
		if (empty($identity)) {
			throw new UserException('Get auth info failed');
		}
		Yii::$app->token->delay($identity->token);
		$adminAttributes = $identity->admin->getAttributes(['uuid', 'type', 'avatar', 'account']);
		return $this->response($adminAttributes, null, 'Get auth info succeed');
	}

	/**
	 * @return array
	 */
	public function actionLogout()
	{
		$identity = Yii::$app->user->identity;
		if ($identity) {
			Yii::$app->token->expire($identity->token);
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
		$authorizeUser = Admin::getAuthorizeUser($type, Yii::$app->oauth2->getGrantType($type, 'authorization_code'));
		$connect = Yii::$app->user->identity->admin->bindConnect($authorizeUser);
		return $this->redirect(Yii::$app->app->setting('hostDomain') . '/admin/authorize?union_id=' . $connect->union_id);
	}

	/**
	 * @return array
	 * @throws UserException
	 * @throws \yii\base\Exception
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionProfile()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$admin = Admin::findOneById($request->getQueryParam('id'), ['uuid', 'avatar', 'account', 'status']);
			return $this->response($admin);
		} else {
			$admin = Admin::edit($request->getBodyParams());
			return $this->response($admin);
		}
	}

	/**
	 * @return array
	 */
	public function actionConnects()
	{
		$request = Yii::$app->request;
		$admin = Admin::findOneById($request->getQueryParam('id'));
		$connects = $admin->connect;
		return $this->response($connects);
	}

	public function actionAdminConnect()
	{
		$connect = Connect::find()->select(['type', 'avatar', 'account'])->where(['union_id' => Yii::$app->request->getQueryParam('union_id')])->limit(1)->one();
		return $this->response($connect);
	}
}