<?php

use yii\db\Migration;

/**
 * Class m210315_085246_alter_admin_add_column_salt
 */
class m210315_085246_alter_admin_add_column_salt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\app\models\Admin::tableName(), 'salt', \yii\db\mysql\Schema::TYPE_STRING);
        $this->addCommentOnColumn(\app\models\Admin::tableName(), 'salt', 'salt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210315_085246_alter_admin_add_column_salt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210315_085246_alter_admin_add_column_salt cannot be reverted.\n";

        return false;
    }
    */
}
