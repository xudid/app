<?php

namespace App\CoreModule\RoleModule;

use App\Module\Module;

/*$firewall->withRule("IPV6", "::1", "/roles", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IPV6", "::1", "/roles/new", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IPV6", "::1", "/roles/search", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IPV6", "::1", "/roles/:id", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IP", "127.0.0.1", "/roles", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IP", "127.0.0.1", "/roles/new", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IP", "127.0.0.1", "/roles/search", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IP", "127.0.0.1", "/roles/:id", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("NETWORKV4", "192.168.0.0/24", "/roles/:id", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("NETWORKV4", "192.168.0.0/24", "/roles/:id", ["LOG" => "null"])
            ->withRule("IP", "127.0.0.1", "/roles/:id/edit", ["AUTH" => ["role" => ["admin"]]])
            ->withRule("IPV6", "::1", "/roles/:id/edit", ["AUTH" => ["role" => ["admin"]]]);*/
/**
 *Control an entity display and modifications
 */
class RoleModule extends Module
{
	protected static string $name = 'Role';
	protected static string $description = <<<TEXT
Participate to role based authorization
TEXT;
	public static function getDir()
	{
		return __DIR__;
	}
}
