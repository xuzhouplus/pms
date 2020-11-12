<?php


namespace app\controllers;


use app\models\File;

class FileController extends RestController
{
	public function actionIndex()
	{
		$request = \Yii::$app->request;
		$files = File::list($request->getQueryParam('page'), $request->getQueryParam('limit'), null, $request->getQueryParam('type'), $request->getQueryParam('name'));
		return $this->response($files, 'succeed');
	}

	public function actionUpload()
	{
		$file = new File();
		$file->load(\Yii::$app->request->post(), '');
		$file->upload();
		$data = $file->getAttributes();
		$data['url'] = $file->getUrl();
		unset($data['path']);
		return $this->response($data, 'Upload succeed');
	}

	public function actionDelete()
	{
		$file = File::find()->where(['id' => \Yii::$app->request->getBodyParam('id')])->limit(1)->one();
		if ($file) {
			$file->remove();
			$file->delete();
		}
		return $this->response(null, 'Delete succeed');
	}
}