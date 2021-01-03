<?php

namespace app\models;

use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;
use yii\behaviors\AttributeBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

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
 * @property integer $status 是否启用，1启用，2禁用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Post extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%posts}}';
    }

    public function behaviors()
    {
        return [
            'uuid' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'uuid'
                ],
                'value' => function ($event) {
                    return str_replace('-', '', Uuid::uuid());
                }
            ],
            'time' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at'
                ],
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'sub_title', 'cover'], 'required'],
            [['content'], 'string'],
            [['uuid'], 'string', 'max' => 32],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 32],
            [['title', 'sub_title'], 'string', 'max' => 255],
            ['cover', 'url'],
            ['status', 'default', 'value' => self::STATUS_DISABLED],
            ['status', 'in', 'range' => [self::STATUS_ENABLED, self::STATUS_DISABLED]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'UUID',
            'type' => '类型，html普通，md Markdown',
            'title' => '标题',
            'sub_title' => '二级标题',
            'content' => '内容',
            'status' => '状态',
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
            'posts' => $dataProvider->getModels(),
        ];
    }

    /**
     * @param $id
     * @return Post
     */
    public static function findOneById($id)
    {
        return Post::find()->where(['id' => $id])->limit(1)->one();
    }

    /**
     * @param $uuid
     * @return Post
     */
    public static function findOneByUuid($uuid)
    {
        return Post::find()->where(['uuid' => $uuid])->limit(1)->one();
    }

    /**
     * 新建或编辑
     * @param $data
     * @return Post|array|ActiveRecord
     * @throws UserException
     */
    public static function set($data)
    {
        if (!empty($data['id'])) {
            $post = Post::find()->where(['id' => $data['id']])->limit(1)->one();
        } else {
            $post = new Post();
        }
        $post->load($data, '');
        if ($post->save()) {
            return $post;
        }
        $errors = $post->getFirstErrors();
        throw new UserException(reset($errors));
    }
}
