<?php


namespace app\models;

/**
 * 轮播配置
 * Class CarouselSetting
 * @package app\models
 */
class CarouselSetting extends Setting
{
    const SETTING_KEY_CAROUSEL_TYPE = 'carousel_type';//轮播类型
    const SETTING_KEY_CAROUSEL_LIMIT = 'carousel_limit';//轮播数量限制

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find()
    {
        return parent::find()->where(['key' => [self::SETTING_KEY_CAROUSEL_TYPE, self::SETTING_KEY_CAROUSEL_LIMIT]])->limit(2);
    }
}