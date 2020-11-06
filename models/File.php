<?php

namespace app\models;

use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property int $id
 * @property string $type 文件类型
 * @property string $name 文件名
 * @property string|null $path 文件路径
 * @property int|null $width 幅面宽
 * @property int|null $height 幅面高
 * @property string|null $description 说明
 */
class File extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%files}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['width', 'height'], 'integer'],
			[['type'], 'string', 'max' => 32],
			[['name', 'path', 'description'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'type' => '文件类型',
			'name' => '文件名',
			'path' => '文件路径',
			'width' => '幅面宽',
			'height' => '幅面高',
			'description' => '说明',
		];
	}

	/**
	 * @return $this
	 * @throws UserException
	 */
	public function upload()
	{
		if ($this->validate()) {
			$directory = date('Y/m/d');
			$uploadRelativePath = DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . Uuid::uuid();
			$uploadedFile = Yii::$app->upload->save(null, 'file', $uploadRelativePath, true);
			$this->path = $uploadRelativePath . '.' . $uploadedFile->getExtension();
			$this->save();
			return $this;
		} else {
			$errors = $this->getFirstErrors();
			throw new UserException(reset($errors));
		}
	}

	public function remove()
	{
		$uploadedFilePath = str_replace('/', DIRECTORY_SEPARATOR, Yii::$app->upload->path . $this->path);
		if (file_exists($uploadedFilePath)) {
			@unlink($uploadedFilePath);
			return true;
		}
		return false;
	}

	public function getPath()
	{
		return str_replace('\\', DIRECTORY_SEPARATOR, Yii::$app->upload->path . $this->path);
	}

	public function getUrl()
	{
		return str_replace(DIRECTORY_SEPARATOR, '/', Yii::$app->upload->host . $this->path);
	}
}
