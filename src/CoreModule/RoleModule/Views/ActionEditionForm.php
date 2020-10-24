<?php


namespace App\CoreModule\RoleModule\Views;


use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use Entity\DefaultResolver;
use Entity\Model\ManagerInterface;
use ReflectionException;
use Router\Router;
use Ui\HTML\Elements\Bases\Span;
use Ui\HTML\Elements\Nested\A;
use Ui\HTML\Elements\Nested\Form;
use Ui\Views\AssociationSelect;
use Ui\Widgets\Button\DelButton;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Icons\MaterialIcon;
use Ui\Widgets\Table\ColumnsFactory;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\RowFactory;
use Ui\Widgets\Table\RowType;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;
use Ui\Widgets\Table\TableRow;

class ActionEditionForm extends Form
{
    public function __construct(Router $router, ManagerInterface $roleManager, int $roleId, int $moduleId)
    {
        parent::__construct();
        $this->setMethod('POST');

        $url = $router->generateUrl('roles_add_actions', ['role_id' => $roleId, 'module_id' => $moduleId], 'POST');
        $this->setAction($url);


        $actionFieldDefinitions = DefaultResolver::getFieldDefinitions(Action::class);
        $selectFieldName = $actionFieldDefinitions->getAssociationSelectField();
        try {
            $moduleManager = $roleManager->manage(Module::class);
            $module = $moduleManager->findById($moduleId);

            $actionManager = $roleManager->manage(Action::class);
            $association = Action::getAssociation(Module::class);
            $associationSelect = new AssociationSelect($actionManager, $association, $module);

            $row = new TableRow();
            $row->setClass('bg-info');
            $tableColumns = ColumnsFactory::make(Action::class);
            foreach ($tableColumns as $tableColumn) {
                if ($tableColumn->getName() == $selectFieldName) {
                    $row->add($associationSelect);
                } else {
                    $row->add('');
                }
            }
            $row->add((new SubmitButton('Ajouter'))->setClass('btn btn-xs btn-light'));
        } catch (\Exception $e) {
            dump($e);
        }

        $tableColumns[] = (new TableColumn('buttons', ''));
        $rowFactory = new RowFactory(RowType::DIV, $tableColumns);
        $builder = $roleManager->builder();
        $request = $builder->select('actions.*')
            ->from('roles_actions')
            ->join('actions', 'actions_id', 'actions.id')
            ->where('roles_id','=', $roleId)
            ->where('modules_id','=', $moduleId)
            ->where('authorized', '=', true);
        $results = $builder->execute($request);
        $url = $router->generateUrl('roles_show', ['id' => $roleId]);
        $icon = new MaterialIcon('person_outline');
        $icon->color('white')->size('xs');
        $span = new Span($association->getName());
        $span->setAttribute('style', 'vertical-align:bottom;');
        $legendA = (new A($icon, $url))->setClass('btn btn-xs btn-success mb-1');

        try {
            $roleTable = new DivTable(
                [new TableLegend($legendA)],
                $tableColumns,
                [],
            );
            $roleTable->addRow($row);
            foreach ($results as $result) {
                $action = Action::hydrate($result);
                $row = $rowFactory->rowFromModel($action);
                $delButton = new DelButton();
                $delButton->size('xs');
                $delButton->setAttribute('type', 'submit');
                $delUrl = $router->generateUrl(
                    'roles_delete_actions',
                    [
                        'roles_id' => $roleId ,
                        'actions_id' => $action->getId()
                    ],
                    'POST'

                );
                $delButton->setFormAction($delUrl);
                $delButton->setClass('btn btn-sm mr-2 btn-outline-danger');
                $modelColumnsLastIndex = count($tableColumns) - 1;
                $row->getCell($modelColumnsLastIndex )->setValue([$delButton]);

                $roleTable->addRow($row);
            }
            $this->add($roleTable);
        } catch (ReflectionException $e) {
        }

    }
}