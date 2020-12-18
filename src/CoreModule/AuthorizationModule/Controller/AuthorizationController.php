<?php

namespace App\CoreModule\AuthorizationModule\Controller;

use App\App;
use App\Controller;
use App\CoreModule\AuthorizationModule\Model\AuthorizationManager;
use App\CoreModule\AuthorizationModule\View\RoleActionAuthorizationView;
use App\CoreModule\AuthorizationModule\View\RoleModuleAuthorizationView;
use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\UserModule\Model\User;
use App\Session\Session;
use Doctrine\Common\Inflector\Inflector;
use Entity\Database\QueryBuilder\QueryBuilder;
use Exception;
use Ui\Views\EntityView;

/**
 * Class AuthorizationController
 * @package App\CoreModule\AuthorizationModule\Controller
 */
class AuthorizationController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function isAuthorize(array $roles, string $routeName)
	{
		$manager = $this->modelManager(Action::class, AuthorizationManager::class);
		$builder = $manager->builder();
		$request= $builder->select('roles.id')
			->from('roles')
			->join('roles_actions', 'roles.id', 'roles_id')
			->join('actions', 'actions_id', 'actions.id')
			->where('route_name', '=', $routeName)
			->where('authorized', '=', ' 1');
		$result = $builder->execute($request);
		$authorizedRolesIds = array_column($result, 'id');
		foreach ($roles as $role) {
			//root can access every thing
			if ($role->getId() == 1 ) {
				return true;
			}
			// if user has one role authorized for this action
			if (in_array($role->getId(), $authorizedRolesIds)) {
				return true;
			}
		}
		return false;
	}

	public function getAuthorizedModules()
	{
		$modules = [];
		if (Session::has('user')) {
			$user = Session::get('user');
			$roles = $user->getRoles();
			$rolesManager = $this->modelManager(Role::class);
			foreach ($roles as $role) {
				$result = $rolesManager->findAssociationValuesBy(Module::class, $role);
				if ($result) {
					$modules = array_merge($modules, $result);
				}
			}
		}
		return $modules;
	}

	public function authorizeRole($id)
	{
		$modulesManager = $this->modelManager(Module::class);
		$roleManager = $this->modelManager(Role::class);
		$role = $roleManager->findById($id);
		$modules = $modulesManager->findAll();
		$builder = $modulesManager->builder();
		$request = $builder->select('modules_id')
			->from('roles_modules')
			->where('roles_id','=', $id)
			->where('authorized', '=', true);
		$authorizedModules = $builder->execute($request);
		$authorizedModules = array_column($authorizedModules,'modules_id');

		$form = new RoleModuleAuthorizationView($id,$authorizedModules,...$modules);
		$view = new EntityView();
		$view->setTitle('Autorisations Modules ');
		$view->setSubTitle(Inflector::ucwords($role->getName()));
		$view->add($form);

		return App::render($view);
	}

	public function registerModulesAuthorizations()
	{
		$roleId = $_POST['role_id'];
		$modules = $_POST['modules'];
		$builder = App::get(QueryBuilder::class);
		foreach ($modules as $moduleId => $authorized) {
			$authorized = $authorized == 'on' ? 1 : 0;
			$request = $builder::insert('roles_modules');
			$request->columns('roles_id', 'modules_id', 'authorized');
			$request->values(['roles_id' => $roleId, 'modules_id' => $moduleId, 'authorized' => $authorized]);
			$result = $builder::execute($request);
			if (!$result) {
				$request = $builder::update('roles_modules');
				$request->set('authorized', $authorized);
				$request->where('roles_id', '=', $roleId);
				$request->where('modules_id', '=', $moduleId);
				$builder::execute($request);
			}
		}
		$this->app->redirectTo('/authorizations/role/' . $roleId);
	}

	public static function module(string $moduleClass)
	{
		$app = App::getInstance();
		$modulesManager = $app->getModelManager(Module::class);
		return $modulesManager->findBy(['module_class' => $moduleClass]);
	}

	public function authorizeModuleActions(int $roleId, int $moduleId)
	{
		$modulesManager = $this->modelManager(Module::class);
		$module = $modulesManager->findById($moduleId);
		$rolesManager = $this->modelManager(Role::class);
		$role = $rolesManager->findById($roleId);
		$actionsManager = $this->modelManager(Action::class);
		$actions = $actionsManager->findBy(['modules_id' => $moduleId]);
		foreach ($actions as $action) {
			$action->setModule($module);
		}

		$builder = $actionsManager->builder();
		$request = $builder->select('actions_id')
			->from('roles_actions')
			->join('actions', 'actions_id', 'id')
			->where('roles_id','=', $roleId)
			->where('modules_id','=', $moduleId)
			->where('authorized', '=', true);
		$authorizedActions = $builder->execute($request);
		$authorizedActions = array_column($authorizedActions,'actions_id');

		$form = new RoleActionAuthorizationView($roleId, $authorizedActions, ...$actions);
		$view = new EntityView();
		$view->setTitle("Autorisations Actions ");
		$view->setSubTitle(Inflector::ucwords($role->getName()));
		$view->add($form);
		return App::render($view);
	}

	public function registerActionsAuthorizations()
	{
		$roleId = $_POST['role_id'];
		$moduleId = $_POST['module_id'];
		if (!$roleId || !$moduleId) {
			$this->app->internalError('try to register actions without module and role');
		}
		$modules = $_POST['actions'];
		$builder = App::get(QueryBuilder::class);
		foreach ($modules as $actionId => $authorized) {
			$authorized = $authorized == 'on' ? 1 : 0;
			$request = $builder::insert('roles_actions');
			$request->columns('roles_id', 'actions_id', 'authorized');
			$request->values(['roles_id' => $roleId, 'actions_id' => $actionId, 'authorized' => $authorized]);
			$result = $builder::execute($request);
			if (!$result) {
				$request = $builder::update('roles_actions');
				$request->set('authorized', $authorized);
				$request->where('roles_id', '=', $roleId);
				$request->where('actions_id', '=', $actionId);
				$result = $builder::execute($request);
			}
		}
		$this->redirect("/authorizations/role/$roleId/module/$moduleId/actions");
	}

	public static function authorizedAction(string $moduleClass)
	{
		$app = App::getInstance();
		$module = self::module($moduleClass)[0];
		$actions = [];
		if (Session::has('user')) {
			$user = Session::get('user');
			$userManager = $app->getModelManager(User::class);
			$roles = $userManager->findAssociationValuesBy(Role::class, $user);
			$actionsManager = $app->getModelManager(Action::class);
			$builder = $actionsManager->builder();
			foreach ($roles as $role) {
				$request = $builder->select('actions.*')
					->from('roles_actions')
					->join('actions', 'actions_id', 'actions.id')
					->where('roles_actions.roles_id','=', $role->getId())
					->where('modules_id','=', $module->getId())
					->where('authorized', '=', true);
				$authorizedActions = $builder->execute($request);
				if ($authorizedActions) {
					$actions = array_merge($actions, $authorizedActions);
				}
			}
		}
		return $actions;
	}
}
