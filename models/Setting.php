<?php

namespace app\models;

use Cassandra\Set;
use Yii;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property int $id
 * @property string $key 配置标识
 * @property string $name 配置名称
 * @property string $type 配置类型，input输入框，radio单选框，checkbox复选框，select下拉选择，multiSelect多选下拉选择，textarea文本域
 * @property string|null $value 配置值
 * @property string|null $options 配置选项
 * @property string|null $description 配置说明
 */
class Setting extends \yii\db\ActiveRecord
{
    const TYPE_INPUT = 'input';
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_MULTI_SELECT = 'multiSelect';
    const TYPE_TEXTAREA = 'textarea';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name', 'value', 'options', 'description'], 'string', 'max' => 255],
            ['type', 'in', 'range' => [self::TYPE_INPUT, self::TYPE_RADIO, self::TYPE_CHECKBOX, self::TYPE_SELECT, self::TYPE_MULTI_SELECT, self::TYPE_TEXTAREA]],
            [['key'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Setting Key',
            'name' => 'Setting Name',
            'type' => 'Setting Type',
            'value' => 'Setting Value',
            'options' => 'Setting Options',
            'description' => 'Setting Description',
        ];
    }
}
