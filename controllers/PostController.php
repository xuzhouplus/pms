<?php


namespace app\controllers;


use app\models\Post;

class PostController extends RestController
{
	public array $except = [
		'index'
	];

	public function actionIndex()
	{
		$request = \Yii::$app->request;
		$posts = Post::list($request->getQueryParam('page'), $request->getQueryParam('limit'), ['uuid', 'title', 'sub_title', 'created_at', 'updated_at'], $request->getQueryParam('search'), true);
		return $this->response($posts);
	}

	public function actionList()
	{
		$request = \Yii::$app->request;
		$posts = Post::list($request->getQueryParam('page'), $request->getQueryParam('limit'), [], $request->getQueryParam('search'), $request->getQueryParam('enable'));
		return $this->response($posts);
	}

	public function actionCreate()
	{

	}

	public function actionUpdate()
	{
	}

	public function actionDelete()
	{
	}
}