<?php

namespace app\models;

use app\components\oauth2\gateway\AuthorizeUser;
use app\helpers\RsaHelper;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\UserException;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%admins}}".
 *
 * 管理账号
 * @property integer $id
 * @property string $uuid UUID
 * @property integer $type 类型，1超管，2普通
 * @property string $avatar 头像
 * @property string $account 账号
 * @property string $password 密码
 * @property integer $status 状态，1启用，2禁用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Admin extends \yii\db\ActiveRecord
{
	//用于保存认证用户的token
	public $token;
	const AUTH_KEY_CACHE_KEY = 'auth_key_cache';

	const TYPE_SUPER = 1;
	const TYPE_NORMAL = 2;
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%admins}}';
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'value' => date('Y-m-d H:i:s')
			],
			'uuid' => [
				'class' => AttributeBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => 'uuid'
				],
				'value' => function ($event) {
					return str_replace('-', '', Uuid::uuid4()->toString());
				}
			]
		];
	}

	public function getConnect()
	{
		return $this->hasMany(Connect::class, ['admin_id' => 'id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['type', 'status'], 'integer'],
			[['account', 'password'], 'required', 'on' => ['create', 'update']],
			[['created_at', 'updated_at'], 'safe'],
			[['avatar', 'account', 'password'], 'string', 'max' => 255],
			[['uuid'], 'string', 'max' => 32],
			['account', 'unique']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'type' => '类型，1超管，2普通',
			'avatar' => '头像',
			'account' => '账号',
			'password' => '密码',
			'uuid' => 'UUID',
			'status' => '状态，1启用，2禁用',
			'created_at' => '创建时间',
			'updated_at' => '更新时间',
		];
	}

	/**
	 * @param int|string $id
	 * @param array $select
	 * @param bool $enabled
	 * @return Admin
	 */
	public static function findOneById($id, $select = [], $enabled = true)
	{
		$query = Admin::find();
		if (is_numeric($id)) {
			$query->where(['id' => $id]);
		} else {
			$query->where(['uuid' => $id]);
		}
		if ($select) {
			$query->select($select);
		}
		if ($enabled) {
			$query->andWhere(['status' => Admin::STATUS_ENABLED]);
		}
		return $query->one();
	}

	/**
	 * @param array $extraData
	 * @param null $expiresAt
	 * @return array
	 */
	public function generateAccessToken($extraData = [], $expiresAt = null): array
	{
		$data = [
			'id' => $this->uuid
		];
		$data = array_merge($data, $extraData);
		if (!is_null($expiresAt)) {
			$data['expiresAt'] = $expiresAt;
		}
		return Yii::$app->token->encode($data);
	}

	/**
	 * @return mixed
	 */
	public function getRsaPublicKey()
	{
		return Yii::$app->security->decryptByKey($this->rsaKey['publicKey'], Yii::$app->app->setting('security.encryptSecret'));
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function getRsaPrivateKey()
	{
		return Yii::$app->security->decryptByKey($this->rsaKey['privateKey'], Yii::$app->app->setting('security.encryptSecret'));;
	}

	/**
	 * @param $password
	 * @return bool
	 * @throws UserException
	 */
	public function validatePassword($password): bool
	{
		return true;
		$privateKey = file_get_contents(Yii::$aliases['@app'] . '/rsa_1024_priv.pem');
		$decrypted = RsaHelper::privateDecode($password, $privateKey, true);
		if ($decrypted) {
			$password = $decrypted;
		} else {
			throw new UserException('Password is incredible');
		}
		return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * @param $password
	 * @throws \yii\base\Exception
	 */
	public function setPassword($password)
	{
		$this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
	}

	/**
	 * @param $data
	 * @return Admin
	 * @throws UserException|\yii\base\Exception
	 */
	public static function add($data): Admin
	{
		$exist = Admin::find()->where(['account' => $data['account']])->limit(1)->one();
		if ($exist) {
			throw new UserException('Admin account already existed');
		}
		$admin = new Admin();
		$admin->load($data, '');
		$admin->setPassword($admin->password);
		if ($admin->save()) {
			return $admin;
		}
		$errors = $admin->getFirstErrors();
		throw new UserException(reset($errors));
	}

	/**
	 * @param $data
	 * @return Admin
	 * @throws UserException|\yii\base\Exception
	 */
	public static function edit($data): Admin
	{
		$admin = Admin::findOneById($data['id'] ?? $data['uuid'], [], false);
		if ($admin) {
			if (empty($data['password'])) {
				unset($data['password']);
			} else {
				$privateKey = file_get_contents(Yii::$aliases['@app'] . '/rsa_1024_priv.pem');
				$decrypted = RsaHelper::privateDecode($data['password'], $privateKey, true);
				if ($decrypted) {
					$data['password'] = $decrypted;
				} else {
					throw new UserException('Password is incredible');
				}
			}
			if (!empty($data['avatar'])) {
				if (!empty($admin->avatar)) {
					$avatar = Yii::$app->upload->urlToPath($admin->avatar);
					if (file_exists($avatar)) {
						unlink($avatar);
					}
				}
				$filePath = Yii::$app->upload->saveBase64($data['avatar'], DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $admin->uuid);
				$data['avatar'] = Yii::$app->upload->pathToUrl($filePath);
			} else {
				unset($data['avatar']);
			}
			$admin->load($data, '');
			if (!empty($data['password'])) {
				$admin->setPassword($admin->password);
			}
			if ($admin->save()) {
				return $admin;
			}
			$errors = $admin->getFirstErrors();
			throw new UserException(reset($errors));
		}
		throw new UserException('Admin is not exist');
	}

	/**
	 * @param $id
	 * @return void
	 * @throws UserException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function del($id)
	{
		$admin = Admin::findOneById($id, [], false);
		if ($admin) {
			$transaction = Admin::getDb()->beginTransaction();
			try {
				if ($admin->delete()) {
					if (Connect::batchUnbind($admin->id)) {
						$transaction->commit();
						return;
					}
					throw new \Exception('删除第三方绑定失败');
				}
				throw new UserException('删除识别');
			} catch (\Exception $exception) {
				$transaction->rollBack();
				throw $exception;
			}
		}
		throw new UserException('账号不存在');
	}

	/**
	 * @param $account
	 * @param $password
	 * @return Admin
	 * @throws \Exception
	 */
	public static function login($account, $password): Admin
	{
		/**
		 * @var $admin Admin
		 */
		$admin = Admin::find()->where(['account' => $account, 'status' => Admin::STATUS_ENABLED])->limit(1)->one();
		if (empty($admin)) {
			throw new UserException('账号错误');
		}
		if ($admin->validatePassword($password)) {
			return $admin;
		}
		throw new UserException('账号或密码错误');
	}

	/**
	 * 获取授权跳转地址
	 * @param $type
	 * @param $scope
	 * @return string
	 * @throws \yii\base\Exception
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getAuthorizeUrl($type, $scope): string
	{
		$redirect = Yii::$app->params['hostDomain'] . '/backend/admin/bind/' . strtolower($type);
		$state = base64_encode(Yii::$app->security->generateRandomString());
		return Yii::$app->oauth2->getAuthorizeUrl($type, $scope, $redirect, $state);
	}

	/**
	 * 获取授权用户信息
	 * @param $type
	 * @param $grantType
	 * @return AuthorizeUser
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getAuthorizeUser($type, $grantType): AuthorizeUser
	{
		return Yii::$app->oauth2->getUserInfo($type, $grantType);
	}

	/**
	 * @param AuthorizeUser $authorizeUser
	 * @return Connect
	 * @throws \Exception
	 */
	public function bindConnect(AuthorizeUser $authorizeUser): Connect
	{
		return Connect::bind([
			'admin_id' => $this->id,
			'avatar' => $authorizeUser->avatar,
			'account' => $authorizeUser->nickname,
			'union_id' => $authorizeUser->union_id,
			'type' => $authorizeUser->type
		]);
	}

	/**
	 * @param $connectId
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function unbindConnect($connectId): bool
	{
		return Connect::unbind($this->id, $connectId);
	}

	/**
	 * @param null $indexBy
	 * @return Connect[]
	 */
	public function getBoundConnects($indexBy = null): array
	{
		return Connect::find()->where(['admin_id' => $this->id])->indexBy($indexBy)->all();
	}

	/**
	 * @param bool $save
	 */
	public function disable($save = true)
	{
		$this->status = Admin::STATUS_DISABLED;
		if ($save) {
			$this->save();
		}
	}

	/**
	 * @param bool $save
	 */
	public function enable($save = true)
	{
		$this->status = Admin::STATUS_ENABLED;
		if ($save) {
			$this->save();
		}
	}
}
