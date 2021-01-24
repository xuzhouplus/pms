<?php

use yii\db\Migration;

/**
 * Class m200929_082542_create_table_connects
 */
class m200929_082542_create_table_connects extends Migration
{
	private $tableName = '{{%connects}}';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB COMMENT ="第三方账号互联"';
		}
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(11),
			'admin_id' => $this->integer(11)->notNull()->comment('所属账号'),
			'type' => $this->string(32)->notNull()->defaultValue('wechat')->comment('对接类型，wechat微信，weibo微博，qq QQ'),
			'avatar' => $this->string(255)->notNull()->comment('头像'),
			'account' => $this->string(255)->notNull()->comment('账号'),
			'union_id' => $this->string(255)->notNull()->comment('三方授权唯一标识'),
			'status' => $this->tinyInteger(1)->defaultValue(1)->comment('状态，1启用，2禁用'),
			'created_at' => $this->dateTime()->comment('创建时间'),
			'updated_at' => $this->dateTime()->comment('更新时间')
		], $tableOptions);

		$this->createIndex('union_id', $this->tableName, 'union_id', true);
		$this->createIndex('admin_id', $this->tableName, 'admin_id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m200929_082542_create_table_connects cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m200929_082542_create_table_admins cannot be reverted.\n";

		return false;
	}
	*/
}
