<?php


namespace app\models;

use yii\base\Model;

/**
 * 百度对接配置
 * Class BaiduSetting
 * @package app\models
 */
class BaiduSetting extends SettingModel
{
	const SETTING_KEY_API_KEY = 'baidu_api_key';
	const SETTING_KEY_SECRET_KEY = 'baidu_secret_key';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_API_KEY,
			self::SETTING_KEY_SECRET_KEY
		];
	}
}