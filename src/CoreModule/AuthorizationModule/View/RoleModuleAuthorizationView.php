<?php

namespace App\CoreModule\AuthorizationModule\View;

use App\CoreModule\ManagerModule\Model\Module;
use Ui\HTML\Elements\Bases\Span;
use Ui\HTML\Elements\Empties\Br;
use Ui\HTML\Elements\Empties\Hr;
use Ui\HTML\Elements\Empties\Input;
use Ui\HTML\Elements\Nested\Form;
use Ui\HTML\Elements\Nested\Div;
use Ui\HTML\Elements\Nested\P;
use Ui\Widgets\Button\CheckBox;
use Ui\Widgets\Button\IconButton;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Button\Toggle;
use Ui\Widgets\Cards\InfoCard;
use Ui\Widgets\Icons\MaterialIcon;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Views\Row;
use Router\Router;

class RoleModuleAuthorizationView extends Form
{

	/**
	 * RoleModuleAuthorizationView constructor.
	 * @param Module $modules
	 */
	public function __construct(Router $router, int $id,array $authorized, Module...$modules)
	{
		parent::__construct();
		$roleIdInput = (new HiddenInput('role_id'))
			->setValue($id);
		$this->add($roleIdInput);
		$grid = (new Div())
			->setClass('grid-3 justify-content-center');
		$this->add($grid);
		foreach ($modules as $module) {
			$card = (new InfoCard($module->getName()))
				->setClass('col-3');
			$grid->add($card);

			$moduleId = $module->getId();
			$input = (new HiddenInput("modules[$moduleId]"))
				->setValue('off');
			$toggler = new Toggle("modules[$moduleId]");
			if (in_array($moduleId, $authorized)) {
				$toggler->on();
			}
			$togglerContainer = new Div();
			$togglerContainer->setClass('flex-end text-center');
			$togglerContainer->add($toggler);

			/*$actionsButton = new IconButton('settings');
			$actionsButton->size('xs');*/
			$icon = new MaterialIcon('build');
			$icon->color('white')
				->size('xs')
				->setClass('pr-8');



			$actionsUrl = $router->generateUrl(
				'roles_actions_new',
				[
					'role_id' => $id ,
					'module_id' => $module->getId()
				],
				'POST'
			);

			$actionsButton = new SubmitButton('');
			$actionsButton->setAttribute('type', 'submit')
				->setFormAction($actionsUrl)
				->setClass('btn-success flex-1 mt-8 py-4')
				->feed(
					$icon,
					'Actions'
				);

			$card->setClass('d-column')
				->body()
				->setClass('d-column')
				->feed(
					(new P($module->getDescription()))->setClass('mx-8'),
					new Br(),
					$actionsButton,
					(new Hr())->setClass('bg-primary'),
					$input,
				);

			$card->footer()->add($togglerContainer)->setClass('d-column');
		}

		$button = (new SubmitButton('Authorise'))
			->setClass('btn btn-primary');
		$buttonRow = (new Row())
			->setClass('mt-12 justify-center')
			->add($button);
		$this->setAction('/authorizations/modules')
			->setMethod('POST')
			->add($buttonRow);
	}
}