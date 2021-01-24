<?php


namespace app\components\token;


use app\models\SiteSetting;
use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;

class CacheToken extends BaseToken
{
	private $secret;
	private $duration;
	const CACHE_TOKEN_CACHE_KEY = 'cache_token:';

	public function init()
	{
		parent::init();
		$this->secret = Yii::$app->params['security']['encryptSecret'];
		$this->duration = Yii::$app->app->setting(SiteSetting::SETTING_KEY_LOGIN_DURATION);
	}

	public function encode($data)
	{
		if (empty($data['issuedAt'])) {
			$now = new \DateTimeImmutable();
		} else {
			$now = new \DateTimeImmutable($data['issuedAt']);
		}
		unset($data['issuedAt']);
		if (empty($data['expiresAt'])) {
			$duration = $this->duration;
		} else {
			$expireAt = new \DateTimeImmutable($data['expiresAt']);
			$duration = $expireAt->getTimestamp() - $now->getTimestamp();
		}
		unset($data['expiresAt']);
		$uuid = Uuid::uuid();
		$data['uuid'] = $uuid;
		Yii::$app->cache->set(CacheToken::CACHE_TOKEN_CACHE_KEY . $uuid, $data, $duration);
		$tokenString = base64_encode(Yii::$app->security->encryptByKey($uuid, $this->secret));
		return ['uuid'=>$uuid,'token'=>$tokenString];
	}

	public function decode($token)
	{
		$encryptString = base64_decode($token);
		$decryptString = Yii::$app->security->decryptByKey($encryptString, $this->secret);
		if (!$decryptString) {
			throw new UserException('The token is incorrect');
		}
		$cache = Yii::$app->cache->get(CacheToken::CACHE_TOKEN_CACHE_KEY . $decryptString);
		if (empty($cache)) {
			throw new UserException('The token is unavailable');
		}
		return $cache;
	}

	public function delay($token)
	{
		$claims = $this->decode($token);
		if (empty($claims)) {
			throw new UserException('The token is expired');
		}
		Yii::$app->cache->set(CacheToken::CACHE_TOKEN_CACHE_KEY . $claims['uuid'], $claims, $this->duration);
	}

	public function expire($token = null)
	{
		if (is_null($token)) {
			$token = $this->value();
		}
		if ($token) {
			$claims = $this->decode($token);
			if ($claims) {
				Yii::$app->cache->delete(CacheToken::CACHE_TOKEN_CACHE_KEY . $claims['uuid']);
			}
		}
	}
}