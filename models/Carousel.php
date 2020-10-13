<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%carousels}}".
 *
 * @property int $id
 * @property string $type 类型，image图片，video视频，ad广告，html网页
 * @property string $title 标题
 * @property int|null $width 幅面宽
 * @property int|null $height 幅面高
 * @property string|null $description 描述
 */
class Carousel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%carousels}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'title'], 'required'],
            [['width', 'height'], 'integer'],
            [['type'], 'string', 'max' => 32],
            [['title', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'title' => 'Title',
            'width' => 'Width',
            'height' => 'Height',
            'description' => 'Description',
        ];
    }
}
