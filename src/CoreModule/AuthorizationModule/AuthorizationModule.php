<?php

namespace App\CoreModule\AuthorizationModule;

use App\Module\Module;

class AuthorizationModule extends Module
{
	protected static string $name = 'Authorization';
	protected static string $description = <<<TEXT
Role authorization managment
TEXT;
	public static function getDir()
	{
		return __DIR__;
	}
}
