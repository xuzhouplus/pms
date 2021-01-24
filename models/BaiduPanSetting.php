<?php


namespace app\models;

use yii\base\Model;

/**
 * 百度网盘对接配置
 * Class BaiduPanSetting
 * @package app\models
 */
class BaiduPanSetting extends SettingModel
{
	const SETTING_KEY_APP_KEY = 'baidu_pan_app_key';
	const SETTING_KEY_APP_SECRET = 'baidu_pan_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_APP_KEY,
			self::SETTING_KEY_APP_SECRET
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public static function find(): \yii\db\ActiveQuery
	{
		$types = self::types();
		return Setting::find()->where(['key' => $types])->limit(count($types));
	}
}