<?php


namespace App\CoreModule\ManagerModule;


use App\App;
use App\CoreModule\ManagerModule\Controller\ModuleManagerController;
use App\Module\Module;

class ManagerModule extends Module
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public static function getDir() : string
    {
        return __DIR__;
    }
}
