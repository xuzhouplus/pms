<?php

namespace app\models;

use Faker\Provider\Uuid;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Yii;
use yii\base\UserException;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

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
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
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
				'value' => new Expression('NOW()')
			],
			'uuid' => [
				'class' => AttributeBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => 'uuid'
				],
				'value' => function ($event) {
					return str_replace('-', '', Uuid::uuid());
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
	 * @param bool $enabled
	 * @return Admin
	 */
	public static function findIdentity($id, $enabled = true)
	{
		$query = Admin::find();
		if (is_numeric($id)) {
			$query->where(['id' => $id]);
		} else {
			$query->where(['uuid' => $id]);
		}
		if ($enabled) {
			$query->andWhere(['status' => Admin::STATUS_ENABLED]);
		}
		return $query->one();
	}

	/**
	 * @param mixed $token
	 * @param null $type
	 * @return Admin|null
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		$data = Yii::$app->token->decode($token);
		if ($data) {
			$identity = self::findIdentity($data['id']);
			if ($identity) {
				$identity->token = $token;
				return $identity;
			}
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function generateAccessToken($expiresAt = null)
	{
		$data = [
			'id' => $this->uuid
		];
		if (!is_null($expiresAt)) {
			$data['expiresAt'] = $expiresAt;
		}
		return Yii::$app->token->encode($data);
	}

	public function getId()
	{
		return $this->uuid;
	}

	public function getAuthKey()
	{
		$authKey = Yii::$app->security->generateRandomKey();
		Yii::$app->cache->set(self::AUTH_KEY_CACHE_KEY . ':' . $authKey, [
			'id' => $this->uuid,
			'issued' => time()
		]);
		return $authKey;
	}

	/**
	 * @param string $authKey
	 * @return bool
	 * @throws \Exception
	 */
	public function validateAuthKey($authKey)
	{
		$authAdmin = Yii::$app->cache->get(self::AUTH_KEY_CACHE_KEY . ':' . $authKey);
		if ($authAdmin && $authAdmin['id'] == $this->uuid) {
			$loginDuration = Yii::$app->app->setting(Setting::SETTING_KEY_LOGIN_DURATION);
			if ($authAdmin['issued'] + $loginDuration > time()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $password
	 * @return bool
	 * @throws UserException
	 */
	public function validatePassword($password)
	{
		$privateKey = openssl_get_privatekey(file_get_contents(Yii::$aliases['@app'] . '/rsa_1024_priv.pem'));
		$rsaDecrypt = openssl_private_decrypt(base64_decode($password), $decrypted, $privateKey);
		if ($rsaDecrypt) {
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
	public static function add($data)
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
	public static function edit($data)
	{
		$admin = Admin::findIdentity($data['id'] ?? $data['uuid'], false);
		if ($admin) {
			$admin->load($data, '');
			$admin->setPassword($admin->password);
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
		$admin = Admin::findIdentity($id, false);
		if ($admin) {
			if ($admin->delete()) {
				return;
			}
			throw new UserException('Delete failed');
		}
		throw new UserException('Admin is not exist');
	}

	/**
	 * @param $account
	 * @param $password
	 * @return Admin
	 * @throws \Exception
	 */
	public static function login($account, $password)
	{
		/**
		 * @var $admin Admin
		 */
		$admin = Admin::find()->where(['account' => $account, 'status' => Admin::STATUS_ENABLED])->limit(1)->one();
		if (empty($admin)) {
			throw new UserException('Account is wrong');
		}
		if ($admin->validatePassword($password)) {
			return $admin;
		}
		throw new UserException('Account or password is wrong');
	}

	public function deleteConnects()
	{

	}

	public function disable($save = true)
	{
		$this->status = Admin::STATUS_DISABLED;
		if ($save) {
			$this->save();
		}
	}

	public function enable($save = true)
	{
		$this->status = Admin::STATUS_ENABLED;
		if ($save) {
			$this->save();
		}
	}
}
