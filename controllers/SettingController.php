<?php

namespace app\controllers;

use app\helpers\Response;
use app\helpers\RsaHelper;
use app\models\AlipaySetting;
use app\models\BaiduPanSetting;
use app\models\CarouselSetting;
use app\models\FacebookSetting;
use app\models\GitHubSetting;
use app\models\GoogleSetting;
use app\models\LineSetting;
use app\models\QQSetting;
use app\models\Setting;
use app\models\SiteSetting;
use app\models\TwitterSetting;
use app\models\WechatSetting;
use app\models\WeiboSetting;
use Yii;
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
		$setting = Setting::add(Yii::$app->request->getBodyParams());
		return $this->response($setting->getAttributes(null, ['id']));
	}

	public function actionEdit()
	{
		$setting = Setting::edit(Yii::$app->request->getBodyParams());
		return $this->response($setting->getAttributes(null, ['id']));
	}

	public function actionDel()
	{
		Setting::del(Yii::$app->request->getBodyParam('key'));
		return $this->response(null, null, 'Setting delete succeed');
	}

	public function actionCarousel()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = CarouselSetting::find('key');
			return $this->response($settings);
		} else {
			CarouselSetting::save($request->getBodyParams());
			return $this->response();
		}
	}

	public function actionBaiduPan()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = BaiduPanSetting::find();
			$settingPairs = ArrayHelper::map($settings, 'key', 'value');
			return $this->response($settingPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[BaiduPanSetting::SETTING_KEY_APP_SECRET])) {
				$requestData[BaiduPanSetting::SETTING_KEY_APP_SECRET] = BaiduPanSetting::decrypt($requestData[BaiduPanSetting::SETTING_KEY_APP_SECRET]);
				$requestData[BaiduPanSetting::SETTING_KEY_APP_SECRET] = BaiduPanSetting::encrypt($requestData[BaiduPanSetting::SETTING_KEY_APP_SECRET]);
			}
			BaiduPanSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionSite()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = SiteSetting::find('key');
			return $this->response($settings);
		} else {
			SiteSetting::save($request->getBodyParams());
			return $this->response();
		}
	}

	public function actionAlipay()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = AlipaySetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY] = $keyPairs[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY] ? true : "";
			$keyPairs[AlipaySetting::SETTING_KEY_ALIPAY_PUBLIC_KAY] = AlipaySetting::decrypt($keyPairs[AlipaySetting::SETTING_KEY_ALIPAY_PUBLIC_KAY], 'yii');
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY])) {
				$requestData[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY] = AlipaySetting::decrypt($requestData[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY]);
				$requestData[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY] = AlipaySetting::encrypt($requestData[AlipaySetting::SETTING_KEY_APP_PRIMARY_KEY]);
			}
			$requestData[AlipaySetting::SETTING_KEY_ALIPAY_PUBLIC_KAY] = AlipaySetting::encrypt($requestData[AlipaySetting::SETTING_KEY_ALIPAY_PUBLIC_KAY]);
			AlipaySetting::save($requestData);
			return $this->response();
		}
	}

	public function actionFacebook()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = FacebookSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET] = $keyPairs[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET])) {
				$requestData[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET] = FacebookSetting::decrypt($requestData[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET]);
				$requestData[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET] = FacebookSetting::encrypt($requestData[FacebookSetting::SETTING_KEY_FACEBOOK_APP_SECRET]);
			}
			FacebookSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionGithub()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = GitHubSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET] = $keyPairs[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET])) {
				$requestData[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET] = GitHubSetting::decrypt($requestData[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET]);
				$requestData[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET] = GitHubSetting::encrypt($requestData[GitHubSetting::SETTING_KEY_GITHUB_APP_SECRET]);
			}
			GitHubSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionGoogle()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = GoogleSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET] = $keyPairs[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET])) {
				$requestData[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET] = GoogleSetting::decrypt($requestData[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET]);
				$requestData[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET] = GoogleSetting::encrypt($requestData[GoogleSetting::SETTING_KEY_GOOGLE_APP_SECRET]);
			}
			GoogleSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionLine()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = LineSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[LineSetting::SETTING_KEY_LINE_APP_SECRET] = $keyPairs[LineSetting::SETTING_KEY_LINE_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[LineSetting::SETTING_KEY_LINE_APP_SECRET])) {
				$requestData[LineSetting::SETTING_KEY_LINE_APP_SECRET] = LineSetting::decrypt($requestData[LineSetting::SETTING_KEY_LINE_APP_SECRET]);
				$requestData[LineSetting::SETTING_KEY_LINE_APP_SECRET] = LineSetting::encrypt($requestData[LineSetting::SETTING_KEY_LINE_APP_SECRET]);
			}
			LineSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionQq()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = QQSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[QQSetting::SETTING_KEY_QQ_APP_SECRET] = $keyPairs[QQSetting::SETTING_KEY_QQ_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[QQSetting::SETTING_KEY_QQ_APP_SECRET])) {
				$requestData[QQSetting::SETTING_KEY_QQ_APP_SECRET] = QQSetting::decrypt($requestData[QQSetting::SETTING_KEY_QQ_APP_SECRET]);
				$requestData[QQSetting::SETTING_KEY_QQ_APP_SECRET] = QQSetting::encrypt($requestData[QQSetting::SETTING_KEY_QQ_APP_SECRET]);
			}
			QQSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionTwitter()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = TwitterSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET] = $keyPairs[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET])) {
				$requestData[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET] = TwitterSetting::decrypt($requestData[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET]);
				$requestData[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET] = TwitterSetting::encrypt($requestData[TwitterSetting::SETTING_KEY_TWITTER_APP_SECRET]);
			}
			TwitterSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionWechat()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = WechatSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET] = $keyPairs[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET])) {
				$requestData[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET] = WechatSetting::decrypt($requestData[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET]);
				$requestData[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET] = WechatSetting::encrypt($requestData[WechatSetting::SETTING_KEY_WECHAT_APP_SECRET]);
			}
			WechatSetting::save($requestData);
			return $this->response();
		}
	}

	public function actionWeibo()
	{
		$request = Yii::$app->request;
		if ($request->isGet) {
			$settings = WeiboSetting::find();
			$keyPairs = ArrayHelper::map($settings, 'key', 'value');
			$keyPairs[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET] = $keyPairs[WeiboSetting::SETTING_KEY_APP_PRIMARY_KEY] ? true : "";
			return $this->response($keyPairs);
		} else {
			$requestData = $request->getBodyParams();
			if (!empty($requestData[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET])) {
				$requestData[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET] = WeiboSetting::decrypt($requestData[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET]);
				$requestData[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET] = WeiboSetting::encrypt($requestData[WeiboSetting::SETTING_KEY_WEIBO_APP_SECRET]);
			}
			WeiboSetting::save($requestData);
			return $this->response();
		}
	}
}
