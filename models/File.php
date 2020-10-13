<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property int $id
 * @property string $type 文件类型
 * @property string $name 文件名
 * @property string|null $path 文件路径
 * @property int|null $width 幅面宽
 * @property int|null $height 幅面高
 * @property string|null $description 说明
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['width', 'height'], 'integer'],
            [['type'], 'string', 'max' => 32],
            [['name', 'path', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '文件类型',
            'name' => '文件名',
            'path' => '文件路径',
            'width' => '幅面宽',
            'height' => '幅面高',
            'description' => '说明',
        ];
    }
}
