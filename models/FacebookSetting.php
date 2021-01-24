<?php


namespace app\models;

/**
 * Class FacebookSetting
 * @package app\models
 */
class FacebookSetting extends SettingModel
{
	const SETTING_KEY_FACEBOOK_APP_ID = 'facebook_app_id';
	const SETTING_KEY_FACEBOOK_APP_SECRET = 'facebook_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_FACEBOOK_APP_ID,
			self::SETTING_KEY_FACEBOOK_APP_SECRET
		];
	}
}