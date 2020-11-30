<?php

namespace app\models;

use Exception;
use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;
use yii\behaviors\AttributeBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%carousels}}".
 *
 * 首页幻灯片
 * @property integer $id
 * @property string $uuid uuid
 * @property integer $file_id 使用的文件id
 * @property string $type 类型，image图片，video视频，ad广告，html网页
 * @property string $title 标题
 * @property string $url 地址
 * @property integer $width 幅面宽
 * @property integer $height 幅面高
 * @property string $description 描述
 * @property integer $status 状态，1启用，2禁用
 * @property integer $order 顺序
 */
class Carousel extends \yii\db\ActiveRecord
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;

	const TYPE_WEBGL = 'webgl';
	const TYPE_BOOTSTRAP = 'bootstrap';

	const ORIENTATION_MOVE_UP = 'up';
	const ORIENTATION_MOVE_DOWN = 'down';

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
			[['type', 'title', 'url', 'file_id'], 'required', 'on' => ['create', 'update']],
			[['width', 'height'], 'integer'],
			[['type'], 'string', 'max' => 32],
			[['title', 'description'], 'string', 'max' => 255],
			['url', 'url'],
			[['uuid'], 'string', 'max' => 32],
			['order', 'default', 'value' => function () {
				$maxOrder = Carousel::find()->max('[[order]]');
				return $maxOrder ? ($maxOrder + 1) : 0;
			}],
			['order', 'integer']
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
			'file_id' => 'File ID',
			'type' => 'Type',
			'title' => 'Title',
			'url' => 'Url',
			'width' => 'Width',
			'height' => 'Height',
			'description' => 'Description',
			'status' => 'Status',
			'order' => 'Order'
		];
	}

	/**
	 * @param $order
	 * @throws \yii\db\Exception
	 */
	public function setOrder($order)
	{
		if ($this->order == $order) {
			return;
		}
		$transaction = Carousel::getDb()->beginTransaction();
		try {
			if (is_null($order)) {
				foreach (Carousel::find()->where(['>', 'order', $this->order])->each() as $carousel) {
					$carousel->order = $carousel->order - 1;
					$carousel->save();
				}
				$transaction->commit();
				return;
			}
			if ($order < $this->order) {
				foreach (Carousel::find()->where(['>', 'order', $order - 1])->andWhere(['<', 'order', $this->order])->each() as $carousel) {
					$carousel->order = $carousel->order + 1;
					$carousel->save();
				}
			} else {
				foreach (Carousel::find()->where(['>', 'order', $this->order])->andWhere(['<', 'order', $order])->each() as $carousel) {
					$carousel->order = $carousel->order - 1;
					$carousel->save();
				}
			}
			$this->order = $order;
			$this->save();
			$transaction->commit();
		} catch (Exception $exception) {
			$transaction->rollBack();
			throw $exception;
		}
	}

	public function isEnabled()
	{
		return $this->status == Carousel::STATUS_ENABLED;
	}

	public function enable()
	{
		$this->status = Carousel::STATUS_ENABLED;
		$this->save();
	}

	public function disable()
	{
		$this->status = Carousel::STATUS_DISABLED;
		$this->save();
	}

	/**
	 * @param null $page
	 * @param int $limit
	 * @param array $select
	 * @param string $like
	 * @param null $enable
	 * @param null $order
	 * @return array|ActiveRecord[]
	 */
	public static function list($page = null, $limit = 10, $select = [], $like = '', $enable = null, $order = null)
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
		if ($order) {
			$query->orderBy($order);
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
	 * @throws \yii\db\Exception
	 * @throws Exception
	 */
	public static function create($data)
	{
		if ($data['status'] == Carousel::STATUS_ENABLED) {
			if (Carousel::find()->where(['status' => Carousel::STATUS_ENABLED])->count('id') == Setting::getSetting(Setting::CAROUSEL_LIMIT)) {
				throw new UserException('The carousels number is reached limit');
			}
		}
		/**
		 * @var $file File
		 */
		$file = File::find()->where(['id' => $data['file_id']])->limit(1)->one();
		if (!$file) {
			throw new UserException('File is not exist:' . $data['file_id']);
		}
		$carouselUrl = CarouselService::make($file);
		$data['url'] = $carouselUrl;
		$data['type'] = $file->type;
		$data['width'] = $file->width;
		$data['height'] = $file->height;
		$carousel = new Carousel();
		$carousel->setScenario('create');
		$carousel->load($data, '');
		if ($carousel->save()) {
			$carousel->setOrder($carousel->order);
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
			if ($data['status'] == Carousel::STATUS_ENABLED) {
				if (Carousel::find()->where(['status' => Carousel::STATUS_ENABLED])->count('id') == Setting::getSetting(Setting::CAROUSEL_LIMIT)) {
					throw new UserException('The carousels number is reached limit');
				}
			}
			if ($carousel->file_id != $data['file_id']) {
				CarouselService::destroy($carousel->url);
				$file = File::find()->where(['id' => $data['file_id']])->limit(1)->one();
				if (!$file) {
					throw new UserException('File is not exist:' . $data['file_id']);
				}
				$carouselUrl = CarouselService::make($file);
				$data['url'] = $carouselUrl;
				$data['type'] = $file->type;
				$data['width'] = $file->width;
				$data['height'] = $file->height;
			}
			$carousel->setScenario('update');
			$carousel->load($data);
			if ($carousel->save()) {
				$carousel->setOrder($carousel->order);
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
			CarouselService::destroy($carousel->url);
			if ($carousel->delete()) {
				$carousel->setOrder(null);
				return true;
			}
			return false;
		}
		throw new UserException('Carousel is not exist');
	}

	/**
	 * @param File|string $file
	 * @return mixed
	 * @throws Exception
	 */
	public static function make($file)
	{
		$carousel = Yii::$app->image->carousel(($file instanceof File) ? File::getPath($file->path) : $file, 1920, 1080, 'jpg', 3);
		if ($carousel) {
			$image = Yii::$app->image->compress($carousel->dir . DIRECTORY_SEPARATOR . $carousel->name, 60);
			return str_replace('\\', '/', str_replace(Yii::$app->upload->path, Yii::$app->upload->host, $image->dir . DIRECTORY_SEPARATOR . $image->name));
		}
		throw new Exception('fail to make carousel of file:' . $file);
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

	/**
	 * @param $id
	 * @return Carousel
	 * @throws UserException
	 */
	public static function toggle($id)
	{
		/**
		 * @var Carousel $carousel
		 */
		$carousel = Carousel::find()->where(['id' => $id])->limit(1)->one();
		if ($carousel) {
			$carousel->isEnabled() ? $carousel->disable() : $carousel->enable();
			return $carousel;
		}
		throw new UserException('Carousel is not exist');
	}

	public function adjustOrder()
	{
		$menus = Carousel::find()->orderBy(['order' => SORT_ASC])->all();
		foreach ($menus as $index => $menu) {
			if ($menu->order != $index) {
				$menu->order = $index;
				$menu->save(false);
			}
		}
	}

	/**
	 * @param $orientation
	 */
	public function move($orientation)
	{
		if (is_bool($orientation)) {
			if ($orientation) {
				$orientation = Carousel::ORIENTATION_MOVE_UP;
			} else {
				$orientation = Carousel::ORIENTATION_MOVE_DOWN;
			}
		} else {
			if (!ArrayHelper::isIn($orientation, [Carousel::ORIENTATION_MOVE_UP, Carousel::ORIENTATION_MOVE_DOWN])) {
				$orientation = Carousel::ORIENTATION_MOVE_UP;
			}
		}
		if ($orientation == Carousel::ORIENTATION_MOVE_UP) {
			if ($this->order === 0) {
				return;
			}
			$carousel = Carousel::find()->where(['order' => $this->order - 1])->limit(1)->one();
			$carousel->order = $this->order;
			$this->order = $carousel->order;
			$this->save();
			$carousel->save();
		} else {
			if ($this->order === 99) {
				return;
			}
			$carousel = Carousel::find()->where(['order' => $this->order + 1])->limit(1)->one();
			$carousel->order = $this->order;
			$this->order = $carousel->order;
			$this->save();
			$carousel->save();
		}
	}
}
