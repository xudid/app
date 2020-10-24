<?php

namespace App\CoreModule\ManagerModule\Views;


use Ui\Views\ViewFieldsDefinition;

class ActionFieldDefinition extends ViewFieldsDefinition
{
    function __construct()
    {
        $this->fieldsDefinition = ['name' => 'input', 'type' => 'input', '$routeName' => 'input'];
        $this->dataForListInput = [];
        $this->associationSelectField = 'name';
        $this->associationSelectKey = 'id';
        $this->displays = [
            'type' => 'Type',
            'name' => 'Nom',
            'routeName' => 'Nom de la route',
            'id' => 'Identifiant'
        ];
    }
}
