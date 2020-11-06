<?php

namespace app\models;

use Yii;
use yii\base\UserException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%carousels}}".
 *
 * @property int $id
 * @property string $uuid
 * @property string $type 类型，image图片，video视频，ad广告，html网页
 * @property string $title 标题
 * @property string $url 访问地址
 * @property int|null $width 幅面宽
 * @property int|null $height 幅面高
 * @property string|null $description 描述
 * @property int $status 状态，1启用，2禁用
 */
class Carousel extends \yii\db\ActiveRecord
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%carousels}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['type', 'title', 'url'], 'required', 'on' => ['create', 'update']],
			[['width', 'height'], 'integer'],
			[['type'], 'string', 'max' => 32],
			[['title', 'description'], 'string', 'max' => 255],
			['url', 'url']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'type' => 'Type',
			'title' => 'Title',
			'Url' => 'Url',
			'width' => 'Width',
			'height' => 'Height',
			'description' => 'Description',
		];
	}

	public static function list($page = null, $limit = 10, $select = [], $like = '', $enable = null)
	{
		$query = Carousel::find();
		if ($select) {
			$query->select($select);
		}
		if ($like) {
			$query->where(['like', 'title', $like]);
		}
		if (!is_numeric($enable)) {
			$query->andFilterWhere(['status' => $enable]);
		}
		if ($page) {
			$pagination = [
				'page' => $page,
				'pageSize' => $limit,
			];
		} else {
			$pagination = null;
		}
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => $pagination,
		]);
		$pagination = $dataProvider->getPagination();
		return [
			'pageSize' => $pagination->getPageSize(),
			'pageCount' => $pagination->getPageCount(),
			'pageOffset' => $pagination->getPage(),
			'totalCount' => $pagination->totalCount,
			'recordOffset' => $pagination->getOffset(),
			'auditTasks' => $dataProvider->getModels(),
		];
	}

	/**
	 * @param $data
	 * @return Carousel
	 * @throws UserException
	 */
	public static function create($data)
	{
		$carousel = new Carousel();
		$carousel->setScenario('create');
		$carousel->load($data, '');
		if ($carousel->save()) {
			return $carousel;
		}
		$errors = $carousel->getFirstErrors();
		throw new UserException(reset($errors));
	}

	/**
	 * @param $data
	 * @return Carousel
	 * @throws UserException
	 */
	public static function modify($data)
	{
		$carousel = Carousel::find()->where(['id' => ArrayHelper::getValue($data, 'id')])->limit(1)->one();
		if ($carousel) {
			$carousel->setScenario('update');
			$carousel->load($data);
			if ($carousel->save()) {
				return $carousel;
			}
			$errors = $carousel->getFirstErrors();
			throw new UserException(reset($errors));
		}
		throw new UserException('Carousel is not exist');
	}

	/**
	 * @param $id
	 * @return false|int
	 * @throws UserException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function remove($id)
	{
		$carousel = Carousel::find()->where(['id' => $id])->limit(1)->one();
		if ($carousel) {
			return $carousel->delete();
		}
		throw new UserException('Carousel is not exist');
	}
}
