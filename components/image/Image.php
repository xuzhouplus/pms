<?php


namespace app\components\image;

class Image
{
	public string $dir;
	public int $width;
	public int $height;
	public string $extension;
	public string $name;
	public $file;
	public int $quality = 100;

	/**
	 * Image constructor.
	 * @param null $file
	 * @throws \Exception
	 */
	public function __construct($file = null)
	{
		if ($file) {
			$this->analyse($file);
		}
	}

	public function __destruct()
	{
		$this->file && imagedestroy($this->file);
	}

	/**
	 * @param $file
	 * @throws \Exception
	 */
	public function setFile($file)
	{
		$this->analyse($file);
	}

	/**
	 * @param $file
	 * @throws \Exception
	 */
	private function analyse($file)
	{
		if (!file_exists($file)) {
			throw new \Exception('file is not exist:' . $file);
		}
		if (!is_readable($file)) {
			throw new \Exception('file is not readable:' . $file);
		}
		$sourceFilePathInfo = pathinfo($file);
		$this->dir = $sourceFilePathInfo['dirname'];
		$this->name = $sourceFilePathInfo['basename'];
		$this->extension = $sourceFilePathInfo['extension'];
		switch (strtolower($this->extension)) {
			case 'jpg':
				$this->file = imagecreatefromjpeg($file);
				break;
			case 'webp':
				$this->file = imagecreatefromwebp($file);
				break;
			case 'png':
				$this->file = imagecreatefrompng($file);
				break;
			default:
				throw new \Exception('文件类型不支持：' . $this->extension);
		}
		$this->width = imagesx($this->file);
		$this->height = imagesy($this->file);
	}

	public function write()
	{
		$fileName = str_replace('.' . $this->extension, '', $this->name);
		$fileName = $fileName . '_' . $this->width . '_' . $this->height . '_' . $this->quality . '.' . $this->extension;
		switch ($this->extension) {
			case 'jpg':
				imagejpeg($this->file, $this->dir . DIRECTORY_SEPARATOR . $fileName, $this->quality);
				break;
			case 'png':
				imagepng($this->file, $this->dir . DIRECTORY_SEPARATOR . $fileName, $this->quality);
				break;
			case 'webp':
				imagewebp($this->file, $this->dir . DIRECTORY_SEPARATOR . $fileName, $this->quality);
				break;
			default:
				throw new \Exception('Unsupported file extension:' . $this->extension);
		}
		imagedestroy($this->file);
		$this->name = $fileName;
	}

	public function resize($width, $height)
	{
		$target = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($target, $this->file, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		imagedestroy($this->file);
		$this->file = $target;
	}

	public function cut($offsetX, $offsetY, $width, $height)
	{
		$target = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($target, $this->file, 0, 0, $offsetX, $offsetY, $width, $height, $this->width, $this->height);
		imagedestroy($this->file);
		$this->file = $target;
	}

	public function compress($quality)
	{
		$this->quality = round($quality);
	}

	public function convert($extension)
	{
		$this->extension = $extension;
	}
}