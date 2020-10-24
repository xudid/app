<?php
namespace App\CoreModule\UserModule\Views;


use Ui\Views\ViewFieldsDefinition;

class UserFieldDefinition extends ViewFieldsDefinition
{
  function __construct()
  {
    $this->fieldsDefinition = ["name"=>"input","email"=>"email","password"=>"password","id"=>"input"];
    $this->dataForListInput = ["role"=>["admin","qis","cdem","soa","cvsp","none"]];
    $this->displays = [
                         "id"=>"Id Utilisateur",
                         "User"=>"Utilisateur",
                         "role"=>"Rôle",
                         "name"=>"nom",
                         "type"=>"type",
                         "password"=>"mot de passe",
                         "email"=>"mail"
                       ];
  }
}
