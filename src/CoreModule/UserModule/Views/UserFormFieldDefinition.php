<?php
namespace App\CoreModule\UserModule\Views;


use Ui\Views\ViewFieldsDefinition;

class UserFormFieldDefinition extends ViewFieldsDefinition
{
  function __construct()
  {
    $this->fieldsDefinition = ["name"=>"input","email"=>"email","password"=>"password","role"=>"select"];
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
