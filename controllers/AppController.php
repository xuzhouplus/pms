<?php


namespace app\controllers;


use app\models\Admin;
use app\models\Setting;
use Yii;

class AppController extends RestController
{
	public array $except = [
		'init'
	];
	public array $verbs = [
		'init' => ['POST']
	];

	public function actionInit()
	{
		$transaction = Yii::$app->getDb()->beginTransaction();
		try {
			Admin::add(Yii::$app->request->getBodyParam('admin'));
			Setting::saveValue(Yii::$app->request->getBodyParam('settings', []));
			Yii::$app->app->installLock('lock');
			$transaction->commit();
		} catch (\Exception $exception) {
			$transaction->rollBack();
			throw $exception;
		}
		return $this->response(null, null, 'Initialize succeed');
	}
}