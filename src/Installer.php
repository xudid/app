<?php


namespace App;


use Exception;

class Installer
{
	public static function install()
	{
		print_r("create application skeleton from script \n") ;
		if (!file_exists('app')) {
			print_r("create public directory \n");
			mkdir('app');
		}
		if (!file_exists('public')) {
			print_r("create public directory \n");
			mkdir('public');
			copy('vendor/xudid/app/public/bootstrap.php', 'public/bootstrap.php');

			mkdir('public/js');
			print_r("create public/js directory \n");
			self::copyDirectory('vendor/xudid/app/public/js', 'public/js', 'js');

			mkdir('public/css');
			print_r("create public/css directory \n");
			self::copyDirectory('vendor/xudid/app/public/css', 'public/css', 'css');

			mkdir('public/images');
			print_r("create public/images directory \n");

		}

		if (!file_exists('config')) {
			print_r("create config directory \n");
			mkdir('config');
			self::copyDirectory('vendor/xudid/app/config', 'config', 'php');
		}
		if(!file_exists('cache')) {
			mkdir('tmp');
		}

		if(!file_exists('cache/classes')) {
			mkdir('cache/classes',0777, true);
		}

		if(!file_exists('cache/routes')) {
			mkdir('cache/routes');
		}

		if(file_exists('composer.json')) {
			$configContent = file_get_contents('composer.json');
			$config = json_decode($configContent, true);

			$config["autoload"] = ['psr-4' => ["App\\" => "app/"]];
			$configContent = json_encode($config, JSON_PRETTY_PRINT);
			file_put_contents('composer.json', $configContent);
			self::handleJsonError(json_last_error());
		}


	}

	private static function copyDirectory(string $src, string $dest, $extension)
	{
		$filesName = glob($src . DIRECTORY_SEPARATOR .'*.' . $extension);
		foreach ($filesName as $fileName) {
			if (is_dir($fileName)) {
				mkdir($fileName);
				$destFileName = $dest . DIRECTORY_SEPARATOR. $fileName;
				self::copyDirectory($fileName, $destFileName, $extension);
			}
			$baseFileName = basename($fileName);
			$destFileName = $dest . DIRECTORY_SEPARATOR. $baseFileName;
			copy($fileName, $destFileName);
		}
	}

	private static function handleJsonError($error)
	{
		switch ($error) {
			case JSON_ERROR_NONE: break; // handle nothing
			case JSON_ERROR_DEPTH: throw new Exception("Maximum stack depth exceeded");
			case JSON_ERROR_STATE_MISMATCH: throw new Exception("Underflow or the modes mismatch");
			case JSON_ERROR_CTRL_CHAR: throw new Exception("Unexpected control character found");
			case JSON_ERROR_SYNTAX: throw new Exception("Syntax error, malformed JSON");
			case JSON_ERROR_UTF8: throw new Exception("Malformed UTF-8 characters, possibly incorrectly encoded");
			default: throw new Exception("Unknown error");
		}
	}


}