<?php


namespace app\components\image;


use Yii;

class Carousel extends Image
{
    private Image $source;
    private Image $blur;

    public function __construct($file = null)
    {
        if ($file) {
            $this->load($file);
        } else {
            $this->source = new Image();
            $this->blur = new Image();
        }
    }

    public function render($width = null, $height = null, $extension = null, $factory = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->extension = $extension;
        if ($this->source->width == $this->width && $this->source->height == $this->height) {
            $this->file = $this->source->file;
        } else {
            $targetFile = imagecreatetruecolor($this->width, $this->height);
            if ($this->source->width < $this->width || $this->source->height < $this->height) {
                $this->blur($factory);
                imagecopyresampled($targetFile, $this->blur->file, 0, 0, 0, 0, $this->width, $this->height, $this->source->width, $this->source->height);
            }
            $targetXOffset = max($this->width - $this->source->width, 0);
            $targetYOffset = max($this->height - $this->source->height, 0);
            imagecopyresampled($targetFile, $this->source->file, $targetXOffset / 2, $targetYOffset / 2, 0, 0, min($this->source->width, $this->width), min($this->source->height, $this->height), $this->source->width, $this->source->height);
            $this->file = $targetFile;
            Yii::error($this->file);
        }
    }

    public function load($sourceFile)
    {
        $this->source = new Image($sourceFile);
        $this->blur = new Image($sourceFile);
        $this->name = 'carousel_' . str_replace($this->source->extension, '', $this->source->name);
        $this->dir = $this->source->dir;
    }

    public function write()
    {
        parent::write();
        unset($this->source);
        unset($this->blur);
    }

    private function blur($factory = 3)
    {
        // blurFactor has to be an integer
        $blurFactor = round($factory);
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