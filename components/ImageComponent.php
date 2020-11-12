<?php


namespace app\components;


use app\components\carousel\Carousel;
use app\components\carousel\Image;

class ImageComponent extends \yii\base\Component
{
	/**
	 * @param $file
	 * @param int $width
	 * @param int $height
	 * @param string $extension
	 * @param int $blurFactor
	 * @return Carousel
	 */
	public function create($file, $width = 1920, $height = 1080, $extension = 'jpg', $blurFactor = 4)
	{
		$carousel = new Carousel();
		$carousel->setWidth($width);
		$carousel->setHeight($height);
		$carousel->setExtension($extension);
		$carousel->setBlurFactor($blurFactor);
		$carousel->load($file);
		$carousel->render();
		$carousel->write();
		return $carousel;
	}

	/**
	 * @param $file
	 * @param $width
	 * @param $height
	 * @return Image
	 * @throws \Exception
	 */
	public function resize($file, $width, $height)
	{
		$image = new Image($file);
		$image->resize($width, $height);
		$image->write();
		return $image;
	}

	/**
	 * @param $file
	 * @param $quality
	 * @return Image
	 * @throws \Exception
	 */
	public function compress($file, $quality)
	{
		$image = new Image($file);
		$image->compress($quality);
		$image->write();
		return $image;
	}

	/**
	 * @param $file
	 * @param $format
	 * @return Image
	 * @throws \Exception
	 */
	public function convert($file, $format)
	{
		$image = new Image($file);
		$image->convert($format);
		$image->write();
		return $image;
	}
}