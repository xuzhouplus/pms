<?php

use yii\db\Migration;

/**
 * Class m201009_052600_create_table_admins
 */
class m201009_052600_create_table_admins extends Migration
{
	private $tableName = '{{%admins}}';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB COMMENT ="管理账号"';
		}
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(11),
			'uuid' => $this->string(32)->unique()->comment('UUID'),
			'type' => $this->tinyInteger(1)->defaultValue(2)->comment('类型，1超管，2普通'),
			'avatar' => $this->string(255)->comment('头像'),
			'account' => $this->string(255)->notNull()->comment('账号'),
			'password' => $this->string(255)->notNull()->comment('密码'),
			'status' => $this->tinyInteger(1)->defaultValue(1)->comment('状态，1启用，2禁用'),
			'created_at' => $this->dateTime()->comment('创建时间'),
			'updated_at' => $this->dateTime()->comment('更新时间')
		], $tableOptions);
		$admin = new \app\models\Admin();
		$admin->setScenario('create');
		$admin->account = 'admin';
		$admin->setPassword('123456');
		$admin->status = \app\models\Admin::STATUS_ENABLED;
		$admin->type = \app\models\Admin::TYPE_SUPER;
		$admin->save();
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m201009_052600_create_table_admins cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m201009_052600_create_table_admins cannot be reverted.\n";

		return false;
	}
	*/
}
