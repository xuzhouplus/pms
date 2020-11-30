<?php


namespace app\components\image;


class Thumb extends Image
{
	public function render($width = 320, $height = 180, $extension = 'jpg')
	{
		$this->extension = $extension;
		$widthRatio = $width / $this->width;
		$heightRatio = $height / $this->height;
		if ($widthRatio >= 1 && $heightRatio >= 1) {
			if ($widthRatio > $heightRatio) {
				$offsetTop = floor((floor($height / $widthRatio) - $this->height) / 2);
				$offsetLeft = 0;
			} else {
				$offsetTop = 0;
				$offsetLeft = floor((floor($width / $heightRatio) - $this->width) / 2);
			}
		} elseif ($widthRatio >= 1 && $heightRatio < 1) {
			$offsetTop = floor((floor($height / $widthRatio) - $this->height) / 2);
			$offsetLeft = 0;
		} elseif ($widthRatio < 1 && $heightRatio >= 1) {
			$offsetTop = 0;
			$offsetLeft = floor((floor($width / $heightRatio) - $this->width) / 2);
		} else {
			$offsetLeft = floor(($this->width - $width) / 2);
			$offsetTop = floor(($this->height - $height) / 2);
		}
		$targetImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($targetImage, $this->file, 0, 0, $offsetLeft, $offsetTop, $width, $height, $this->width, $this->height);
		imagedestroy($this->file);
		$this->file = $targetImage;
	}
}