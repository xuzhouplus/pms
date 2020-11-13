<?php

namespace app\models;

use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%carousels}}".
 *
 * @property int $id
 * @property string $uuid
 * @property int $file_id
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
			]
		];
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
			['url', 'url'],
			[['uuid'], 'string', 'max' => 32],
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
			'type' => 'Type',
			'title' => 'Title',
			'url' => 'Url',
			'width' => 'Width',
			'height' => 'Height',
			'description' => 'Description',
		];
	}

	/**
	 * @param null $page
	 * @param int $limit
	 * @param array $select
	 * @param string $like
	 * @param null $enable
	 * @return array
	 */
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
		if (is_null($page)) {
			return $query->all();
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

	/**
	 * @param $data
	 * @return Carousel
	 * @throws UserException
	 */
	public static function create($data)
	{
		$file = File::find()->where(['id' => $data['file_id']])->limit(1)->one();
		$carouselUrl = Carousel::make($file);
		unset($data['file_id']);
		$data['url'] = $carouselUrl;
		$data['type'] = $file->type;
		$data['width'] = $file->width;
		$data['height'] = $file->height;
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
			if ($carousel->file_id != $data['file_id']) {
				Carousel::destroy($carousel->url);
				$file = File::find()->where(['id' => $data['file_id']])->limit(1)->one();
				$carouselUrl = Carousel::make($file);
				unset($data['file_id']);
				$data['url'] = $carouselUrl;
				$data['type'] = $file->type;
				$data['width'] = $file->width;
				$data['height'] = $file->height;
			}
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
			Carousel::destroy($carousel->url);
			return $carousel->delete();
		}
		throw new UserException('Carousel is not exist');
	}

	/**
	 * @param File|string $file
	 * @return mixed
	 * @throws \Exception
	 */
	public static function make($file)
	{
		$carousel = Yii::$app->image->create(($file instanceof File) ? $file->getPath() : $file, 1920, 1080, 'jpg', 3);
		if ($carousel) {
			$image = Yii::$app->image->compress($carousel->target->dir . DIRECTORY_SEPARATOR . $carousel->target->name, 60);
			return str_replace('\\', '/', str_replace(Yii::$app->upload->path, Yii::$app->upload->host, $image->dir . DIRECTORY_SEPARATOR . $image->name));
		}
		throw new \Exception('fail to make carousel of file:' . $file);
	}

	/**
	 * @param $file
	 * @return bool
	 */
	public static function destroy($file)
	{
		$filePath = str_replace('\\', '/', str_replace(Yii::$app->upload->host, Yii::$app->upload->path, $file));
		if (file_exists($filePath)) {
			return unlink($filePath);
		}
		return true;
	}
}
