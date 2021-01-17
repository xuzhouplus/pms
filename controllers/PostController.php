<?php


namespace app\controllers;


use app\models\Post;
use yii\base\UserException;

class PostController extends RestController
{
    public array $except = [
        'index',
        'detail'
    ];

    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $posts = Post::list($request->getQueryParam('page'), $request->getQueryParam('limit'), ['uuid', 'cover', 'title', 'sub_title', 'created_at', 'updated_at'], $request->getQueryParam('search'), true);
        return $this->response($posts);
    }

    public function actionList()
    {
        $request = \Yii::$app->request;
        $posts = Post::list($request->getQueryParam('page'), $request->getQueryParam('limit'), [], $request->getQueryParam('search'), $request->getQueryParam('enable'));
        return $this->response($posts);
    }

    public function actionInfo()
    {
        $request = \Yii::$app->request;
        $post = Post::findOneById($request->getQueryParam('id'));
        if ($post) {
            return $this->response($post);
        }
        throw new UserException('文稿不存在');
    }

    public function actionDetail()
    {
        $request = \Yii::$app->request;
        $id = $request->getQueryParam('id');
        if (strlen($id) == 32) {
            $post = Post::findOneByUuid($id);
        } else {
            $post = Post::findOneById($id);
        }
        if ($post) {
            return $this->response($post);
        }
        throw new UserException('文稿不存在');
    }

    public function actionSave()
    {
        $request = \Yii::$app->request;
        $post = Post::savePost($request->getBodyParams());
        return $this->response($post);
    }

    public function actionDelete()
    {
        $request = \Yii::$app->request;
        $post = Post::findOneById($request->getBodyParam('id'));
        if ($post) {
            if ($post->delete()) {
                return $this->response($post);
            }
            throw new \Exception('删除失败');
        }
        throw new UserException('文稿不存在');
    }

    public function actionToggleStatus()
    {
        $request = \Yii::$app->request;
        $post = Post::toggleStatus($request->getBodyParam('id'));
        if ($post) {
            return $this->response($post);
        }
        throw new UserException('修改失败');
    }
}