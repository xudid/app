<?php

namespace App\CoreModule\UserModule;

use App\Module\Module;

/**
 *Control an entity display and modifications
 */
class UserModule extends Module
{
	protected static string $name = 'User';
	protected static string $description = <<<TEXT
Participate to User authentication
TEXT;
	public static function getDir()
	{
		return __DIR__;
	}
}
