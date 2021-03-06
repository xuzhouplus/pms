<?php

use yii\db\Migration;

/**
 * Class m200727_065404_create_table_settings
 */
class m200727_065404_create_table_settings extends Migration
{
	private $tableName = '{{%settings}}';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB COMMENT = "系统配置"';
		}
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(11),
			'key' => $this->string(32)->notNull()->unique()->comment('配置标识'),
			'name' => $this->string(255)->notNull()->unique()->comment('配置名称'),
			'type' => $this->string(20)->notNull()->defaultValue('input')->comment('配置类型，input输入框，radio单选框，checkbox复选框，select下拉选择，multiSelect多选下拉选择，textarea文本域'),
			'private' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('是否私有，1是，2否'),
			'value' => $this->text()->comment('配置值'),
			'options' => $this->string(255)->comment('配置选项'),
			'required' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('是否必填，1是，2否'),
			'description' => $this->string(255)->comment('配置说明')
		], $tableOptions);

		$settings = [
			[
				'key' => 'title',
				'name' => '站点名',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => 'Cool',
				'options' => ''
			],
			[
				'key' => 'icp',
				'name' => '备案号',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => '123456',
				'options' => ''
			],
			[
				'key' => 'version',
				'name' => '版本',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => 'v1',
				'options' => ''
			],
			[
				'key' => 'maintain',
				'name' => '维护状态',
				'type' => \app\models\Setting::TYPE_RADIO,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => \app\models\SiteSetting::MAINTAIN_FALSE,
				'options' => json_encode([
					\app\models\SiteSetting::MAINTAIN_FALSE => '否',
					\app\models\SiteSetting::MAINTAIN_TRUE => '是',
				])
			],
			[
				'key' => 'icon',
				'name' => 'ICON',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => '',
				'options' => ''
			],
			[
				'key' => 'logo',
				'name' => 'LOGO',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => '',
				'options' => ''
			],
			[
				'key' => 'login_duration',
				'name' => '登录有效时长',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PRIVATE_SETTING,
				'value' => 30 * 60,
				'options' => ''
			],
			[
				'key' => 'carousel_type',
				'name' => '轮播类型',
				'type' => \app\models\Setting::TYPE_SELECT,
				'private' => \app\models\Setting::PUBLIC_SETTING,
				'value' => 'webgl',
				'options' => json_encode([
					\app\models\Carousel::TYPE_WEBGL => 'webGL',
					\app\models\Carousel::TYPE_BOOTSTRAP => 'Bootstrap'
				])
			],
			[
				'key' => 'carousel_limit',
				'name' => '轮播数量限制',
				'type' => \app\models\Setting::TYPE_INPUT,
				'private' => \app\models\Setting::PRIVATE_SETTING,
				'value' => '3',
				'options' => ''
			],
		];
		$this->batchInsert($this->tableName, ['key', 'name', 'type', 'private', 'value', 'options'], $settings);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m200727_065404_create_table_settings cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m200727_065404_create_table_settings cannot be reverted.\n";

		return false;
	}
	*/
}
