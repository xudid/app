<?php
namespace App\CoreModule\RoleModule\Views;

use Ui\Views\ViewFieldsDefinition;

class RoleFormFieldDefinition extends ViewFieldsDefinition
{
  public function __construct()
  {
    $this->fieldsDefinition = ["name"=>"input","description"=>"input"];
    $this->dataForListInput = [];
    $this->displays = [
                       "id"=>"Rôle id",
                       "Role"=>"Role",
                       "name"=>"nom",
                       "description"=>"description"
                      ];
  }
}
