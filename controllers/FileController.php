<?php


namespace app\controllers;


use app\models\File;
use yii\web\UploadedFile;

class FileController extends RestController
{

	public function actionIndex()
	{
		$file = new File();
		$file->load(\Yii::$app->request->post(), '');
		$file->upload();
		$data = $file->getAttributes();
		$data['url'] = $file->getUrl();
		unset($data['path']);
		return $this->response($data, 'Upload succeed');
	}
}