<?php


namespace app\helpers;

use Endroid\QrCode\ErrorCorrectionLevel;
use Yii;
use yii\base\BaseObject;

class QrCode extends BaseObject
{
	public $size = 150;
	public $margin = 5;
	public $suffix = 'png';
	public $encoding = 'UTF-8';
	public $errorCorrectionLevel = 'low';
	public $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
	public $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];
	public $duration = 60;

	/**
	 * 生成二维码图片
	 * @param $string
	 * @return array
	 */
	public function generate($string)
	{
		// Create a basic QR code
		$qrCode = new \Endroid\QrCode\QrCode($string);
		$qrCode->setSize($this->size);
		$qrCode->setMargin($this->margin);
		// Set advanced options
		$qrCode->setWriterByName($this->suffix);
		$qrCode->setEncoding($this->encoding);
		$qrCode->setErrorCorrectionLevel(call_user_func([ErrorCorrectionLevel::class, strtoupper($this->errorCorrectionLevel)]));
		$qrCode->setForegroundColor($this->foregroundColor);
		$qrCode->setBackgroundColor($this->backgroundColor);
//		$qrCode->setLabel($string, 16, null, LabelAlignment::CENTER());
//		$qrCode->setLogoPath(Yii::getAlias('@backend') . '/web/img/edge.png');
//		$qrCode->setLogoSize(100, 100);
//		$qrCode->setValidateResult(false);

		// Round block sizes to improve readability and make the blocks sharper in pixel based outputs (like png).
		// There are three approaches:
		$qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
		$qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
		$qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary

		// Set additional writer options (SvgWriter example)
		$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

		// Directly output the QR code
		return [
			'type' => $qrCode->getContentType(),
			'code' => $qrCode->writeDataUri()
		];
	}

	public function createLogin($params)
	{
		//获取登录配置，以便在用户确认登录后返回登录跳转等信息
		$loginServer = new LoginServer($params['login_type'] ?? '', 'local');
		$loginConf = $loginServer->getLoginHostConf();
		//生成此次登录的uuid
		$loginUuid = Yii::$app->security->generateRandomString();
		//生成此次登录的token，用于验证APP发送的登录用户信息可信
		$loginToken = Yii::$app->security->encryptByKey($loginUuid, STRING_CRYPT_KEY);
		//二维码内容
		$qrCodeContent = [
			'type' => 'cmc_qr_code_login',//标识是cmc二维码登录
			'login_uuid' => $loginUuid,//登录uuid
			'login_token' => md5($loginToken)//登录token，不做md5不能生成二维码
		];
		//生成二维码
		$qrCode = $this->generate(Yii::$app->request->getHostInfo() . '/lr/login/qr-code-login?' . http_build_query($qrCodeContent));
		//缓存本次登录信息，过期时长用于保证二维码时效
		Yii::$app->cache->set('cmc_qr_code_login:' . $loginUuid, ['status' => 'start', 'login_id' => '', 'login_tid' => '', 'login_config' => $loginConf], $this->duration);
		return [
			'type' => $qrCode['type'],
			'qr_code' => $qrCode['code'],
			'login_uuid' => $loginUuid,
		];
	}

	/**
	 * 保存app端数据，更新扫码状态
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	public function setStatus()
	{
		$loginUuid = Yii::$app->request->getQueryParam('login_uuid');
		$loginToken = Yii::$app->request->getQueryParam('login_token');
		$type = Yii::$app->request->getQueryParam('type');
		$encryptString = Yii::$app->security->encryptByKey($loginUuid, STRING_CRYPT_KEY);
		if (md5($encryptString) == $loginToken) {
			$requestData = Yii::$app->request->getBodyParams();
			//
			if (empty($requestData)) {
				return [
					'code' => 10000,
					'message' => '请在APP端确认登录',
					'data' => [
						'type' => 'cmc_qr_code_login'
					]
				];
			}
			$loginStatus = Yii::$app->cache->get('cmc_qr_code_login:' . $loginUuid);
			$loginStatus = array_merge($loginStatus, $requestData);
			Yii::$app->cache->set('cmc_qr_code_login:' . $loginUuid, $loginStatus, $this->duration);
			return [
				'code' => 10000,
				'message' => '用户数据提交成功'
			];
		}
		return [
			'code' => 99999,
			'message' => '用户登录token无效'
		];
	}

	/**
	 * 获取验证结果
	 * @param $loginUuid
	 * @return array
	 * @throws \Exception
	 */
	public function getStatus($loginUuid)
	{
		$loginStatus = Yii::$app->cache->get('cmc_qr_code_login:' . $loginUuid);
		if (empty($loginStatus)) {
			return [
				'code' => 98701,
				'message' => '二维码已失效，请刷新'
			];
		}
		switch ($loginStatus['status']) {
			case 'start':
				return [
					'code' => 98702,
					'message' => '请使用APP扫码登录'
				];
			case 'scanned':
				return [
					'code' => 98703,
					'message' => '扫码成功，请在APP端确认登录'
				];
			case 'succeed':
				$loginServer = new LoginServer();
				$loginInfo = $loginServer->getLoginInfo($loginStatus['loginId'], $loginStatus['login_tid']);
				if ($loginInfo['code'] == 10000) {
					Yii::$app->cache->delete($loginUuid);
					$loginConf = $loginStatus['login_config'];
					$successback = $loginConf['successback'];
					if ($loginInfo['data']['login_mode'] == 2 && $loginInfo['data']['login_status'] == 1) {
						$successback = CMC_LOGIN_URL . '/login/login-product';
					}
					//登录成功
					if ($loginConf['is_agent_group']) {
						$auth_data = [
							'login_version' => 'v2',
							'successback' => $successback,
							'failback' => $loginConf['failback'],
							'login_id' => $loginInfo['data']['login_id'],
							'login_tid' => $loginInfo['data']['login_tid'],
							'login_type' => 'IdTid',
						];
						$successback = CMC_LOGIN_URL . '/lr/login/auth-login?' . http_build_query($auth_data);
						return [
							'code' => 20000,
							'data' => compact('successback'),
							'message' => ApiCode::$code[20000]
						];
					}
					return [
						'code' => 10000,
						'data' => compact('successback'),
						'message' => ApiCode::$code[10000]
					];
				} else {
					//登录失败
					return [
						'code' => $loginInfo['code'],
						'data' => $loginInfo['data'],
						'message' => $loginInfo['message']
					];
				}
			case 'failed':
				//登录失败
				return [
					'code' => 98704,
					'data' => [],
					'message' => 'APP拒绝了登录'
				];
		}
		return [
			'code' => 98702,
			'message' => '请使用APP扫码登录'
		];
	}
}