<?php

/**
 * Converter to .webp extension
 *
 * Link: https://github.com/nkukehenry/Image-to-WebP-Converter/tree/master
 *
 * @var string $fullpath
 * @var int $outPutQuality
 * @var string $extension
 * @var string $newFilefullPath
 * @return mixed
 */
class WebpConverter
{
	private static $fullPath;
	private static $outPutQuality;
	private static $deleteOriginal;
	private static $extension;
	private static $newFilefullPath;

	public static function convert(
		$fullPath,
		$outPutQuality = 100,
		$deleteOriginal = false
	) {

		self::$fullPath = $fullPath;
		self::$outPutQuality  = $outPutQuality;
		self::$deleteOriginal = $deleteOriginal;

		if (file_exists(self::$fullPath)) :

			$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
			self::$extension = $ext;
			self::$newFilefullPath = str_replace('.' . $ext, '.webp', $fullPath);


			$isValidFormat = false;

			if (self::$extension == 'png' || self::$extension == 'PNG') {
				$img = imagecreatefrompng(self::$fullPath);
				$isValidFormat = true;
			} elseif (self::$extension == 'jpg' || self::$extension == 'JPG' || self::$extension == 'JPEG' || self::$extension == 'jpeg') {
				$img = imagecreatefromjpeg(self::$fullPath);
				$isValidFormat = true;
			} elseif (self::$extension == 'gif' || self::$extension == 'GIF') {
				$img = imagecreatefromgif(self::$fullPath);
				$isValidFormat = true;
			}

			if ($isValidFormat && $img !== false) {
				imagepalettetotruecolor($img);
				imagealphablending($img, true);
				imagesavealpha($img, true);
				imagewebp($img, self::$newFilefullPath, self::$outPutQuality);
				imagedestroy($img);

				//delete original file if desired
				if (self::$deleteOriginal) {
					unlink(self::$fullPath);
				}
			} else {
				//if wrong file format
				return (object) array('error' => 'Given file cannot be converted to webp', 'status' => 0);
			}

			$newPathInfo = explode('/', self::$newFilefullPath);
			$finalImage  = $newPathInfo[count($newPathInfo) - 1];

			$result = array(
				"fullPath" => $img !== false ? self::$newFilefullPath : self::$fullPath,
				"file" => $finalImage,
				"status" => 1
			);

			return (object) $result;

		else :
			return (object) array('error' => 'File does not exist', 'status' => 0);
		endif;
	}
}
