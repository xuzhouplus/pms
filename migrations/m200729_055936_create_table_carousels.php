<?php

use yii\db\Migration;

/**
 * Class m200729_055936_create_table_carousels
 */
class m200729_055936_create_table_carousels extends Migration
{
	private $tableName = '{{%carousels}}';

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
			'type' => $this->string('32')->notNull()->comment('类型，image图片，video视频，ad广告，html网页'),
			'title' => $this->string('255')->notNull()->comment('标题'),
			'width' => $this->integer('11')->comment('幅面宽'),
			'height' => $this->integer('11')->comment('幅面高'),
			'description' => $this->string('255')->comment('描述')
		], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_055936_create_table_carousels cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_055936_create_table_carousels cannot be reverted.\n";

        return false;
    }
    */
}
