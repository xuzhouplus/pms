<?php


namespace app\controllers;


class PostController extends RestController
{
	public array $except = [
		'index'
	];
public function actionIndex(){}
public function actionList(){}
public function actionCreate(){}
public function actionUpdate(){}
public function actionDelete(){}
}