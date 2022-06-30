<?php

namespace App\CoreModule\SetupModule\Controller;


use App\App;
use App\Controller;
use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\SetupModule\Views\GodRoleForm;
use App\CoreModule\SetupModule\Views\RootInitForm;
use App\CoreModule\UserModule\Model\User;
use App\Module\Module as ApplicationModule;
use Entity\Database\Dao;
use Entity\Database\DataSourceInterface;
//Setup STATE
// INITIAL, ROLE_OK, FIRST_USER_OK, MODULES_INSTALLED,
class SetupController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->app = App::getInstance();
	}

	private function installDB()
	{
		$modulesClassName = $this->app->getModulesClassName();
		foreach ($modulesClassName as $moduleClassName) {
			$this->installModule($moduleClassName);
		}
	}

	public function installModule(string $moduleClassName)
	{
		if (ApplicationModule::exists($moduleClassName)) {
			$migrationsDir = $moduleClassName::getMigrationsDir($moduleClassName);
			if (file_exists($migrationsDir)) {
				try {
					$dataSource = $this->app->get(DataSourceInterface::class);
					$dao = new Dao($dataSource);
					$environment = $this->app::getEnvironment();
					$database = 'development_db';
					$moduleClassName::install($dao, $database, $environment);
				} catch (\Exception $e) {
					$this->processError($e->getMessage());
				} finally {

				}
			}
		}
	}

	public function installModuleActions(Module $module)
	{
		$actionManager = $this->modelManager(Action::class);
		$installedActions = $this->getInstalledModuleActions($module->getId());
		$moduleClass = $module->getModuleClass();
		$routes = $moduleClass::getRoutes();
		foreach ($routes as $route) {
			if(!array_key_exists('action', $route)) {
				continue;
			}
			if (array_key_exists($route['name'], $installedActions)) {
				continue;
			}
			if(array_key_exists('description', $route['action'])) {
				$description = $route['action']['description'];
			} else {
				$description = $route['name'];
			}
			$action = new Action();
			$action->setName($description);
			$action->setType($route['action']['type'] ?? '');
			$action->setRouteName($route['name'] ?? '');
			$action->setModule($module);
			$actionManager->insert($action);
			$installedActions[$route['name']] = $action;
		}
	}

	private function seedModuleManager()
	{
		$modulesToInstall = array_merge(App::getCoreModules(), App::getApplicationModules());
		if ($modulesToInstall) {
			$moduleManager = $this->modelManager(Module::class);
			$builder = $moduleManager->builder();

			$query = $builder->select('module_class')->from('modules');
			$installedModules = $builder->execute($query);

			$installedModules = array_map(function ($class) {
				$name = $class['module_class'];
				return str_replace('::class', '', $name);
			}, $installedModules);
		}

		foreach ($modulesToInstall as $appModule) {
			if (!in_array($appModule, $installedModules)) {
				if (class_exists($appModule) && is_subclass_of($appModule, ApplicationModule::class)) {
					$moduleManager = $moduleManager->manage(Module::class);
					$name = $appModule::getName();
					$description = $appModule::getDescription();
					$module = new Module();
					$module->setModuleClass($appModule);
					$module->setName($name);
					$module->setDescription($description);
					try {
						$module = $moduleManager->insert($module);
					} catch (\Exception $e) {
						echo '<pre>' . var_dump($e->getMessage());
					}
					$installedModules[] = $appModule;
				}
			}
		}
		foreach ($this->getInstalledModules() as $module) {
			$this->installModuleActions($module);
		}
	}

	public function initGodRole()
	{
		$method = $this->request->getMethod();
		if ($method == 'GET') {
			$form = new GodRoleForm();
			return $this->render($form);
		}
		if ($method == 'POST') {
			$role = new Role();
			$this->requestHandler->handle($role);
			$this->modelManager(Role::class)->insert($role);
			$this->routeTo('init_firstuser', ['id' => $role->getId()]);
		}
	}

	public function initRoot(int $id)
	{
		$method = $this->request->getMethod();
		if ($method == 'GET') {
			$role = $this->modelManager(Role::class)->findById($id);
			return $this->render(new RootInitForm($role));
		}
		if ($method == 'POST') {
			$user = new User();
			$role = new Role();
			$this->requestHandler->handle($role);
			$this->requestHandler->handle($user);
			$password = $this->requestHandler->get('user_password');
			$confirmedPassword = $this->requestHandler->get('user_password_confirmation');
			if ($password == $confirmedPassword) {
				$user->initPassword($password);
				$user->setRoles([$role]);

				$manager = $this->modelManager(User::class);
				$manager->insert($user);
				$builder = $manager->builder();
				$request = $builder->insert('users_roles')
					->columns('users_id', 'roles_id')
					->values(['users_id' => $user->getId(), 'roles_id' => $role->getId()]);
				$builder->execute($request);
				$this->authorizeRoot($user);
				return App::redirectToRoute('modules_index');
			} else {
				$this->routeTo('init_firstuser', ['id' => $role->getId()]);
			}
		}
	}

	public function setup()
	{
		$this->installDB();
		$this->seedModuleManager();
		$this->routeTo('init_firstrole');
	}

	// move to SetupManager class
	private function getInstalledModules()
	{
		$modules = [];
		$moduleManager = $this->modelManager(Module::class);
		$builder = $moduleManager->builder();

		$query = $builder->select()
			->from('modules');
		$installedModules = $builder->execute($query);
		$installedModules = array_map(function ($class) {
			$class['module_class'] =  str_replace('::class', '', $class['module_class']);
			return $class;
		}, $installedModules);

		foreach ($installedModules as $installedModule) {
			$module = Module::hydrate($installedModule);
			$modules[] = $module;
		}
		return $modules;
	}


	// move to SetupManager class

	private function getInstalledModuleActions($moduleId)
	{
		$actions = [];
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();

		$query = $builder->select()
			->from('actions')
			->where('modules_id', '=', $moduleId);
		$installedActions = $builder->execute($query);

		foreach ($installedActions ?: [] as $installedModule) {
			$action = Action::hydrate($installedModule);
			$actions[$action->getRouteName()] = $action;
		}
		return $actions;
	}

	private function getRootAuthorizedModules()
	{
		$moduleManager = $this->modelManager(Module::class);
		$builder = $moduleManager->builder();
		$request = $builder->select()
			->from('roles_modules')
			->where('roles_id', '=', 1);
		$authorizedModules = $builder->execute($request);
		if ($authorizedModules) {
			$authorizedModulesIds = array_column($authorizedModules, 'modules_id');
		} else {
			$authorizedModulesIds = [];
		}

		$installedModules = $moduleManager->findAll() ?: [];
		$actions = [];
		foreach ($installedModules as $installedModule) {
			if (in_array($installedModule->getId(), $authorizedModulesIds)) {
				$modules[$installedModule->getId()] = $installedModule;
			}
		}

		return $modules;
	}

	// move to SetupManager class

	private function getRootAuthorizedActions()
	{
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();
		$request = $builder->select()
			->from('roles_actions')
			->where('roles_id', '=', 1);
		$authorizedActions = $builder->execute($request);
		if ($authorizedActions) {
			$authorizedActionsIds = array_column($authorizedActions, 'actions_id');
		} else {
			$authorizedActionsIds = [];
		}

		$installedActions = $moduleManager->findAll() ?: [];
		$actions = [];
		foreach ($installedActions as $installedAction) {
			if (in_array($installedAction->getId(), $authorizedActionsIds)) {
				$actions[$installedAction->getRouteName()] = $installedAction;
			}
		}

		return $actions;
	}

	// move to SetupManager class

	private function authorizeRoot(User $user)
	{
		$roles = $user->getRoles();
		$role = $roles[0];

		$moduleManager = $this->modelManager(Module::class);
		$builder = $moduleManager->builder();
		$installedModules = $moduleManager->findAll() ?: [];
		$authorizedModules = $this->getRootAuthorizedModules() ?: [];

		foreach ($installedModules as $installedModule) {
			if (array_key_exists($installedModule->getId(), $authorizedModules)) {
				continue;
			}
			$request = $builder->insert('roles_modules')
				->columns('roles_id',
					'modules_id',
					'authorized'
				)->values([
					'roles_id' => $role->getId(),
					'modules_id' => $installedModule->getId(),
					'authorized' => 1
				]);
			$builder->execute($request);
		}
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();
		$installedActions = $moduleManager->findAll() ?: [];
		$authorizedActions = $this->getRootAuthorizedActions() ?: [];
		foreach ($installedActions as $installedAction) {
			if (array_key_exists($installedAction->getRouteName(), $authorizedActions)) {
				continue;
			}
			$request = $builder->insert('roles_actions')
				->columns('roles_id',
					'actions_id',
					'authorized'
				)->values([
					'roles_id' => $role->getId(),
					'actions_id' => $installedAction->getId(),
					'authorized' => 1
				]);
			$builder->execute($request);
		}
	}
}
