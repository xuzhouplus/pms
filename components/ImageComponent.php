<?php


namespace app\components;


use app\components\image\Carousel;
use app\components\image\Image;
use app\components\image\Thumb;

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
	public function carousel($file, $width = 1920, $height = 1080, $extension = 'jpg', $blurFactor = 4)
	{
		$carousel = new Carousel($file);
		$carousel->render($width,$height,$extension,$blurFactor);
		$carousel->write();
		return $carousel;
	}

	/**
	 * @param $file
	 * @param int $width
	 * @param int $height
	 * @param string $extension
	 * @return Thumb
	 * @throws \Exception
	 */
	public function thumb($file, $width = 320, $height = 180, $extension = 'png')
	{
		$thumb = new Thumb($file);
		$thumb->render($width, $height, $extension);
		$thumb->write();
		return $thumb;
	}

	/**
	 * @param $file
	 * @param int $offsetTop
	 * @param int $offsetLeft
	 * @param int $width
	 * @param int $height
	 * @param string $extension
	 * @return Image
	 * @throws \Exception
	 */
	public function cut($file, $offsetTop = 0, $offsetLeft = 0, $width = 320, $height = 180, $extension = 'jpg')
	{
		$image = new Image($file);
		$image->cut($offsetTop, $offsetLeft, $width, $height, $extension);
		$image->write();
		return $image;
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