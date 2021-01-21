<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%connects}}".
 *
 * 第三方账号互联
 * @property integer $id
 * @property integer $admin_id 所属账号
 * @property string $type 对接类型，wechat微信，weibo微博，qq QQ
 * @property string $avatar 头像
 * @property string $account 账号
 * @property string $open_id 三方授权唯一标识，微信、QQ为OpenID，微博为uid
 * @property integer $status 状态，1启用，2禁用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Connect extends \yii\db\ActiveRecord
{
	const CONNECT_TYPE_ALIPAY = 'alipay';
	const CONNECT_TYPE_WECHAT = 'wechat';
	const CONNECT_TYPE_WEIBO = 'weibo';
	const CONNECT_TYPE_QQ = 'qq';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%connects}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['admin_id', 'avatar', 'account', 'open_id'], 'required'],
			[['admin_id', 'status'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['type'], 'string', 'max' => 32],
			[['avatar', 'account', 'open_id'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'admin_id' => '所属账号',
			'type' => '对接类型，wechat微信，weibo微博，qq QQ',
			'avatar' => '头像',
			'account' => '账号',
			'open_id' => '三方授权唯一标识，微信、QQ为OpenID，微博为uid',
			'status' => '状态，1启用，2禁用',
			'created_at' => '创建时间',
			'updated_at' => '更新时间',
		];
	}

	/**
	 * @param $data
	 * @return Connect
	 * @throws \Exception
	 */
	public static function bind($data): Connect
	{
		$connect = new Connect();
		$connect->load($data, '');
		if ($connect->save()) {
			return $connect;
		}
		$errors = $connect->getFirstErrors();
		throw new \Exception(reset($errors));
	}

	/**
	 * 解绑
	 * @param $admin
	 * @param $connectId
	 * @return bool|int
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function unbind($admin, $connectId)
	{
		$connect = Connect::find()->where(['admin_id' => $admin, 'id' => $connectId])->one();
		if ($connect) {
			return $connect->delete();
		}
		return true;
	}

	/**
	 * 批量解绑
	 * @param $admin
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function batchUnbind($admin): bool
	{
		/**
		 * @var $connects Connect[]
		 */
		$connects = Connect::find()->where(['admin_id' => $admin])->all();
		if (empty($connects)) {
			return true;
		}
		foreach ($connects as $connect) {
			$connect->unbind();
			$connect->delete();
		}
		return true;
	}
}
