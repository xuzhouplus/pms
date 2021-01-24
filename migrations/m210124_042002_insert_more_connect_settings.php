<?php

use yii\db\Migration;

/**
 * Class m210124_042002_insert_more_connect_settings
 */
class m210124_042002_insert_more_connect_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $settings = [
		    [
			    'key' => 'wechat_app_id',
			    'name' => '微信APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'wechat_app_secret',
			    'name' => '微信APP_SECRET',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'qq_app_id',
			    'name' => 'QQ APP_ID',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'qq_app_secret',
			    'name' => 'QQ APP_SECRET',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'weibo_app_id',
			    'name' => '微博APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'weibo_app_secret',
			    'name' => '微博APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'github_application_name',
			    'name' => 'GitHub应用名称',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'github_app_id',
			    'name' => 'GitHub APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'github_app_secret',
			    'name' => 'GitHub APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'facebook_app_id',
			    'name' => 'Facebook APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'facebook_app_secret',
			    'name' => 'Facebook APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'twitter_app_id',
			    'name' => 'Twitter APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'twitter_app_secret',
			    'name' => 'Twitter APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'line_app_id',
			    'name' => 'Line APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'line_app_secret',
			    'name' => 'Line APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'google_app_id',
			    'name' => 'Google APP_ID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'google_app_secret',
			    'name' => 'Google APP_SECRET',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ]
	    ];
	    $this->batchInsert(\app\models\Setting::tableName(), ['key', 'name', 'type', 'private', 'value', 'options'], $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210124_042002_insert_more_connect_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210124_042002_insert_more_connect_settings cannot be reverted.\n";

        return false;
    }
    */
}
