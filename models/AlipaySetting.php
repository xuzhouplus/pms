<?php


namespace app\models;

/**
 * 支付宝对接配置
 * Class AlipaySetting
 * @package app\models
 */
class AlipaySetting extends Setting
{
    const SETTING_KEY_APPID = 'alipay_appid';//应用appid
    const SETTING_KEY_APP_PRIMARY_KEY = 'alipay_app_primary_key';//应用私钥
    const SETTING_KEY_ALIPAY_PUBLIC_KAY = 'alipay_public_key';//支付宝公钥

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find()
    {
        return parent::find()->where(['key' => [self::SETTING_KEY_APPID, self::SETTING_KEY_APP_PRIMARY_KEY, self::SETTING_KEY_ALIPAY_PUBLIC_KAY]])->limit(3);
    }
}