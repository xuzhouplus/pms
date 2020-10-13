<?php


namespace app\controllers;


use app\models\Admin;
use yii\base\UserException;

class AdminController extends RestController
{
	public array $verbs = [
		'add' => ['POST']
	];

	/**
	 * @return array
	 * @throws UserException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionAdd()
	{
		$admin = Admin::add(\Yii::$app->request->getBodyParams());
		return $this->response($admin->getAttributes(['account', 'avatar', 'type', 'status', 'created_at', 'updated_at']));
	}

	/**
	 * @return array
	 * @throws UserException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionEdit()
	{
		$admin = Admin::add(\Yii::$app->request->getBodyParams());
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
		Admin::del(\Yii::$app->request->getBodyParam('id'));
		return $this->response(null, null, 'Delete succeed');
	}
}