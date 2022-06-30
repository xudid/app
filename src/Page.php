<?php

namespace App;

use App\Session\Session;
use Ui\HTML\Element\Simple\Br;
use Ui\HTML\Element\Nested\A;
use Ui\HTML\Element\Nested\Div;
use Ui\Widget\Card\Info;
use Ui\Widget\Toolbar\Action;
use Ui\Widget\View\AppPage;
use Ui\Widget\View\Modal;
use Ui\Widget\View\Navbar\Item;
use Ui\X;

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
        $accountMenu->content()->setClass('justify-center');

        if (Session::has('user')) {
            $usersBar = new Action(['LIST' => $router->generateUrl('users'), 'ADD' => $router->generateUrl('users_new'), 'SEARCH' => $router->generateUrl('users_search')]);
			$usersCard = new Info('Utilistateurs');
            $usersCard->body()->add($usersBar);

			$rolesBar = new Action(['LIST' => $router->generateUrl('roles'), 'ADD' => $router->generateUrl('roles_new'), 'SEARCH' => 'roles_search']);
			$rolesCard = new Info('Roles');
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
			$logoutButton = (new A('Logout', '/logout'))->setClass('btn');
			$accountCard = new Info($user->getName());
			$accountCard->body()->setClass('text-center');
			$accountCard->footer()->feed($detailButton, $logoutButton)->setClass('d-flex justify-center');
			$accountMenu->setHeaderText('Logged as : ');
			$accountMenu->setTriggerText($user->getName());
			$accountModalContent->add($accountCard);
			$this->feedNavbarLeft(
				new Item($administrationMenu),
				new Item($sequencingMenu),
				new Item($stockMenu),

			);
            $this->feedNavbarRight(new Item($accountMenu, Item::RIGHT),);
		} else {
            $accountMenu->setTriggerText('Login');
            $form = X::Form(
                X::TextField()->label('Identifiant')->name('username'),
                X::TextField()->label('Mot de passe')->name('password'),
                X::Button('Valider')
            );
            $form->setClass('text-center');
            $linkContainer = (new Div(
                new A('Identifiant oublié', ''),
                new Br(),
                new A('Mot de passe oublié', '/reset/password'),
            ));
            $form->add($linkContainer);
            $accountModalContent->add($form);
			$this->feedNavbarRight(new Item($accountMenu, Item::RIGHT),);

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
