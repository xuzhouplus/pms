<?php


namespace app\models;

/**
 * 站点配置
 * Class SiteSetting
 * @package app\models
 */
class SiteSetting extends Setting
{
    //维护状态
    const MAINTAIN_TRUE = 'true';
    const MAINTAIN_FALSE = 'false';

    const SETTING_KEY_TITLE = 'title';//站点名
    const SETTING_KEY_ICP = 'icp';//备案号
    const SETTING_KEY_VERSION = 'version';//版本
    const SETTING_KEY_MAINTAIN = 'maintain';//维护状态
    const SETTING_KEY_ICON = 'icon';
    const SETTING_KEY_LOGO = 'logo';
    const SETTING_KEY_ENCRYPT_SECRET = 'encrypt_secret';//加密密钥
    const SETTING_KEY_LOGIN_DURATION = 'login_duration';//登录有效时长

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find(){
        return parent::find()->where(['key'=>[self::SETTING_KEY_TITLE,self::SETTING_KEY_ICP,self::SETTING_KEY_VERSION,self::SETTING_KEY_MAINTAIN,self::SETTING_KEY_ICON,self::SETTING_KEY_LOGO,self::SETTING_KEY_ENCRYPT_SECRET,self::SETTING_KEY_LOGIN_DURATION]])->limit(8);
    }
}