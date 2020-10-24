<?php
namespace App\CoreModule\RoleModule\Views;

use Ui\Views\ViewFieldsDefinition;

class RoleFieldDefinition extends ViewFieldsDefinition
{
  public function __construct()
  {
    $this->fieldsDefinition = ["name"=>"input","description"=>"input"];
    $this->dataForListInput = [];
    $this->associationSelectField = 'name';
    $this->associationSelectKey = 'id';
    $this->displays = [
                       "id"=>"Rôle id",
                       "Role"=>"Role",
                        'roles' => 'Roles',
                       "name"=>"nom",
                       "description"=>"description"
                      ];
  }
}
