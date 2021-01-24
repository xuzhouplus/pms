<?php


namespace app\models;

/**
 * Class WeiboSetting
 * @package app\models
 */
class WeiboSetting extends SettingModel
{
	const SETTING_KEY_WEIBO_APP_ID = 'weibo_app_id';
	const SETTING_KEY_WEIBO_APP_SECRET = 'weibo_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_WEIBO_APP_ID,
			self::SETTING_KEY_WEIBO_APP_SECRET
		];
	}
}