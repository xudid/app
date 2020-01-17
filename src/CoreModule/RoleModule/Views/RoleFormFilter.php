<?php
namespace App\CoreModule\RoleModule\Views;

use Ui\Views\ViewFilter;

class RoleFormFilter extends ViewFilter{


    public function __construct()
    {
      $this->viewables = ["name","description"];
      $this->viewablesfor = [
                              "/roles"=>["name","description"],
                              "/roles/:id"=>["name","description"],
                              "/users/:id"=>["name"]
                            ];

      $this->writables = [];
      $this->writablesfor = [];
      $this->confirmables = ["password"];
      $this->searchables =["name","description"];
    }
  }
