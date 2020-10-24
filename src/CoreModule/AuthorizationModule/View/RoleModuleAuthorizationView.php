<?php

namespace App\CoreModule\AuthorizationModule\View;

use App\CoreModule\ManagerModule\Model\Module;
use Ui\HTML\Elements\Empties\Br;
use Ui\HTML\Elements\Empties\Hr;
use Ui\HTML\Elements\Empties\Input;
use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\CheckBox;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Button\Toggle;
use Ui\Widgets\Cards\InfoCard;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Views\Row;

class RoleModuleAuthorizationView extends Form
{

    /**
     * RoleModuleAuthorizationView constructor.
     * @param Module $modules
     */
    public function __construct(int $id,array $authorized, Module...$modules)
    {
        parent::__construct();
        $roleIdInput = (new HiddenInput('role_id'))
            ->setValue($id);
        $this->add($roleIdInput);
        $row = (new Row())
            ->setClass('row justify-content-center');
        $this->add($row);
        foreach ($modules as $module) {
            $card = (new InfoCard($module->getName()))
                ->setClass('col-3');
            $row->add($card);
            $moduleId = $module->getId();
            $input = (new HiddenInput("modules[$moduleId]"))
                ->setValue('off');
            $toggler = new Toggle("modules[$moduleId]");
            if (in_array($moduleId, $authorized)) {
                $toggler->on();
            }

            $card->body()->feed(
                $module->getDescription(),
                (new Hr())->setClass('bg-primary'),
                $input,
                $toggler,
            );
        }

        $button = (new SubmitButton('Autoriser'))
            ->setClass('btn btn-primary float-right');
        $buttonRow = (new Row())
            ->setClass('mt-3 justify-content-center')
            ->add($button);
        $this->setAction('/authorizations/modules')
            ->setMethod('POST')
            ->add($buttonRow);
    }
}