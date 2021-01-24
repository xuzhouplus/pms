<?php

namespace app\helpers;
class RsaHelper
{
	/**
	 * RSA最大解密密文大小
	 */
	private const MAX_DECRYPT_BLOCK = 256;

	/**
	 * 私钥加密
	 * @param string $data 原文
	 * @param string $privateKey 私钥
	 * @param bool $base64Encode 是否把结果base64编码
	 * @return string       密文
	 */
	public static function privateEncode(string $data, string $privateKey, $base64Encode = false): string
	{
		$outVal = '';
		$res = openssl_pkey_get_private($privateKey);
		openssl_private_encrypt($data, $outVal, $res);
		if ($base64Encode) {
			$outVal = base64_encode($outVal);
		}
		return $outVal;
	}

	/**
	 * 公钥解密
	 * @param string $data 密文
	 * @param string $publicKey 公钥
	 * @param bool $base64Decode 是否需要先base64解码
	 * @return string       原文
	 */
	public static function publicDecode(string $data, string $publicKey, $base64Decode = false): string
	{
		$outVal = '';
		if ($base64Decode) {
			$data = base64_decode($data);
		}
		$res = openssl_pkey_get_public($publicKey);
		openssl_public_decrypt($data, $outVal, $res);
		return $outVal;
	}

	/**
	 * 公钥加密
	 * @param string $data 原文
	 * @param string $publicKey
	 * @param bool $base64Encode
	 * @return string       密文
	 */
	public static function publicEncode(string $data, string $publicKey, $base64Encode = false): string
	{
		$outVal = '';
		$res = openssl_pkey_get_public($publicKey);
		openssl_public_encrypt($data, $outVal, $res);
		if ($base64Encode) {
			$outVal = base64_encode($outVal);
		}
		return $outVal;
	}

	/**
	 * 私钥解密
	 * @param string $data 密文
	 * @param string $privateKey
	 * @param bool $base64Decode
	 * @return string       原文
	 */
	public static function privateDecode(string $data, string $privateKey, $base64Decode = false): string
	{
		if ($base64Decode) {
			$data = base64_decode($data);
		}
		$data = str_split($data, self::MAX_DECRYPT_BLOCK);
		$res = openssl_pkey_get_private($privateKey);
		$decrypted = '';
		foreach ($data as $block) {
			openssl_private_decrypt($block, $dataDecrypt, $res);
			$decrypted .= $dataDecrypt;
		}
		return $decrypted;
	}

	/**
	 * 创建一组公钥私钥
	 * @param null $newConfig openssl_pkey_new参数
	 * @param null $passPhrase openssl_pkey_export密钥加密
	 * @param null $exportConfig openssl_pkey_export参数
	 * @return array 公钥私钥数组
	 */
	public static function newKeyPair($newConfig = null, $passPhrase = null, $exportConfig = null): array
	{
		$res = openssl_pkey_new($newConfig);
		openssl_pkey_export($res, $privateKey, $passPhrase, $exportConfig);
		$d = openssl_pkey_get_details($res);
		return array(
			'privateKey' => $privateKey,
			'publicKey' => $d['key']
		);
	}


	public static function generateKeyPair($keyName)
	{
		if (!function_exists('exec')) {
			throw new \Exception('exec方法不可用');
		}
		$tmpDir = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'rsa';
		if (!file_exists($tmpDir)) {
			mkdir($tmpDir);
		}
		$privateKeyPath = $tmpDir . DIRECTORY_SEPARATOR . $keyName . "_priv.pem";
		$publicKeyPath = $tmpDir . DIRECTORY_SEPARATOR . $keyName . "_pub.pem";
		@exec("openssl genrsa -out " . $privateKeyPath . " 1024", $output, $code);
		if ($code === 0) {
			@exec("openssl rsa -pubout -in " . $privateKeyPath . " -out " . $publicKeyPath, $output, $code);
			if ($code === 0) {
				$privateKey = file_get_contents($privateKeyPath);
				$publicKey = file_get_contents($publicKeyPath);
				//unlink($privateKeyPath);
				//unlink($publicKeyPath);
				return compact('privateKey', 'publicKey');
			} else {
				\Yii::error($output);
				unlink($privateKeyPath);
				throw new \Exception('生成公钥失败');
			}
		}
		\Yii::error($output);
		throw new \Exception('生成私钥失败');
	}

}