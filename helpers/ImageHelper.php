<?php

namespace app\helpers;
class ImageHelper
{
	public static function blur($gdImageResource, $blurFactor = 3)
	{
		// blurFactor has to be an integer
		$blurFactor = round($blurFactor);
		$originalWidth = imagesx($gdImageResource);
		$originalHeight = imagesy($gdImageResource);
		$smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
		$smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));
		// for the first run, the previous image is the original input
		$prevImage = $gdImageResource;
		$prevWidth = $originalWidth;
		$prevHeight = $originalHeight;
		// scale way down and gradually scale back up, blurring all the way
		for ($i = 0; $i < $blurFactor; $i += 1) {
			// determine dimensions of next image
			$nextWidth = $smallestWidth * pow(2, $i);
			$nextHeight = $smallestHeight * pow(2, $i);
			// resize previous image to next size
			$nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
			imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
				$nextWidth, $nextHeight, $prevWidth, $prevHeight);
			// apply blur filter
			imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);
			// now the new image becomes the previous image for the next step
			$prevImage = $nextImage;
			$prevWidth = $nextWidth;
			$prevHeight = $nextHeight;
		}
		// scale back to original size and blur one more time
		imagecopyresized($gdImageResource, $nextImage,
			0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
		imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);
		// clean up
		imagedestroy($prevImage);
		// return result
		return $gdImageResource;
	}

	public static function carouselImage($carouseFile)
	{
		$sourceFilePathInfo=pathinfo($carouseFile);
		str_replace($sourceFilePathInfo['extension'],$sourceFilePathInfo['basename']);
		$targetFilePath=$sourceFilePathInfo['dirname'];
		$targetFileWidth=1920;
		$targetFileHeight=1080;
		$sourceFile = imagecreatefromjpeg($carouseFile);
		$gaussFile = self::blur($sourceFile);
		$gaussFileWidth = imagesx($gaussFile);
		$gaussFileHeight = imagesy($gaussFile);
		$targetFile = imagecreatetruecolor($targetFileWidth, $targetFileHeight);
		imagecopyresized($targetFile, $gaussFile, 0, 0, 0, 0, $targetFileWidth, $targetFileHeight, $gaussFileWidth, $gaussFileHeight);
		$targetXOffset = max($targetFileWidth - $gaussFileWidth, 0);
		$targetYOffset = max($targetFileHeight - $gaussFileHeight, 0);
		imagecopy($targetFile, $sourceFile, $targetXOffset / 2, $targetYOffset / 2, 0, 0, $gaussFileWidth, $gaussFileHeight);
imagejpeg($targetFile,$carouseFile);
	}
}