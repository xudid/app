<?php

namespace App\CoreModule\AuthModule;

use App\Module\Module;

/**
 * Class AuthModule
 * @package App\CoreModule\AuthModule
 */
class AuthModule extends Module
{
	/*$firewall->withRule("IPV6", "::1", "/login", ["ACCEPT" => null])
			->withRule("IPV6", "::1", "/auth", ["ACCEPT" => null])
			->withRule("IPV6", "::1", "/logout", ["ACCEPT" => null])
			->withRule("IPV6", "::1", "/demo", ["ACCEPT" => null])
			->withRule("IPV6", "::1", "/demo", ["LOG" => null]);
	}*/
	protected static string $name = 'Authentication module';
	protected static string $description = <<<TEXT
User login, logout
TEXT;
	public static function getDir()
	{
		return __DIR__;
	}
}
