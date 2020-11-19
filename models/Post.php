<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%posts}}".
 *
 * 稿件
 * @property integer $id
 * @property string $uuid uuid
 * @property string $type 类型，html普通，md Markdown
 * @property string $title 标题
 * @property string $sub_title 二级标题
 * @property string $cover 封面
 * @property string $content 内容
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%posts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'sub_title'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 32],
            [['title', 'sub_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型，html普通，md Markdown',
            'title' => '标题',
            'sub_title' => '二级标题',
            'content' => '内容',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

   public static function list($page = 0, $limit = 10, $select = [], $like = '', $enable = null)
   {
	   $query = Post::find();
	   if ($select) {
		   $query->select($select);
	   }
	   if ($like) {
		   $query->where(['like', 'title', $like]);
	   }
	   if (!is_numeric($enable)) {
		   $query->andFilterWhere(['status' => $enable]);
	   }
	   $pagination = [
		   'page' => $page,
		   'pageSize' => $limit,
	   ];
	   $dataProvider = new ActiveDataProvider([
		   'query' => $query,
		   'pagination' => $pagination,
	   ]);
	   $pagination = $dataProvider->getPagination();
	   return [
		   'size' => $pagination->getPageSize(),
		   'count' => $pagination->getPageCount(),
		   'page' => $pagination->getPage(),
		   'total' => $pagination->totalCount,
		   'offset' => $pagination->getOffset(),
		   'carousels' => $dataProvider->getModels(),
	   ];
   }
}
