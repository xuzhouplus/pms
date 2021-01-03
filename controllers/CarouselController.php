<?php


namespace app\controllers;


use app\models\Carousel;
use app\models\File;
use Yii;

class CarouselController extends RestController
{
    public array $except = [
        'index'
    ];

    /**
     * @return array
     */
    public function actionIndex()
    {
        $carousels = Carousel::list(null, 0, ['uuid', 'type', 'width', 'height', 'title', 'url', 'link', 'description'], null, 'order');
        return $this->response($carousels, 'succeed');
    }

    /**
     * @return array
     */
    public function actionList()
    {
        $request = Yii::$app->request;
        $carousels = Carousel::list($request->getQueryParam('page'), $request->getQueryParam('limit'), null, $request->getQueryParam('like'));
        return $this->response($carousels, 'succeed');
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $carousel = Carousel::create(Yii::$app->request->getBodyParams());
        return $this->response($carousel, 'succeed');
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     */
    public function actionUpdate()
    {
        $carousel = Carousel::modify(Yii::$app->request->getBodyParams());
        return $this->response($carousel, 'succeed');
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\base\UserException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        Carousel::remove(Yii::$app->request->getBodyParam('id'));
        return $this->response(null, 'succeed');
    }

    public function actionPreview()
    {
        $file = File::find()->where(['id' => Yii::$app->request->getQueryParam('file_id')])->limit(1)->one();
        $carousel = Carousel::make($file);
        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, str_replace(Yii::$app->upload->host, Yii::$app->upload->path, $carousel['url']));
        Yii::$app->response->sendFile($filePath);
        return Yii::$app->response;
    }

    public function actionMove()
    {

    }
}