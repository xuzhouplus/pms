<?php

use yii\db\Migration;

/**
 * Class m200729_030821_create_table_files
 */
class m200729_030821_create_table_files extends Migration
{
	private $tableName = '{{%files}}';

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
			'type' => $this->string('32')->notNull()->comment('文件类型'),
			'name' => $this->string('255')->notNull()->comment('文件名'),
			'path' => $this->string('255')->comment('文件路径'),
			'width' => $this->integer('11')->comment('幅面宽'),
			'height' => $this->integer('11')->comment('幅面高'),
			'description' => $this->string('255')->comment('说明')
		], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_030821_create_table_files cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_030821_create_table_files cannot be reverted.\n";

        return false;
    }
    */
}
