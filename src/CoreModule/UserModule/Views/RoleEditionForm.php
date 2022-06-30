<?php


namespace App\CoreModule\UserModule\Views;


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
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Icons\MaterialIcon;
use Ui\Widgets\Table\Cell;
use Ui\Widgets\Table\ColumnsFactory;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\RowFactory;
use Ui\Widgets\Table\RowType;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;
use Ui\Widgets\Table\TableRow;

class RoleEditionForm extends Form
{
	public function __construct(Router $router, ManagerInterface $userManager, int $id)
	{
		parent::__construct();
		$roleManager = $userManager->manage(Role::class);
		$this->setMethod('POST');

		$url = $router->generateUrl('users_add_roles', ['id' => $id], 'POST');
		$this->setAction($url);

		$user = $userManager->findById($id);

		$rows = [];
		$tableColumns = ColumnsFactory::make(Role::class);
		$row = new TableRow();
		$row->setClass('bg-info');

		$roleFieldDefinitions = DefaultResolver::getFieldDefinitions(Role::class);
		$association = User::getAssociation(Role::class);
		$selectFieldName = $roleFieldDefinitions->getAssociationSelectField();

		try {
			$associationSelect = new AssociationSelect($roleManager, $association);

			foreach ($tableColumns as $tableColumn) {
				if ($tableColumn->getName() == $selectFieldName) {
					$row->add($associationSelect);
				} else {
					$row->add('');
				}
			}

			$buttonsColumn = new TableColumn('buttons', ' ');
			$tableColumns[] = $buttonsColumn;
			$rowFactory = new RowFactory(RowType::DIV, $tableColumns);
			$rowFactory->useBaseUrl('/roles');
			$results = $userManager->findAssociationValuesBy(Role::class, $user);

			$url = $router->generateUrl('users_show', ['id' => $id]);

			$icon = new MaterialIcon('person');
			$icon->color('white')->size('xs');
			$span = new Span($association->getName());
			$span->setAttribute('style', 'vertical-align:bottom;');
			$legendA = (new A($icon, $url))->setClass('btn btn-xs btn-success mb-1');

			$addButton = (new SubmitButton('Ajouter'))->setClass('btn btn-xs btn-light');
			$addButtonCell = new Cell($addButton);
			$row->addCell($buttonsColumn, $addButtonCell);
			$rows[] = $row;
			$roleTable = new DivTable(
				[new TableLegend($legendA)],
				$tableColumns,
				[],
			);
			$roleTable->addRow($row);

			foreach ($results as $result) {
				$row = $rowFactory->rowFromModel($result);

				$delButton = new DelButton();
				$delButton->size('xs');
				$delButton->setAttribute('type', 'submit');
				$delUrl = $router->generateUrl(
					'users_delete_roles',
					[
						'users_id' => $id ,
						'roles_id' => $result->getId()
					],
					'POST'

				);
				$delButton->setFormAction($delUrl);
				$delButton->setClass('btn btn-sm mr-2 btn-outline-danger');
				$cell = $row->getCell($buttonsColumn);
				$cell->setValue($delButton);
				$roleTable->addRow($row);

			}
			$this->add($roleTable);

		} catch (ReflectionException $e) {
			dump($e);
		}
	}
}