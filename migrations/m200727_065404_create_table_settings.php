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
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(11),
			'key' => $this->string('32')->notNull()->unique()->comment('配置标识'),
			'name' => $this->string('255')->notNull()->unique()->comment('配置名称'),
			'type' => $this->string('10')->notNull()->defaultValue('input')->comment('配置类型，input输入框，radio单选框，checkbox复选框，select下拉选择，multiSelect多选下拉选择，textarea文本域'),
			'value' => $this->string('255')->comment('配置值'),
			'options' => $this->string('255')->comment('配置选项'),
			'description' => $this->string('255')->comment('配置说明')
		], $tableOptions);

		$this->createIndex('KeyIndex', $this->tableName, ['key']);
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
