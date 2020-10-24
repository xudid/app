<?php

namespace App\CoreModule\ManagerModule\Views;


use Ui\Views\ViewFieldsDefinition;

class ModuleFieldDefinition extends ViewFieldsDefinition
{
    function __construct()
    {
        $this->fieldsDefinition = ['moduleClass' => 'input', 'name' => 'input', 'description' => 'input', "id" => "input"];
        $this->associationSelectField = 'name';
        $this->associationSelectKey = 'id';
        $this->dataForListInput = [];
        $this->displays = [
            'moduleClass' => 'Classe',
            'name' => 'Nom',
            'descrition' => 'Description',
            'id' => 'Identifiant'
        ];
    }
}
