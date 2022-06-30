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
		$result = $manager->getAuthorizedRolesIds($routeName);
		if ($result) {
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
		}

		return false;
	}

	public function getAuthorizedModules()
	{
		$modules = [];
		if (Session::has('user')) {
			$user = Session::get('user');
			$roles = $user->getRoles();
			foreach ($roles as $role) {
				$result = $this->modelManager(Role::class)
					->findAssociationValuesBy(Module::class, $role);
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
		$authorizedModules = $builder->select('modules_id')
			->from('roles_modules')
			->where('roles_id','=', $id)
			->where('authorized', '=', true)
			->execute();
		$authorizedModules = array_column($authorizedModules,'modules_id');

		$form = new RoleModuleAuthorizationView($this->router, $id,$authorizedModules,...$modules);
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
		$builder = $this->modelManager(Module::class)->builder();
		foreach ($modules as $moduleId => $authorized) {
			$authorized = $authorized == 'on' ? 1 : 0;
			$result = $builder->insert('roles_modules')
				->columns('roles_id', 'modules_id', 'authorized')
				->values(['roles_id' => $roleId, 'modules_id' => $moduleId, 'authorized' => $authorized])
				->execute();
			if (!$result) {
				$builder->update('roles_modules')
					->set('authorized', $authorized)
					->where('roles_id', '=', $roleId)
					->where('modules_id', '=', $moduleId)
					->execute();
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

		$manager = $this->modelManager(
			Role::class,
			AuthorizationManager::class
		);
		$authorizedActions = $manager->getAuthorizedModuleActions($role, $module);
		if ($authorizedActions) {
			$authorizedActions = array_column($authorizedActions,'actions_id');
		} else {
			$authorizedActions = [];
		}
		$form = new RoleActionAuthorizationView($roleId, $authorizedActions , ...$actions);
		$view = new EntityView();
		$view->setTitle("Authorized actions ");
		$view->setSubTitle(Inflector::ucwords($role->getName()));
		$view->add($form);
		return App::render($view);
	}

	public function registerActionsAuthorizations()
	{
		$roleId = $this->requestHandler->get('role_id');
		$role = $this->modelManager(Role::class)->findById($roleId);
		$moduleId = $this->requestHandler->get('module_id');

		if (!$roleId || !$moduleId) {
			$this->alert('try to register actions without module and role', 'info');
		}
		$actions = $this->requestHandler->get('actions');
		$manager = $this->modelManager(
			Role::class,
			AuthorizationManager::class
		);
		foreach ($actions as $actionId => $authorized) {
			$authorized = $authorized == 'on' ? 1 : 0;
			$action = $this->modelManager(Action::class)->findById($actionId);
			if ($authorized) {
				$manager->authorizeAction($role, $action);
			} else {
				$manager->removeActionAuthorization($role, $action);
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
				$authorizedActions = $builder->select('actions.*')
					->from('roles_actions')
					->join('actions', 'actions_id', 'actions.id')
					->where('roles_actions.roles_id','=', $role->getId())
					->where('modules_id','=', $module->getId())
					->where('authorized', '=', true)
					->execute();
				if ($authorizedActions) {
					$actions = array_merge($actions, $authorizedActions);
				}
			}
		}
		return $actions;
	}
}
