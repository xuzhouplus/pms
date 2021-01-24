<?php


namespace app\models;

/**
 * Class WechatSetting
 * @package app\models
 */
class WechatSetting extends SettingModel
{
	const SETTING_KEY_WECHAT_APP_ID = 'wechat_app_id';
	const SETTING_KEY_WECHAT_APP_SECRET = 'wechat_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_WECHAT_APP_ID,
			self::SETTING_KEY_WECHAT_APP_SECRET
		];
	}
}