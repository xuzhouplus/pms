<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Carousel;
use app\models\File;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionFix()
    {
        $name = [
            'bipenggou' => '毕棚沟',
            'dujiangyan' => '都江堰',
            'jianmenguan' => '剑门关',
            'langzhonggucheng' => '阆中古城',
            'emeishan' => '峨眉山',
            'huashan' => '华山',
            'uestc' => '电子科技大学',
            'yiheyuan' => '颐和园',
        ];
        /**
         * @var $file File
         */
        foreach (File::find()->each() as $file) {
            $file->description = $name[$file->name];
            $file->save();
            $carousel = Carousel::find()->where(['file_id' => $file->id])->limit(1)->one();
            $carousel->title = $file->description;
            $carousel->description = $file->description;
            $carousel->save();
        }
    }
}
