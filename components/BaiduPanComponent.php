<?php


namespace app\components;


use app\models\BaiduSetting;
use Yii;
use yii\base\Component;

class BaiduPanComponent extends Component
{
	private $appKey;
	private $appSecret;

	public function init()
	{
		$this->appKey = Yii::$app->app->setting(BaiduSetting::SETTING_KEY_APP_KEY);
		$this->appSecret = Yii::$app->app->setting(BaiduSetting::SETTING_KEY_APP_SECRET);
	}

	public function authorize()
	{
		$this->appKey;
		$this->appSecret;
	}
}