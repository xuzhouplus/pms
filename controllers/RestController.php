<?php


namespace app\controllers;


use app\behaviors\authenticators\CookieTokenAuth;
use ReflectionException;
use ReflectionMethod;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Action;
use yii\rest\ActiveController;
use yii\rest\OptionsAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RestController extends ActiveController
{
	public $enableCsrfValidation = YII_ENV_PROD;

	public $modelClass = '';
	public $responseOK = 1;
	public $responseFail = 0;

	public array $authMethods = [];
	/**
	 * 不需要认证的方法
	 * @var string[]
	 */
	public array $except = [];
	/**
	 * 需要认证，但可以不认证通过的方法
	 * @var string[]
	 */
	public array $optional = [];
	/**
	 * 方法请求类型限制
	 * @var array
	 */
	public array $verbs = [];

	/**
	 * 设置认证方法
	 * @return array
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		$behaviors['authenticator'] = [
			'class' => CompositeAuth::class,
			'authMethods' => array_merge($this->authMethods, [HttpBearerAuth::class]),
			'optional' => $this->optional,
			'except' => array_merge($this->except, ['error'])
		];

		return $behaviors;
	}

	/**
	 * 定义方法的请求类型
	 * @return array|string[]
	 */
	protected function verbs()
	{
		return $this->verbs;
	}

	/**
	 * 定义方法
	 */
	public function actions()
	{
		return [
			'options' => [
				'class' => OptionsAction::class,
			]
		];
	}

	/**
	 * 解析路由
	 * @param string $id
	 * @return null|object|Action|InlineAction
	 * @throws InvalidConfigException
	 * @throws ReflectionException
	 */
	public function createAction($id)
	{
		if ($id === '') {
			$id = $this->defaultAction;
		}
		$actionMap = $this->actions();
		if (isset($actionMap[lcfirst($id)])) {
			$action = lcfirst($id);
			return Yii::createObject($actionMap[$action], [$action, $this]);
		} elseif (isset($actionMap[ucfirst($id)])) {
			$action = ucfirst($id);
			return Yii::createObject($actionMap[$action], [$action, $this]);
		} elseif (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
			$methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
			if (method_exists($this, $methodName)) {
				$method = new ReflectionMethod($this, $methodName);
				if ($method->isPublic() && $method->getName() === $methodName) {
					return new InlineAction($id, $this, $methodName);
				}
			}
		} else {
			$methodName = 'action' . ucfirst($id);
			if (method_exists($this, $methodName)) {
				$method = new ReflectionMethod($this, $methodName);
				if ($method->isPublic() && $method->getName() === $methodName) {
					return new InlineAction($id, $this, $methodName);
				}
			}
		}
		return null;
	}

	/**
	 * 标准响应
	 * @param null $data
	 * @param null $code
	 * @param null $message
	 * @param int $status
	 * @return array
	 */
	protected function response($data = null, $code = null, $message = null, $status = 200)
	{
		if (is_string($code)) {
			$message = $code;
			$code = null;
		}
		return [
			'data' => $data,
			'message' => $message ?: '',
			'code' => $code ?: $this->responseOK,
			'status' => $status
		];
	}

	/**
	 * 所有的可捕获异常（UserException）都在这里处理
	 * @return array
	 */
	public function actionError()
	{
		$errorhandler = Yii::$app->errorHandler;
		if (($exception = $errorhandler->exception) === null) {
			$exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		if ($exception instanceof UserException) {
			Yii::$app->response->setStatusCode(400);
		}
		return $errorhandler->convertExceptionToArray($exception);
	}
}