<?php

namespace App\CoreModule\ManagerModule\Views;

use Ui\Views\ViewFilter;

class ModuleFilter extends ViewFilter
{
    public function __construct()
    {
        //Read right
        $this->viewables = ['id', 'moduleClass', 'name', 'description'];
        //Write right
        $this->writables = ['moduleClass', 'name', 'description'];
        $this->confirmables = [];
        $this->searchables = ['class', 'name', 'description'];
    }
}

