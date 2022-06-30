<?php

namespace App\CoreModule\LoggingModule;

use App\App;
use App\Module\Module;

/**
 * Class LoggingModule
 * @package App\CoreModule\LoggingModule
 */
class LoggingModule extends Module
{
	protected static string $name = 'Logging';
	protected static string $description = <<<TEXT
Provides application activities tracability in a file
TEXT;
	public static function getDir(): string
	{
		return __DIR__;
	}
}
