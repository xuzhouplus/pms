<?php

namespace app\controllers;

use app\models\Test;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	/**
	 * Displays homepage.
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		return $this->render('index');
	}

	/**
	 * Login action.
	 *
	 * @return Response|string
	 */
	public function actionLogin()
	{
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}

		$model->password = '';
		return $this->render('login', [
			'model' => $model,
		]);
	}

	/**
	 * Logout action.
	 *
	 * @return Response
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

	/**
	 * Displays contact page.
	 *
	 * @return Response|string
	 */
	public function actionContact()
	{
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
			Yii::$app->session->setFlash('contactFormSubmitted');

			return $this->refresh();
		}
		return $this->render('contact', [
			'model' => $model,
		]);
	}

	/**
	 * Displays about page.
	 *
	 * @return string
	 */
	public function actionAbout()
	{
		return $this->render('about');
	}

	public function actionProperty($table = null)
	{
		if (!YII_ENV_DEV) {
			return $this->redirect(['site/index']);
		}
		$db = \Yii::$app->getDb();
		if (is_null($table)) {
			$tableSchemas = $db->getSchema()->getTableSchemas();

		} else {
			$tableSchemas = [$db->getSchema()->getTableSchema($table)];
		}
		foreach ($tableSchemas as $tableSchema) {
			echo '<h3 style="border-bottom: 1px solid #ccc">' . $tableSchema->fullName . '</h3>';
			$databases = $db->createCommand('SELECT TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_NAME=:tableName', [':tableName' => $tableSchema->fullName])->queryOne();
			$tableProperty = '* ' . $databases['TABLE_COMMENT'] . PHP_EOL . '<br/>';
			foreach ($tableSchema->columns as $column) {
				$tableProperty .= '* ';
				$tableProperty .= '@property ';
				$tableProperty .= $column->phpType;
				$tableProperty .= ' $';
				$tableProperty .= $column->name;
				$tableProperty .= ' ';
				$tableProperty .= $column->comment;
				$tableProperty .= PHP_EOL;
				$tableProperty .= '<br/>';
			}
			echo '<p>' . $tableProperty . '</p>';
		}
	}

	public function actionTest()
	{
		$test = new Test();
		$test->load(['with_scenario' => 'with_scenario', 'without_scenario' => 'without_scenario'], '');
		print_r($test->getAttributes());
	}
}
