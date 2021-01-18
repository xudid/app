<?php

namespace App\CoreModule\RouterModule;

use App\Module\Module;

class RouterModule extends Module
{
	protected static string $name = 'Router';
	protected static string $description = <<<TEXT
Provide Application router and route managment
TEXT;
	public static function getDir(): string
	{
		return __DIR__;
	}
}
