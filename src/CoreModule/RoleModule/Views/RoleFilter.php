<?php
namespace App\CoreModule\RoleModule\Views;

use Ui\Views\ViewFilter;

class RoleFilter extends ViewFilter{


    public function __construct()
    {
      $this->viewables = ['id', 'name', 'description', 'modules'];
      $this->viewablesfor = [
                              '/roles' => ['name','description'],
                              '/roles/:id' => ['name', 'description'],
                              '/users/:id' => ['name']
                            ];

      $this->writables = ['name', 'description'];
      $this->writablesfor = [];
      $this->confirmables = ['password'];
      $this->searchables =['name', 'description'];
    }
  }
