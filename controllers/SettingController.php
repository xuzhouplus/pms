<?php

namespace app\controllers;

use app\helpers\Response;
use app\models\AlipaySetting;
use app\models\BaiduPanSetting;
use app\models\CarouselSetting;
use app\models\Setting;
use app\models\SiteSetting;
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
		return $this->response(ArrayHelper::map($settings, 'key', 'value'));
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

	public function actionCarousel()
	{
		$settings = CarouselSetting::find()->all();
		return $this->response(ArrayHelper::map($settings, 'key', 'value'));
	}

	public function actionBaiduPan()
	{
		$settings = BaiduPanSetting::find()->all();
		return $this->response(ArrayHelper::map($settings, 'key', 'value'));
	}

	public function actionAlipay()
	{
		$settings = AlipaySetting::find()->all();
		return $this->response(ArrayHelper::map($settings, 'key', 'value'));
	}

	public function actionSite()
	{
		$settings = SiteSetting::find()->all();
		return $this->response(ArrayHelper::map($settings, 'key', 'value'));
	}
}
