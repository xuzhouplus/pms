<?php


namespace app\controllers;


use app\models\Carousel;

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
		$carousels = Carousel::list(null, 0, ['uuid', 'type', 'width', 'height', 'title', 'url', 'description'], null, true, 'order');
		return $this->response($carousels, 'succeed');
	}

	/**
	 * @return array
	 */
	public function actionList()
	{
		$request = \Yii::$app->request;
		$carousels = Carousel::list($request->getQueryParam('page'), $request->getQueryParam('limit'), null, $request->getQueryParam('like'), $request->getQueryParam('enable'));
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
		$carousel = Carousel::create(\Yii::$app->request->getBodyParams());
		return $this->response($carousel, 'succeed');
	}

	/**
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\base\UserException
	 */
	public function actionUpdate()
	{
		$carousel = Carousel::modify(\Yii::$app->request->getBodyParams());
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
		Carousel::remove(\Yii::$app->request->getBodyParam('id'));
		return $this->response(null, 'succeed');
	}

	public function actionToggle()
	{
		$carousel = Carousel::toggle(\Yii::$app->request->getBodyParam('id'));
		return $this->response($carousel, 'succeed');
	}

	public function actionMove(){

	}
}