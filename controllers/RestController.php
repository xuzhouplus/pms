<?php


namespace app\controllers;


use ReflectionException;
use ReflectionMethod;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidConfigException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Action;
use yii\rest\Controller;
use yii\rest\OptionsAction;

class RestController extends Controller
{
    /**
     * 不需要认证的方法
     * @var string[]
     */
    public $except = [
    ];
    /**
     * 需要认证，但可以不认证通过的方法
     * @var string[]
     */
    public $optional = [
    ];

    /**
     * 设置认证方法
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class
            ],
            'optional' => $this->optional,
            'except' => $this->except
        ];

        return $behaviors;
    }

    /**
     * 定义方法的请求类型
     * @return array|string[]
     */
    protected function verbs()
    {
        return [
            'login' => ['POST']
        ];
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
}