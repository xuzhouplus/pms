<?php

namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%settings}}".
 *
 * 系统配置
 * @property integer $id
 * @property string $key 配置标识
 * @property string $name 配置名称
 * @property string $type 配置类型，input输入框，radio单选框，checkbox复选框，select下拉选择，multiSelect多选下拉选择，textarea文本域
 * @property integer $private 是否私有，1是，2否
 * @property string $value 配置值
 * @property string $options 配置选项
 * @property string $description 配置说明
 */
class Setting extends \yii\db\ActiveRecord
{
	//输入框类型
	const TYPE_INPUT = 'input';
	const TYPE_RADIO = 'radio';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_SELECT = 'select';
	const TYPE_MULTI_SELECT = 'multiSelect';
	const TYPE_TEXTAREA = 'textarea';

	//私有
	const PRIVATE_SETTING = 1;
	//公开
	const PUBLIC_SETTING = 2;

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
			[['name', 'value', 'options', 'description'], 'string'],
			['type', 'default', 'value' => self::TYPE_INPUT],
			['type', 'in', 'range' => [self::TYPE_INPUT, self::TYPE_RADIO, self::TYPE_CHECKBOX, self::TYPE_SELECT, self::TYPE_MULTI_SELECT, self::TYPE_TEXTAREA]],
			['private', 'default', 'value' => self::PRIVATE_SETTING],
			['private', 'in', 'range' => [self::PRIVATE_SETTING, self::PUBLIC_SETTING]],
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

	/**
	 * @return Setting[]
	 */
	public static function getPublicSettings()
	{
		return self::find()->where(['private' => self::PUBLIC_SETTING])->all();
	}

	/**
	 * @param $key
	 * @return mixed|null
	 */
	public static function getSetting($key)
	{
		$setting = self::find()->where(['key' => $key])->limit(1)->one();
		if ($setting) {
			return $setting->value;
		}
		return null;
	}

	/**
	 * @param array $keys
	 * @return array
	 */
	public static function getSettings($keys = [])
	{
		$setting = self::find()->andFilterWhere(['key' => $keys])->all();
		return ArrayHelper::map($setting, 'key', 'value');
	}

	public static function saveValue($keyValuePairs)
	{
		if (!is_array($keyValuePairs)) {
			throw new InvalidArgumentException();
		}
		$transaction = Setting::getDb()->beginTransaction();
		try {
			$keys = array_keys($keyValuePairs);
			$settings = Setting::find()->where(['key' => $keys])->limit(count($keys))->all();
			foreach ($settings as $setting) {
				$setting->value = ArrayHelper::getValue($keyValuePairs, $setting->key);
				if (!$setting->save()) {
					$errors = $setting->getFirstErrors();
					throw new \Exception(reset($errors));
				}
			}
			$transaction->commit();
		} catch (\Exception $exception) {
			$transaction->rollBack();
			throw $exception;
		}
	}

	public static function add($data)
	{
		$exist = Setting::find()->where(['key' => $data['key']])->limit(1)->one();
		if ($exist) {
			throw new UserException('Setting key already existed');
		}
		$setting = new Setting();
		$setting->load($data, '');
		if ($setting->save()) {
			return $setting;
		}
		$errors = $setting->getFirstErrors();
		throw new UserException(reset($errors));
	}

	public static function edit($data)
	{
		$setting = Setting::find()->where(['key' => $data['key']])->limit(1)->one();
		if (!$setting) {
			throw new UserException('Setting not exists');
		}
		$setting->load($data, '');
		if ($setting->save()) {
			return $setting;
		}
		$errors = $setting->getFirstErrors();
		throw new UserException(reset($errors));
	}

	public static function del($key)
	{
		$setting = Setting::find()->where(['key' => $key])->limit(1)->one();
		if (!$setting) {
			throw new UserException('Setting not exists');
		}
		if (!$setting->delete()) {
			throw new UserException('Delete setting failed');
		}
	}
}
