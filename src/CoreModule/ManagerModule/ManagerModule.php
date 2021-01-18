<?php

namespace App\CoreModule\ManagerModule;

use App\Module\Module;

class ManagerModule extends Module
{
	protected static string $name = 'Module Manager';
	protected static string $description = <<<TEXT
Module managment
TEXT;

	public static function getDir() : string
	{
		return __DIR__;
	}
}
