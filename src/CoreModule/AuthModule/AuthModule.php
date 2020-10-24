<?php

namespace App\CoreModule\AuthModule;


use App\App;
use App\Module\Module;
use Psr\Container\ContainerInterface;

/**
 * Class AuthModule
 * @package App\CoreModule\AuthModule
 */
class AuthModule extends Module
{
    public function __construct()
    {
        /*$firewall->withRule("IPV6", "::1", "/login", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/auth", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/logout", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/demo", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/demo", ["LOG" => null]);
    }*/
    }
    public static function getDir()
    {
        return __DIR__;
    }

}
