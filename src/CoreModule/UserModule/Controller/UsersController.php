<?php

namespace App\CoreModule\UserModule\Controller;

use App\App;
use App\Controller;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\UserModule\Model\User;
use App\CoreModule\UserModule\Model\UsersManager;
use App\CoreModule\UserModule\UserModule;
use App\CoreModule\UserModule\Views\RoleEditionForm;
use App\Session\Session;
use Entity\SearchBinder;
use ReflectionException;
use Ui\Views\EntityView;
use Ui\Widgets\Button\AddButton;
use Ui\Widgets\Table\TableLegend;
use Exception;

// to make facade for widgets constructors

/**
 *
 */
class UsersController extends Controller
{
	private $moduleClass = UserModule::class;

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Return a form to get new user datas
	 */
	public function new()
	{
		try {
			$view = $this->formFactory(User::class)
				->withAction('/users/create')
				->setFormTitle("New user")
				->getForm();
		} catch (ReflectionException $exception) {
			$view = $this->processError($exception->getMessage());
		} finally {
			return $this->render($view);
		}
	}

	/**
	 * @param $id User Id
	 * @return string|\Ui\HTML\Elements\Bases\Base
	 */
	public function edit($id)
	{
		$user = $this->modelManager(User::class)->findById($id);
		$view = '';
		if ($user == false) {
			$this->alert('User not found', 'infos');
		}
		try {
			$url = $this->router->generateUrl('users_update', ['id' => $id], 'POST');
			$view = $this->formFactory($user)
				->setFormTitle('Edit user')
				->withAction($url)
				->getForm();
		} catch (Exception $exception) {
			$view = $this->processError('Error in : ' . __CLASS__ . ', ' . __METHOD__ . ' ' . $exception->getMessage());
		} finally {
			return $this->render($view);
		}
	}

	public function editSelf()
	{
		if (Session::has('user')) {
			$user = Session::get('user');
			return $this->render($this->formFactory($user)
				->setFormTitle('My account')
				->getForm());
		} else {
			$this->routeTo('login');
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 * @throws Exception
	 */
	public function show($id): mixed
	{
		$factory = $this->entityViewFactory(User::class, $id);
		$factory->setCurrentPath("/users/:id");

		// Todo respect action type order
		$actions = App::autorize($this->moduleClass, ['EDIT', 'DELETE', 'NEW', 'LIST', 'SEARCH']);
		foreach ($actions as $action => $routeName) {
			$url = $this->router->generateUrl($routeName, ['id' => $id]);
			$factory->useAction($action, $url);
		}
		return $this->render($factory->getView());
	}

	/**
	 * Create a new user
	 */
	public function create()
	{
		//Todo make RequestHandler able to process password fields
		$user = new User();
		$this->requestHandler->handle($user);

		$role = new Role();
		$this->requestHandler->handle($role, 'roles');
		$user->setRoles([$role]);

		$manager = $this->modelManager(User::class, UsersManager::class);
		$id = ($manager->insert($user))->getId();

		$this->app->redirectTo('/users/' . $id);
		$this->routeTo('users_show', ['id' => $id]);
	}

	/**
	 * @param array $params : params to delete a user
	 * @throws Exception
	 */
	public function delete(array $params)
	{
		$id = $params['id'];

		$manager = $this->modelManager(User::class);
		$user = $manager->findById($id);
		if ($user) {
			$manager->delete($user);
		}
		$this->alert('You have deleted user with id: ' . $id);
		$this->routeTo('users');
	}

	/**
	 * Update user with
	 * @param $id
	 */
	public function update($id)
	{
		$manager = $this->modelManager(User::class);
		$user = $manager->findById($id);
		if ($user == false) {
			$this->app->showInfo("User not found");
		}
		$user->setName($_POST['user_name']);
		$user->initPassword($_POST['user_password']);
		$user->setEmail($_POST['user_email']);
		//$user->setRole($_POST['user_role']);
		$manager->update($user);
		$this->routeTo('users_show', ['id' => $id]);
	}


	/**
	 * Search UserModule
	 */
	public function search()
	{
		// Todo embed form and result table in the same factory
		// Think to concept of type of search strict around
		// then replace where = by WHERE LIKE
		//Make possible to pass comma separated values in search
		// and then make request WHERE IN
		// Todo make possible to enable association fields in research
		$view = new EntityView();
		$searchFactory = $this->searchViewFactory(User::class);
		$searchView = $searchFactory->withAction('/users/search')
			->setTitle("Search User")
			->getView($this->app);
		try {
			$searchBinder = new SearchBinder($this->request);
			$params = $searchBinder->bind(User::class);
			$resultsView = ($this->tableFactory(User::class))->withBaseUrl("/users");
			$resultsView->where($params);
			// Todo bind association fields
			/*if (isset($_POST["role"]) && $_POST["role"] != "") {
				$roleManager = $this->app->getModelManager(Role::class);
				$param['name'] = $_POST["role"];
				$role = $roleManager->findBy($param);
				//var_dump($role[0]->getId());
				//$view->where($params);
			}*/
			$resultsView->setTitle("<h2>Resultat de la recherche</h2>");
			$resultsTable = $resultsView->getView($this->app);
		} catch (Exception $exception) {
			$this->alert("Failed to search users");
			$this->processError($exception->getMessage());
			$resultsTable = '';
		}
		return $view->feed($searchView, $resultsTable);
	}

	/**
	 * @param $id
	 * @return RoleEditionForm
	 */
	public function editRoles($id)
	{
		return new RoleEditionForm(
			$this->router,
			$this->modelManager(User::class),
			$id
		);
	}

	/**
	 * @param $id
	 */
	public function addRole($id)
	{
		$role = new Role();
		$this->requestHandler->handle($role, 'roles');
		$userManager = $this->modelManager(User::class, UsersManager::class);
		$user = $userManager->findById($id);
		$userManager->addRole($user, $role);
		$this->routeTo('users_roles', ['id' => $id]);
	}

	/**
	 * @param $userId
	 * @param $roleId
	 */
	public function deleteRole($userId, $roleId)
	{
		$userManager = $this->modelManager(User::class, UsersManager::class);
		$userManager->deleteRole($userId, $roleId);
		$this->routeTo('users_roles', ['id' => $userId]);
	}

	/**
	 * Return a view with the user list
	 */
	public function index()
	{
		try {
			$factory = ($this->tableFactory(User::class))
				->withBaseUrl('/users');
			$legendTitle = new TableLegend(
				"<h4>Users</h4>",
				TableLegend::TOP_LEFT
			);
			$addButton = new AddButton();
			$addButton->size('xs')
				->setOnClick("location.href='/users/new'");
			$legendButton = new TableLegend($addButton, TableLegend::TOP_RIGHT);
			$factory->addALegend($legendTitle);
			$factory->addALegend($legendButton);
			$factory->setRouter($this->router);
			$view = $factory->getView($this->app);
		} catch (Exception $exception) {
			$view = $this->processError($exception->getMessage());
		} finally {
			return $this->render($view);
		}
	}
}
