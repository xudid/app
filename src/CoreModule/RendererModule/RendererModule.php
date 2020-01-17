<?php


namespace App\CoreModule\RendererModule;


use App\App;
use App\Module\Module;

class RendererModule extends Module
{

    /**
     * RendererModule constructor.
     */
    public function __construct(App $app)
    {
        $this->name = 'renderer';
    }
}