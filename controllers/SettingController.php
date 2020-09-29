<?php

namespace app\controllers;

use app\helpers\Response;
use app\models\Setting;
use yii\helpers\ArrayHelper;

class SettingController extends RestController
{
    public $except = [
        'index'
    ];

    public function actionIndex()
    {
        $settings = Setting::find()->select(['key', 'value'])->all();
        return [
            'data' => ArrayHelper::map($settings, 'key', 'value'),
            'code' => Response::CODE_SUCCESS
        ];
    }
}
