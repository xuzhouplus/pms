<?php

namespace app\models;

use app\helpers\ImageHelper;
use Faker\Provider\Uuid;
use Yii;
use yii\base\UserException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Request;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%files}}".
 *
 * 文件管理
 * @property integer $id
 * @property string $type 文件类型
 * @property string $name 文件名
 * @property string $thumb 缩略图
 * @property string $preview 预览图
 * @property string $path 文件路径
 * @property integer $width 幅面宽
 * @property integer $height 幅面高
 * @property string $description 说明
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
            [['name', 'thumb', 'path', 'description'], 'string', 'max' => 255],
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
            'thumb' => '缩略图',
            'path' => '文件路径',
            'width' => '幅面宽',
            'height' => '幅面高',
            'description' => '说明',
        ];
    }

    /**
     * @param null $page
     * @param int $limit
     * @param array $select
     * @param null $type
     * @param string $name
     * @return array
     */
    public static function list($page = null, $limit = 10, $select = [], $type = null, $name = '')
    {
        $query = File::find();
        if ($select) {
            $query->select($select);
        }
        $query->andFilterWhere(['type' => $type]);
        $query->andFilterWhere(['like', 'name', $name]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
                'page' => $page
            ],
        ]);
        /**
         * @var File[] $files
         */
        $files = $dataProvider->getModels();
        if (!empty($files)) {
            foreach ($files as $index => $file) {
                $files[$index]['thumb'] = File::getUrl($file->thumb);
                $files[$index]['path'] = File::getUrl($file->path);
                $files[$index]['preview'] = File::getUrl($file->preview);
            }
        }
        $pagination = $dataProvider->getPagination();
        return [
            'size' => $pagination->getPageSize(),
            'count' => $pagination->getPageCount(),
            'page' => $pagination->getPage(),
            'total' => $pagination->totalCount,
            'offset' => $pagination->getOffset(),
            'files' => $files
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
            $uploadRelativePath = DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . str_replace('-', '', Uuid::uuid());
            $uploadedFile = Yii::$app->upload->save(null, 'file', $uploadRelativePath, true);
            $this->path = $uploadRelativePath . '.' . $uploadedFile->getExtension();
            $this->preview = $this->makePreview();
            $this->thumb = $this->makeThumb();
            $this->save();
            return $this;
        } else {
            $errors = $this->getFirstErrors();
            throw new UserException(reset($errors));
        }
    }

    public function removeFile()
    {
        $uploadedFilePath = str_replace('/', DIRECTORY_SEPARATOR, Yii::$app->upload->path . $this->path);
        if (file_exists($uploadedFilePath)) {
            @unlink($uploadedFilePath);
            return true;
        }
        return false;
    }

    public function makeThumb()
    {
        $thumb = Yii::$app->image->thumb(File::getPath($this->path));
        $thumbPath = $thumb->dir . DIRECTORY_SEPARATOR . $thumb->name;
        return str_replace(Yii::$app->upload->path, "", $thumbPath);
    }

    public function makePreview()
    {
        $preview = Yii::$app->image->compress(File::getPath($this->path), 62);
        $previewPath = $preview->dir . DIRECTORY_SEPARATOR . $preview->name;
        return str_replace(Yii::$app->upload->path, "", $previewPath);
    }

    public function removeThumb()
    {
        $thumbPath = File::getPath($this->thumb);
        if (file_exists($thumbPath)) {
            @unlink($thumbPath);
        }
    }

    public function removePreview()
    {
        $thumbPath = File::getPath($this->preview);
        if (file_exists($thumbPath)) {
            @unlink($thumbPath);
        }
    }

    public static function getPath($filePath)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, Yii::$app->upload->path . $filePath);
    }

    public static function getUrl($filePath)
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', Yii::$app->upload->host . $filePath);
    }
}
