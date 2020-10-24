<?php

namespace App\CoreModule\RoleModule;

use App\App;
use App\Module\Module;

/**
 *Control an entity display and modifications
 */
class RoleModule extends Module
{

    protected string $scope = "roles";


    public function __construct(App $app)
    {


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

    }
    public static function getDir()
    {
        return __DIR__;
    }
}
