<?php

namespace app\controllers;

use app\helpers\Response;
use app\models\Setting;
use yii\helpers\ArrayHelper;

class SettingController extends RestController
{
	public array $except = [
		'index'
	];

	public array $verbs = [
		'index' => ['GET'],
		'add' => ['POST', 'PUT'],
		'edit' => ['POST', 'PATCH'],
		'del' => ['DELETE']
	];

	public function actionIndex()
	{
		$settings = Setting::getPublicSettings();
		return $this->response(ArrayHelper::map($settings, 'key', 'value'), null, null, 404);
	}

	public function actionAdd()
	{
		$setting = Setting::add(\Yii::$app->request->getBodyParams());
		return $this->response($setting->getAttributes(null, ['id']));
	}

	public function actionEdit()
	{
		$setting = Setting::edit(\Yii::$app->request->getBodyParams());
		return $this->response($setting->getAttributes(null, ['id']));
	}

	public function actionDel()
	{
		Setting::del(\Yii::$app->request->getBodyParam('key'));
		return $this->response(null, null, 'Setting delete succeed');
	}
}
