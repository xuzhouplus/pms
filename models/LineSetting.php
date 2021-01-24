<?php


namespace app\models;

/**
 * Class LineSetting
 * @package app\models
 */
class LineSetting extends SettingModel
{
	const SETTING_KEY_LINE_APP_ID = 'line_app_id';
	const SETTING_KEY_LINE_APP_SECRET = 'line_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_LINE_APP_ID,
			self::SETTING_KEY_LINE_APP_SECRET
		];
	}
}