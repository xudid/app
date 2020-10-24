<?php

namespace App\CoreModule\UserModule\Views;


use Ui\Views\ViewFilter;

class UserFilter extends ViewFilter
{

    function __construct()
    {
        $this->viewables = ['id', 'name', 'email', 'password', 'roles'];
        $this->viewablesfor = ["/users/:id" => ["name", "email", "password", "roles"]];
        $this->writables = ['name', 'email', 'password', 'roles'];
        $this->writablesfor = [];
        $this->confirmables = ["password"];
        $this->searchables = ["name", "email", "id"];
    }

}
