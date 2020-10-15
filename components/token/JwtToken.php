<?php


namespace app\components\token;


use app\models\Setting;
use Exception;
use Faker\Provider\Uuid;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\RegisteredClaims;
use Yii;
use yii\base\UserException;

class JwtToken extends BaseToken
{
	private Configuration $configuration;
	private $duration;
	private $issuedBy;
	const JWT_TOKEN_CACHE_KEY = 'jwt_token_cache:';

	public function init()
	{
		$secret = Yii::$app->app->setting(Setting::SETTING_KEY_ENCRYPT_SECRET);
		$this->configuration = Configuration::forSymmetricSigner(new Sha256(), new Key($secret));
		$this->duration = Yii::$app->app->setting(Setting::SETTING_KEY_LOGIN_DURATION);
		$this->issuedBy = Yii::$app->app->setting(Setting::SETTING_KEY_TITLE);
	}

	/**
	 * @param $data
	 * @return string
	 * @throws Exception
	 */
	public function encode($data)
	{
		$uuid = Uuid::uuid();
		$builder = $this->configuration->createBuilder();
		$builder->identifiedBy($uuid);
		$builder->issuedBy($this->issuedBy);
		$builder->permittedFor($this->issuedBy);
		if (empty($data['issuedAt'])) {
			$now = new \DateTimeImmutable();
		} else {
			$now = new \DateTimeImmutable($data['issuedAt']);
		}
		unset($data['issuedAt']);
		$builder->issuedAt($now);
		if (empty($data['expiresAt'])) {
			$duration = $this->duration;
			$builder->expiresAt($now->modify('+' . $this->duration . ' sec'));
		} else {
			$expireAt = new \DateTimeImmutable($data['expiresAt']);
			$duration = $expireAt->getTimestamp() - $now->getTimestamp();
			$builder->expiresAt($expireAt);
		}
		unset($data['expiresAt']);
		$builder->issuedAt($now);
		foreach ($data as $key => $value) {
			$builder->withClaim($key, $value);
		}
		$token = $builder->getToken($this->configuration->getSigner(), $this->configuration->getSigningKey());
		$tokenString = $token->toString();
		Yii::$app->cache->set(JwtToken::JWT_TOKEN_CACHE_KEY . $uuid, $data, $duration);
		return $tokenString;
	}

	/**
	 * @param $token
	 * @return mixed
	 * @throws Exception
	 */
	public function decode($token)
	{
		$now = new \DateTimeImmutable();
		$parser = $this->configuration->getParser()->parse($token);
		if ($parser->isExpired($now)) {
			throw new UserException('The token is expired');
		}
		$claims = $parser->claims()->all();
		if (empty($claims)) {
			throw new UserException('The token is incorrect');
		}
		if (isset($claims[RegisteredClaims::ID])) {
			$cache = Yii::$app->cache->get(JwtToken::JWT_TOKEN_CACHE_KEY . $claims[RegisteredClaims::ID]);
			if (empty($cache)) {
				throw new UserException('The token is unavailable');
			}
		}
		return $claims;
	}

	public function expire($token)
	{
		$claims = $this->decode($token);
		if (empty($claims)) {
			throw new UserException('The token is incorrect');
		}
		if (isset($claims[RegisteredClaims::ID])) {
			Yii::$app->cache->delete(JwtToken::JWT_TOKEN_CACHE_KEY . $claims[RegisteredClaims::ID]);
		}
	}

	public function delay($token)
	{
		$claims = $this->decode($token);
		if (empty($claims)) {
			throw new UserException('The token is incorrect');
		}
		if (isset($claims[RegisteredClaims::ID])) {
			Yii::$app->cache->set(JwtToken::JWT_TOKEN_CACHE_KEY . $claims[RegisteredClaims::ID], $claims, $this->duration);
		}
	}
}