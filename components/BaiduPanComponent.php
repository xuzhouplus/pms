<?php


namespace app\components;


use app\models\Setting;
use Yii;
use yii\base\Component;

class BaiduPanComponent extends Component
{
	private $appKey;
	private $appSecret;

	public function init()
	{
		$this->appKey = Yii::$app->app->setting(Setting::SETTING_KEY_BAIDU_PAN_APP_KEY);
		$this->appSecret = Yii::$app->app->setting(Setting::SETTING_KEY_BAIDU_PAN_APP_SECRET);
	}

	public function authorize()
	{
		$this->appKey;
		$this->appSecret;
	}
}