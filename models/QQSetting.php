<?php


namespace app\models;

/**
 * Class QQSetting
 * @package app\models
 */
class QQSetting extends SettingModel
{
	const SETTING_KEY_QQ_APP_ID = 'qq_app_id';
	const SETTING_KEY_QQ_APP_SECRET = 'qq_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_QQ_APP_ID,
			self::SETTING_KEY_QQ_APP_SECRET
		];
	}
}