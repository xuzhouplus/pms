<?php

use yii\db\Migration;

/**
 * Class m210123_034704_insert_settings
 */
class m210123_034704_insert_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $settings = [
		    [
			    'key' => 'alipay_app_id',
			    'name' => '支付宝应用APPID',
			    'type' => \app\models\Setting::TYPE_INPUT,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'alipay_app_primary_key',
			    'name' => '支付宝应用私钥',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'alipay_public_key',
			    'name' => '支付宝公钥',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'baidu_pan_app_key',
			    'name' => '百度网盘APP_KEY',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
			    'private' => \app\models\Setting::PRIVATE_SETTING,
			    'value' => '',
			    'options' => ''
		    ],
		    [
			    'key' => 'baidu_pan_app_secret',
			    'name' => '百度网盘APP_SECRET',
			    'type' => \app\models\Setting::TYPE_TEXTAREA,
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
        echo "m210123_034704_insert_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210123_034704_insert_settings cannot be reverted.\n";

        return false;
    }
    */
}
