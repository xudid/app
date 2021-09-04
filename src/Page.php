<?php

namespace App;

use App\CoreModule\UserModule\Model\User;
use App\CoreModule\UserModule\Views\UserAuthFormFilter;
use App\Session\Session;
use Ui\HTML\Elements\Empties\Br;
use Ui\HTML\Elements\Nested\A;
use Ui\HTML\Elements\Nested\Div;
use Ui\HTML\Elements\Nested\P;
use Ui\Views\FormFactory;
use Ui\Widgets\Cards\InfoCard;
use Ui\Widgets\Toolbars\Action;
use Ui\Widgets\Views\AppPage;
use Ui\Widgets\Views\Modal;
use Ui\Widgets\Views\NavbarItem;

class Page extends AppPage
{
	/**
	 * Page constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$app = App::getInstance();
		$this->importCss(...$app::get('css'));
		$this->importScript(...$app::get('js'));

		$router = $app->get('router');

		$accountModalContent = new Div();
		$accountMenu = new Modal('account_modal', [$accountModalContent]);
		$accountMenu->modal()->setClass('modal-sm centered');

		if (Session::has('user')) {
			$usersBar = new Action(['LIST' => $router->generateUrl('users'), 'ADD' => $router->generateUrl('users_new'), 'SEARCH' => $router->generateUrl('users_search')]);
			$usersCard = new InfoCard('Utilistateurs');
			$usersCard->body()->add($usersBar);

			$rolesBar = new Action(['LIST' => $router->generateUrl('roles'), 'ADD' => $router->generateUrl('roles_new'), 'SEARCH' => 'roles_search']);
			$rolesCard = new InfoCard('Roles');
			$rolesCard->body()->add($rolesBar);

			$modulesBar = new Action(['LIST' => $router->generateUrl('modules_index')]);
			$modulesCard = new InfoCard('Modules');
			$modulesCard->body()->add($modulesBar);

			$routesBar = new Action(['LIST' => $router->generateUrl('routes')]);
			$routesCard = new InfoCard('Routes');
			$routesCard->body()->add($routesBar);

			$administrationMenuGrid = new Div(
				$usersCard,
				$rolesCard,
				$modulesCard,
				$routesCard,
			);
			$administrationMenuGrid->setClass('grid-3');
			$administrationMenu = new Modal('administration_modal', [$administrationMenuGrid]);
			$administrationMenu->setTriggerText('Administration')->setHeaderText('Administration');

			$jobsBar = new Action(['LIST' => $router->generateUrl('modules_index')]);
			$jobsCard = new InfoCard('Gammes');
			$jobsCard->body()->add($jobsBar);

			$planificationBar = new Action(['LIST' => $router->generateUrl('modules_index')]);
			$planificationCard = new InfoCard('Planification');
			$planificationCard->body()->add($planificationBar);

			$sequencingMenuGrid = new Div(
				$jobsCard,
				$planificationCard,
			);
			$sequencingMenuGrid->setClass('grid-3');

			$articlesBar = new Action(['LIST' => $router->generateUrl('modules_index')]);
			$articlesCard = new InfoCard('Articles');
			$articlesCard->body()->add($articlesBar);

			$stockBar = new Action(['LIST' => $router->generateUrl('modules_index')]);
			$stockCard = new InfoCard('Stock');
			$stockCard->body()->add($stockBar);
			$stockMenuGrid = new Div(
				$articlesCard,
				$stockCard,
			);
			$stockMenuGrid->setClass('grid-3');

			$sequencingMenu = new Modal('sequencing_modal', [$sequencingMenuGrid]);
			$stockMenu = new Modal('stock_modal', [$stockMenuGrid]);
			$sequencingMenu->setTriggerText('Planning')->setHeaderText('Planning');
			$stockMenu->setTriggerText('Stock')->setHeaderText('Stock');

			$user = Session::get('user');
			$detailButton = (new A('My Account', '/users/myaccount'))->setClass('btn btn-success');
			$logoutButton = (new A('Logout', '/logout'))->setClass('btn');
			$accountCard = new InfoCard($user->getName());
			$accountCard->body()->setClass('text-center');
			$accountCard->footer()->feed($detailButton, $logoutButton)->setClass('d-flex justify-center');
			$accountMenu->setHeaderText('Logged as : ');
			$accountMenu->setTriggerText($user->getName());
			$accountModalContent->add($accountCard);
			$this->feedNavbarLeft(
				new NavbarItem($administrationMenu),
				new NavbarItem($sequencingMenu),
				new NavbarItem($stockMenu),

			);
			$this->feedNavbarRight(new NavbarItem($accountMenu, NavbarItem::RIGHT),);
		} else {
			//dump(new  LoginForm(new Controller()));

			$formFilter = new UserAuthFormFilter();
			$factory = new FormFactory(User::class);
			$factory->setFormTitle('Login');
			$factory->withAction('/auth');
			$factory->setAccessFilter($formFilter);
			$accountMenu->setTriggerText('Login');
			$form = $factory->getForm();
			$form->setClass('text-center');
			$linkContainer = (new Div(
				new A('Identifiant oublié', ''),
				new Br(),
				new A('Mot de passe oublié', '/reset/password'),
			));
			$form->add($linkContainer);
			$accountModalContent->add($form);
			$this->feedNavbarRight(new NavbarItem($accountMenu, NavbarItem::RIGHT),);

		}
	}

	public function setInfos($infos = [])
	{
		foreach ($infos as $info) {
			$p = new P($info);
			$p->setClass('bg-primary-light px-16 py-16');
			$this->setHeader(new P($p));
		}

	}

	public function setErrors($errors = [])
	{
		foreach ($errors as $error) {
			$p = new P($error);
			$p->setClass('bg-danger-light px-16 py-16');
			$this->setHeader(new P($p));
		}
	}
}
