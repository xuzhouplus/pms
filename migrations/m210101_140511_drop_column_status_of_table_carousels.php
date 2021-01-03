<?php

use yii\db\Migration;

/**
 * Class m210101_140511_drop_column_status_of_table_carousels
 */
class m210101_140511_drop_column_status_of_table_carousels extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(\app\models\Carousel::tableName(), 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210101_140511_drop_column_status_of_table_carousels cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210101_140511_drop_column_status_of_table_carousels cannot be reverted.\n";

        return false;
    }
    */
}
