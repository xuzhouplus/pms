<?php


namespace app\components\carousel;


class Carousel
{
	public Image $source;
	public Image $target;
	public Image $blur;
	public int $blurFactor;

	public function __construct()
	{
		$this->source = new Image();
		$this->target = new Image();
		$this->blur = new Image();
	}

	public function setWidth($width)
	{
		$this->target->width = $width;
	}

	public function setHeight($height)
	{
		$this->target->height = $height;
	}

	public function setExtension($extension)
	{
		$this->target->extension = $extension;
	}

	public function setBlurFactor($blurFactor)
	{
		$this->blurFactor = $blurFactor;
	}

	public function render()
	{
		if ($this->source->width == $this->target->width && $this->source->height == $this->target->height) {
			$this->target->file = $this->source->file;
		} else {
			$targetFile = imagecreatetruecolor($this->target->width, $this->target->height);
			if ($this->source->width < $this->target->width || $this->source->height < $this->target->height) {
				$this->blur();
				imagecopyresampled($targetFile, $this->blur->file, 0, 0, 0, 0, $this->target->width, $this->target->height, $this->source->width, $this->source->height);
			}
			$targetXOffset = max($this->target->width - $this->source->width, 0);
			$targetYOffset = max($this->target->height - $this->source->height, 0);
			imagecopyresampled($targetFile, $this->source->file, $targetXOffset / 2, $targetYOffset / 2, 0, 0, $this->source->width, $this->source->height, $this->source->width, $this->source->height);
			$this->target->file = $targetFile;
		}
	}

	public function load($sourceFile)
	{
		$this->source = new Image($sourceFile);
		$this->blur = new Image($sourceFile);
		$this->target->name = 'carousel_' . str_replace($this->source->extension, $this->target->extension, $this->source->name);
		$this->target->dir = $this->source->dir;
	}

	public function write()
	{
		$this->target->write();
		imagedestroy($this->source->file);
		imagedestroy($this->blur->file);
	}

	private function blur()
	{
		// blurFactor has to be an integer
		$blurFactor = round($this->blurFactor);
		$originalWidth = $this->blur->width;
		$originalHeight = $this->blur->height;
		$smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
		$smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));
		// for the first run, the previous image is the original input
		$prevImage = $this->blur->file;
		$prevWidth = $originalWidth;
		$prevHeight = $originalHeight;
		$nextImage = null;
		$nextWidth = 0;
		$nextHeight = 0;
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
		imagecopyresized($this->blur->file, $nextImage,
			0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
		imagefilter($this->blur->file, IMG_FILTER_GAUSSIAN_BLUR);
		// clean up
		imagedestroy($prevImage);
	}
}