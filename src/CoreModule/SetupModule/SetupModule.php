<?php


namespace App\CoreModule\SetupModule;


use App\Module\Module;

class SetupModule extends Module
{
    // Provide module choice and
    // some modules can offer cli commands by example RouterModule to generate routesCache list routes ...
    public static function getDir()
    {
        return __DIR__;
    }
}