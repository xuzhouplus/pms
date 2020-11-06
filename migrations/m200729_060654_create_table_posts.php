<?php

use yii\db\Migration;

/**
 * Class m200729_060654_create_table_posts
 */
class m200729_060654_create_table_posts extends Migration
{
	private $tableName = '{{%posts}}';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB COMMENT = "稿件"';
		}
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(11),
			'uuid'=>$this->string(32)->unique()->comment('uuid'),
			'type' => $this->string(32)->notNull()->defaultValue('html')->comment('类型，html普通，md Markdown'),
			'title' => $this->string(255)->notNull()->comment('标题'),
			'sub_title' => $this->string(255)->notNull()->comment('二级标题'),
			'content' => $this->text()->comment('内容'),
			'created_at' => $this->dateTime()->comment('创建时间'),
			'updated_at' => $this->dateTime()->comment('更新时间')
		], $tableOptions);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m200729_060654_create_table_posts cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m200729_060654_create_table_posts cannot be reverted.\n";

		return false;
	}
	*/
}
