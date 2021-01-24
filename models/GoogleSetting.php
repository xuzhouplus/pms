<?php


namespace app\models;

/**
 * Class GoogleSetting
 * @package app\models
 */
class GoogleSetting extends SettingModel
{
	const SETTING_KEY_GOOGLE_APP_ID = 'google_app_id';
	const SETTING_KEY_GOOGLE_APP_SECRET = 'google_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_GOOGLE_APP_ID,
			self::SETTING_KEY_GOOGLE_APP_SECRET
		];
	}
}