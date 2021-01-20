<?php


namespace app\models;

/**
 * 百度网盘对接配置
 * Class BaiduPanSetting
 * @package app\models
 */
class BaiduPanSetting extends Setting
{
    const SETTING_KEY_APP_KEY = 'baidu_pan_app_key';
    const SETTING_KEY_APP_SECRET = 'baidu_pan_app_secret';

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find()
    {
        return parent::find()->where(['key' => [self::SETTING_KEY_APP_KEY, self::SETTING_KEY_APP_SECRET]])->limit(2);
    }
}