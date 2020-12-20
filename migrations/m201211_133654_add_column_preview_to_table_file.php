<?php

use yii\db\Migration;

/**
 * Class m201211_133654_add_column_preview_to_table_file
 */
class m201211_133654_add_column_preview_to_table_file extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn(\app\models\File::tableName(), 'preview', \yii\db\mysql\Schema::TYPE_STRING);
		$this->addCommentOnColumn(\app\models\File::tableName(), 'preview', '预览图');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m201211_133654_add_column_preview_to_table_file cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m201211_133654_add_column_preview_to_table_file cannot be reverted.\n";

		return false;
	}
	*/
}
