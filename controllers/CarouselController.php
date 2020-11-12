<?php


namespace app\controllers;


use app\models\Carousel;

class CarouselController extends RestController
{
	public array $except = [
		'index'
	];

	public function actionIndex()
	{
		$request = \Yii::$app->request;
		$carousels = Carousel::list($request->getQueryParam('page'), $request->getQueryParam('limit'), ['uuid', 'type', 'width', 'height', 'title', 'url', 'description'], $request->getQueryParam('like'), true);
		return $this->response($carousels, 'succeed');
	}

	public function actionList()
	{
		$request = \Yii::$app->request;
		$carousels = Carousel::list($request->getQueryParam('page'), $request->getQueryParam('limit'), null, $request->getQueryParam('like'), $request->getQueryParam('enable'));
		return $this->response($carousels, 'succeed');
	}

	public function actionCreate()
	{
		$carousel = Carousel::create(\Yii::$app->request->getBodyParams());
		return $this->response($carousel, 'succeed');
	}

	public function actionUpdate()
	{
		$carousel = Carousel::modify(\Yii::$app->request->getBodyParams());
		return $this->response($carousel, 'succeed');
	}

	public function actionDelete()
	{
		Carousel::remove(\Yii::$app->request->getBodyParam('id'));
		return $this->response(null, 'succeed');
	}
}