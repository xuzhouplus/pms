<?php

use yii\db\Migration;

/**
 * Class m200929_082542_create_table_admins
 */
class m200929_082542_create_table_admins extends Migration
{
    private $tableName = '{{%admins}}';

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
            'kind' => $this->tinyInteger(1)->defaultValue(2)->comment('类型，1超管，2普通'),
            'type' => $this->string('32')->notNull()->defaultValue('wechat')->comment('对接类型，wechat微信，weibo微博，qq QQ'),
            'account' => $this->string('255')->notNull()->comment('标题'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('状态，1启用，2禁用'),
            'created_at' => $this->dateTime()->comment('创建时间'),
            'updated_at' => $this->dateTime()->comment('更新时间')
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200929_082542_create_table_admins cannot be reverted.\n";

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
