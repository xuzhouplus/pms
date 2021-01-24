<?php


namespace app\models;

/**
 * 轮播配置
 * Class CarouselSetting
 * @package app\models
 */
class CarouselSetting extends SettingModel
{
	const SETTING_KEY_CAROUSEL_TYPE = 'carousel_type';//轮播类型
	const SETTING_KEY_CAROUSEL_LIMIT = 'carousel_limit';//轮播数量限制

	/**
	 * @return string[]
	 */
	public static function types(): array
	{
		return [
			self::SETTING_KEY_CAROUSEL_TYPE,
			self::SETTING_KEY_CAROUSEL_LIMIT
		];
	}
}