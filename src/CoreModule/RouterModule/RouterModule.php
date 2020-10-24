<?php


namespace App\CoreModule\RouterModule;


use App\App;
use App\CoreModule\RouterModule\Controller\RouterController;
use App\Module\Module;

class RouterModule extends Module
{

    /**
     * RouterModule constructor.
     * @param App $app
     * @param $controller
     */
    public function __construct(App $app)
    {

    }

    public static function getDir(): string
    {
        return __DIR__;
    }
}
