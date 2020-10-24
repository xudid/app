<?php


namespace App\CoreModule\RoleModule\Views;


use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\UserModule\Model\User;
use Entity\DefaultResolver;
use Entity\Model\ManagerInterface;
use ReflectionException;
use Router\Router;
use Ui\HTML\Elements\Bases\Span;
use Ui\HTML\Elements\Nested\A;
use Ui\HTML\Elements\Nested\Form;
use Ui\Views\AssociationSelect;
use Ui\Views\FormFieldAdder;
use Ui\Widgets\Button\DelButton;
use Ui\Widgets\Button\IconButton;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Icons\MaterialIcon;
use Ui\Widgets\Table\ColumnsFactory;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\RowFactory;
use Ui\Widgets\Table\RowType;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;
use Ui\Widgets\Table\TableRow;

class ModuleEditionForm extends Form
{
    public function __construct(Router $router, ManagerInterface $roleManager, ManagerInterface $moduleManager, int $id)
    {
        parent::__construct();
        $this->setMethod('POST');
        $url = $router->generateUrl('roles_add_modules', ['id' => $id], 'POST');
        $this->setAction($url);
        $role = $roleManager->findById($id);
        $rows = [];
        $tableColumns = ColumnsFactory::make(Module::class);
        $row = new TableRow();
        $row->setClass('bg-info');

        $fieldAdder = new FormFieldAdder(Module::class, $row);
        $fieldAdder->inline();

        $roleFieldDefinitions = DefaultResolver::getFieldDefinitions(Module::class);
        $association = Role::getAssociation(Module::class);
        $selectFieldName = $roleFieldDefinitions->getAssociationSelectField();
        try {
            /** @var TYPE_NAME $associationSelect */
            $associationSelect = new AssociationSelect($moduleManager, $association);

            foreach ($tableColumns as $tableColumn) {
                if ($tableColumn->getName() == $selectFieldName) {
                    $row->add($associationSelect);
                } else {
                    $row->add('');
                }
            }
            $row->add((new SubmitButton('Ajouter'))->setClass('btn btn-xs btn-light'));
            $rows[] = $row;

        } catch (\Exception $e) {
            dump($e);
        }

        $modelColumnsLastIndex = count($tableColumns) - 1;
        $tableColumns[] = (new TableColumn('buttons', ''));
        $rowFactory = new RowFactory(RowType::DIV, $tableColumns);
        $builder = $roleManager->builder();
        $request = $builder->select('modules.*')
            ->from('modules')
            ->join('roles_modules','modules.id', 'modules_id')
            ->where('roles_id', '=', $role->getId())
            ->where('authorized', '=', '1');
        $results = $builder->execute($request) ?: [];
        $url = $router->generateUrl('roles_show', ['id' => $id]);

        $icon = new MaterialIcon('person');
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
                $module = Module::hydrate($result);
                $row = $rowFactory->rowFromModel($module);
                $delButton = new DelButton();
                $delButton->size('xs');
                $delButton->setAttribute('type', 'submit');
                $delUrl = $router->generateUrl(
                    'roles_delete_modules',
                    [
                        'roles_id' => $id ,
                        'modules_id' => $module->getId()
                    ],
                    'POST'
                );
                $delButton->setFormAction($delUrl);

                $actionsButton = new IconButton('settings');
                $actionsButton->size('xs');
                $actionsButton->setAttribute('type', 'submit');
                $actionsUrl = $router->generateUrl(
                    'roles_actions_new',
                    [
                        'role_id' => $id ,
                        'module_id' => $module->getId()
                    ],
                    'POST'
                );

                $actionsButton->setFormAction($actionsUrl);
                $delButton->setClass('btn btn-sm mr-2 btn-outline-danger');
                $actionsButton->setClass('btn btn-sm mr-2 btn-outline-danger');
                $row->getCell($modelColumnsLastIndex + 1)->setValue([$delButton, $actionsButton]);
                $roleTable->addRow($row);

            }
            $this->add($roleTable);
        } catch (ReflectionException $e) {
        }

    }
}