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
use Ui\Handler\RequestHandler;

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

	private function installModule(string $moduleClassName)
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
				} catch (Exception $e) {
					var_dump($e->getMessage());
				}
			}
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

				$this->modelManager(User::class)->insert($user);
			} else {
				// show alert message
			}
		}
	}

	public function setup()
	{
		$this->installDB();
		$fileName = App::$configDirectory . DIRECTORY_SEPARATOR . App::$modules;
		$this->routeTo('init_firstrole');
		exit;
		//test if modules , actions tables exists
		//if not create them
		//if AuthorizationModule
		//test if users, roles, users_roles, roles_modules and roles_action exists
		//if not create them

		if (file_exists($fileName)) {
			$moduleManager = $this->modelManager(Module::class);
			$builder = $moduleManager->builder();

			$query = $builder->select('module_class')->from('modules');
			$installedModules = $builder->execute($query);

			$installedModules = array_map(function ($class) {
				$name = $class['module_class'];
				return str_replace('::class', '', $name);
			}, $installedModules);

			$modulesToInstall = require $fileName;
			foreach ($modulesToInstall as $appModule) {
				if (!in_array($appModule, $installedModules)) {
					if (class_exists($appModule) && is_subclass_of($appModule, ApplicationModule::class)) {
						$name = $appModule::getName();
						$description = $appModule::getDescription();
						$module = new Module();
						$module->setModuleClass($appModule);
						$module->setName($name);
						$module->setDescription($description);
						$moduleManager->insert($module);
						$installedModules[] = $appModule;
					}
				}
			}
			$actionManager = $moduleManager->manage(Action::class);

			foreach ($this->getInstalledModules() as $module) {
				// move code to get module routes in module class
				$installedActions = $this->getInstalledModuleActions($module->getId());
				$class = $module->getModuleClass();
				$dir = $class::getDir();
				$routesFileName = $dir . DIRECTORY_SEPARATOR . 'routes.php';
				if (file_exists($routesFileName)) {
					$routes = require $routesFileName;

					// code to move
					$routes = is_array($routes) ? $routes : [];
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
					};
				}
			}
		}

		//if
		//dump($this->getRootAuthorizedActions());
		$this->authorizeRoot();

		return App::redirectToRoute('modules_index');
	}

	private function getInstalledModules()
	{
		$modules = [];
		$moduleManager = $this->modelManager(Module::class);
		$builder = $moduleManager->builder();

		$query = $builder->select()->from('modules');
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



	private function getInstalledModuleActions($moduleId)
	{
		$actions = [];
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();

		$query = $builder->select()->from('actions')->where('modules_id', '=', $moduleId);
		$installedActions = $builder->execute($query);

		foreach ($installedActions ?: [] as $installedModule) {
			$action = Action::hydrate($installedModule);
			$actions[$action->getRouteName()] = $action;
		}
		return $actions;
	}

	private function getRootAuthorizedActions()
	{
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();
		$request = $builder->select()->from('roles_actions')->where('roles_id', '=', 1);
		$authorizedActions = $builder->execute($request);
		if ($authorizedActions) {
			$authorizedActionsIds = array_column($authorizedActions, 'actions_id');
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

	private function authorizeRoot()
	{
		$moduleManager = $this->modelManager(Action::class);
		$builder = $moduleManager->builder();
		$installedActions = $moduleManager->findAll() ?: [];
		$authorizedActions = $this->getRootAuthorizedActions() ?: [];
		foreach ($installedActions as $installedAction) {
			if (array_key_exists($installedAction->getRouteName(), $authorizedActions)) {
				continue;
			}
			$request = $builder->insert('roles_actions')->columns('roles_id', 'actions_id', 'authorized')->values(['roles_id' => 1, 'actions_id' => $installedAction->getId(), 'authorized' => 1]);
			$builder->enableDebug();
			$builder->execute($request);
		}
	}

}