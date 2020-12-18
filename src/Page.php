<?php


namespace App;


use App\CoreModule\UserModule\Model\User;
use App\CoreModule\UserModule\Views\UserAuthFormFilter;
use App\Session\Session;
use Ui\HTML\Elements\Empties\Br;
use Ui\HTML\Elements\Nested\A;
use Ui\HTML\Elements\Nested\Div;
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
		$app =App::getInstance();
		$this->importCss(...$app::get('css'));
		$this->importScript(...$app::get('js'));

		$router = $app->get('router');

		$accountModalContent  = new Div();
		$accountMenu = new Modal('account_modal', $accountModalContent);
		$accountMenu->popup()->setClass('popup popup-sm centered');
		$accountMenu->content()->setClass('justify-center');

		if (Session::has('user')) {
			$usersBar = new Action(['LIST' => $router->generateUrl('users'), 'ADD' => $router->generateUrl('users_new'), 'SEARCH' => $router->generateUrl('users_search')]);
			$usersCard = new InfoCard('Utilistateurs', 'test');
			$usersCard->body()->add($usersBar);
			$rolesBar = new Action(['LIST' => $router->generateUrl('roles_index'), 'ADD' => $router->generateUrl('roles_new'), 'SEARCH' => 'roles_search']);
			$rolesCard = new InfoCard('Roles', 'test');
			$rolesCard->body()->add($rolesBar);
			$modalMenuContent1 = new Div(
				$usersCard,
				$rolesCard,
				(new A('Setup', '/setup'))->setClass('mr-2 mt-2'),
				(new A('Modules', '/modules'))->setClass('mr-2 mt-2'),
				(new A('Routes', '/routes'))->setClass('mr-2 mt-2'),
			);

			$modalMenu = new Modal('nav_modal_1', $modalMenuContent1);
			$modalMenu->setTriggerText('Administration')->setHeaderText('Administration');
			$modalMenuContent2 = new Div(
				(new A('Gammes', '/gammes'))->setClass('mr-2 mt-2'),
				(new A('Programme', '/sequencing'))->setClass('mr-2 mt-2'),
			);
			$modalMenuContent3 = new Div(
				(new A('Articles', '/articles'))->setClass('mr-2 mt-2'),
				(new A('Stock', '/stock'))->setClass('mr-2 mt-2'),
			);

			$modalMenu2 = new Modal('nav_modal_2', $modalMenuContent2);
			$modalMenu3 = new Modal('nav_modal_3', $modalMenuContent3);
			$modalMenu2->setTriggerText('Planning')->setHeaderText('Planning');
			$modalMenu3->setTriggerText('Stock')->setHeaderText('Stock');

			$user = Session::get('user');
			$detailButton = (new A('My Account', '/users/myaccount'))->setClass('btn btn-success');
			$logoutButton = (new A('Logout', '/logout'))->setClass('button');
			$accountCard = new InfoCard($user->getName(), $detailButton);
			$accountCard->body()->setClass('text-center');
			$accountCard->footer()->add($logoutButton)->setClass('d-flex justify-center');
			$accountMenu->setTriggerText($user->getName());
			$accountModalContent->add($accountCard);
			$this->feedNavbarLeft(
				new NavbarItem($modalMenu),
				new NavbarItem($modalMenu2),
				new NavbarItem($modalMenu3),

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
}
