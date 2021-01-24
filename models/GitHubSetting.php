<?php


namespace app\models;

/**
 * Class GitHubSetting
 * @package app\models
 */
class GitHubSetting extends SettingModel
{
	const SETTING_KEY_GITHUB_APPLICATION_NAME = 'github_application_name';
	const SETTING_KEY_GITHUB_APP_ID = 'github_app_id';
	const SETTING_KEY_GITHUB_APP_SECRET = 'github_app_secret';

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_GITHUB_APPLICATION_NAME,
			self::SETTING_KEY_GITHUB_APP_ID,
			self::SETTING_KEY_GITHUB_APP_SECRET,
		];
	}
}