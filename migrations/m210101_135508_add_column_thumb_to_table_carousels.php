<?php

use yii\db\Migration;

/**
 * Class m210101_135508_add_column_thumb_to_table_carousels
 */
class m210101_135508_add_column_thumb_to_table_carousels extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\app\models\Carousel::tableName(), 'thumb', \yii\db\mysql\Schema::TYPE_STRING);
        $this->addCommentOnColumn(\app\models\Carousel::tableName(), 'thumb', '预览图');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210101_135508_add_column_thumb_to_table_carousels cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210101_135508_add_column_thumb_to_table_carousels cannot be reverted.\n";

        return false;
    }
    */
}
