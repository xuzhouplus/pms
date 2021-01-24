<?php


namespace app\components;

use app\models\Setting;
use app\models\SiteSetting;
use Yii;
use yii\base\Component;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

class AppComponent extends Component
{
	public array $settings;

	/**
	 * @throws UserException
	 */
	public function init()
	{
		$this->installLock();
		$settings = Setting::getSettings();
		$this->settings = array_merge($settings, Yii::$app->params);
		if ($this->isUnderMaintenance()) {
			throw new UserException('The server is under maintenance');
		}
	}

	public function installLock($action = 'check')
	{
		$installLock = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'install.lock';
		if ($action == 'check') {
			if (file_exists($installLock)) {
				if (Yii::$app->request->getPathInfo() == 'app/init') {
					throw new UserException('The server side is already initialed');
				}
			} else {
				if (Yii::$app->request->getPathInfo() != 'app/init') {
					throw new UserException('The server side is not initialed');
				}
			}
		} else {
			file_put_contents($installLock, date('Y-m-d H:i:s'));
		}
	}

	/**
	 * @param $key
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function setting($key)
	{
		return ArrayHelper::getValue($this->settings, $key);
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function isUnderMaintenance()
	{
		return $this->setting(SiteSetting::SETTING_KEY_MAINTAIN) == SiteSetting::MAINTAIN_TRUE;
	}
}