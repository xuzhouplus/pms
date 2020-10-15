<?php


namespace app\components;

use Yii;
use yii\base\UserException;
use yii\web\Response;

/**
 * Class ErrorHandler
 * @package app\components\errorHandler
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
	protected function renderException($exception)
	{
		if (Yii::$app->has('response')) {
			$response = Yii::$app->getResponse();
			// reset parameters of response to avoid interference with partially created response data
			// in case the error occurred while sending the response.
			$response->isSent = false;
			$response->stream = null;
			$response->data = null;
			$response->content = null;
		} else {
			$response = new Response();
		}

		$response->setStatusCodeByException($exception);

		Yii::$app->view->clear();
		$result = Yii::$app->runAction($this->errorAction);
		if ($result instanceof Response) {
			$response = $result;
		} else {
			$response->data = $result;
		}

		$response->send();
	}

	/**
	 * 把框架自带的ErrorHandler类中的处理Exception返回格式的方法从protected改为public，方便errorAction组装响应结果数据
	 * @param \Error|\Exception $exception
	 * @return array
	 */
	public function convertExceptionToArray($exception)
	{
		return parent::convertExceptionToArray($exception);
	}
}