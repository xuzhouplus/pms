<?php


namespace app\models;


use app\helpers\RsaHelper;
use Yii;

class SettingModel extends \yii\base\Model
{
	public static function encrypt($decrypted)
	{
		return base64_encode(Yii::$app->security->encryptByKey($decrypted, Yii::$app->app->setting('security.encryptSecret')));
	}

	public static function decrypt($encrypted, $type = 'rsa')
	{
		if ($type == 'rsa') {
			$privateKey = file_get_contents(Yii::$aliases['@app'] . '/rsa_1024_priv.pem');
			if (is_array($encrypted)) {
				$result = '';
				foreach ($encrypted as $split) {
					$result .= RsaHelper::privateDecode($split, $privateKey, true);
				}
				return $result;
			}
			return RsaHelper::privateDecode($encrypted, $privateKey, true);
		}
		$base64Decode = base64_decode($encrypted);
		return Yii::$app->security->decryptByKey($base64Decode, Yii::$app->app->setting('security.encryptSecret'));
	}

	/**
	 * @param null $indexBy
	 * @return Setting[]
	 */
	public static function find($indexBy = null): array
	{
		$types = static::types();
		return Setting::find()->where(['key' => $types])->limit(count($types))->indexBy($indexBy)->all();
	}

	/**
	 * @param $setKeyPairs
	 * @throws \yii\db\Exception
	 */
	public static function save($setKeyPairs)
	{
		$settings = self::find('key');
		$transaction = Setting::getDb()->beginTransaction();
		try {
			$setKeyPairs = array_filter($setKeyPairs);
			foreach ($setKeyPairs as $setKey => $setValue) {
				if (isset($settings[$setKey])) {
					$setting = $settings[$setKey];
				} else {
					$setting = new Setting();
					$setting->key = $setKey;
				}
				$setting->value = $setValue;
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
}