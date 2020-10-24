<?php

namespace App\CoreModule\ManagerModule\Views;

use Ui\Views\ViewFilter;

class ActionFilter extends ViewFilter
{
    public function __construct()
    {
        //Read right
        $this->viewables = ['id', 'name', 'type', 'routeName'];
        //Write right
        $this->writables = ['name', 'type', 'routeName'];
        $this->confirmables = [];
        $this->searchables = ['id', 'name', 'type', 'routeName'];
    }
}

