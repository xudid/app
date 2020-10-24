<?php


namespace App\CoreModule\AuthorizationModule\View;


use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use Ui\HTML\Elements\Empties\Br;
use Ui\HTML\Elements\Empties\Hr;
use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Button\Toggle;
use Ui\Widgets\Cards\InfoCard;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Views\Row;

class RoleActionAuthorizationView extends Form
{
    public function __construct(int $roleId, array $authorized, Action...$actions)
    {
        parent::__construct();
        $roleIdInput = (new HiddenInput('role_id'))
            ->setValue($roleId);
        $moduleIdInput = (new HiddenInput('module_id'))
            ->setValue($roleId);
        $this->add($roleIdInput);
        $this->add($moduleIdInput);
        $row = (new Row())
            ->setClass('row justify-content-center');
        $this->add($row);
        if (!empty($actions)) {
            $moduleId = $actions[0]->getModule()->getId();
            $moduleIdInput->setValue($moduleId);
        }
        foreach ($actions as $action) {
            $card = (new InfoCard($action->getName()))
                ->setClass('col-3');
            $row->add($card);
            $actionId = $action->getId();
            $input = (new HiddenInput("actions[$actionId]"))
                ->setValue('off');
            $toggler = new Toggle("actions[$actionId]");
            if (in_array($actionId, $authorized)) {
                $toggler->on();
            }

            $card->body()->feed(
                "Type d'action : " . $action->getType(),
                new Br(),
                "Route : " . $action->getRouteName(),
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
        $this->setAction("/authorizations/actions")
            ->setMethod('POST')
            ->add($buttonRow);
    }
}