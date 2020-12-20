<?php


namespace app\components\image;


class Thumb extends Image
{
	public function render($width = 320, $height = 180, $extension = null)
	{
		$widthRatio = $width / $this->width;
		$heightRatio = $height / $this->height;
		if ($widthRatio >= 1 && $heightRatio >= 1) {
			if ($widthRatio > $heightRatio) {
				$targetWidth = $this->width * $heightRatio;
				$offsetX = ($width - $targetWidth) / 2;
				$offsetY = 0;
				$targetHeight = $height;
			} else {
				$targetHeight = $this->height * $widthRatio;
				$offsetX = 0;
				$offsetY = ($height - $targetHeight) / 2;
				$targetWidth = $width;
			}
		} elseif ($widthRatio >= 1 && $heightRatio < 1) {
			$targetWidth = $this->width * $heightRatio;
			$offsetX = ($width - $targetWidth) / 2;
			$offsetY = 0;
			$targetHeight = $height;
		} elseif ($widthRatio < 1 && $heightRatio >= 1) {
			$targetHeight = $this->height * $widthRatio;
			$offsetX = 0;
			$offsetY = ($height - $targetHeight) / 2;
			$targetWidth = $width;
		} else {
			if ($widthRatio > $heightRatio) {
				$targetWidth = $this->width * $heightRatio;
				$offsetX = ($width - $targetWidth) / 2;
				$offsetY = 0;
				$targetHeight = $height;
			} else {
				$targetHeight = $this->height * $widthRatio;
				$offsetX = 0;
				$offsetY = ($height - $targetHeight) / 2;
				$targetWidth = $width;
			}
		}
		$targetImage = imagecreatetruecolor($width, $height);
		if ($extension == 'png') {
			$trans_colour = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
			imagefill($targetImage, 0, 0, $trans_colour);
			imagealphablending($targetImage, false);
		} else {
			$white = imagecolorallocate($targetImage, 255, 255, 255);
			imagefill($targetImage, $white);
		}
		imagecopyresampled($targetImage, $this->file, $offsetX, $offsetY, 0, 0, $targetWidth, $targetHeight, $this->width, $this->height);
		imagedestroy($this->file);
		$this->width = $width;
		$this->height = $height;
		if (!is_null($extension)) {
			$this->extension = $extension;
		}
		$this->file = $targetImage;
	}
}