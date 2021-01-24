<?php


namespace app\models;

/**
 * Class TwitterSetting
 * @package app\models
 */
class TwitterSetting extends SettingModel
{
	const SETTING_KEY_TWITTER_APP_ID = 'twitter_app_id';
	const SETTING_KEY_TWITTER_APP_SECRET = 'twitter_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_TWITTER_APP_ID,
			self::SETTING_KEY_TWITTER_APP_SECRET
		];
	}
}