<?php


namespace app\models;


use Yii;
use yii\web\IdentityInterface;

class Identity implements IdentityInterface
{
	public string $uuid;
	public string $token;
	public Admin $admin;

	/**
	 * @param int|string $id
	 * @param bool $enabled
	 * @return Admin
	 */
	public static function findIdentity($id)
	{
		return Admin::findOneById($id, [], true);
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
			$admin = self::findIdentity($data['id']);
			if ($admin) {
				$identity = new Identity();
				$identity->uuid = $data['uuid'];
				$identity->admin = $admin;
				$identity->token = $token;
				return $identity;
			}
		}
		return null;
	}

	public function getId()
	{
		return $this->admin && $this->admin->uuid;
	}

	public function getAuthKey(): string
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
	public function validateAuthKey($authKey): bool
	{
		$authAdmin = Yii::$app->cache->get(self::AUTH_KEY_CACHE_KEY . ':' . $authKey);
		if ($authAdmin && $authAdmin['id'] == $this->uuid) {
			$loginDuration = Yii::$app->app->setting(SiteSetting::SETTING_KEY_LOGIN_DURATION);
			if ($authAdmin['issued'] + $loginDuration > time()) {
				return true;
			}
		}
		return false;
	}
}