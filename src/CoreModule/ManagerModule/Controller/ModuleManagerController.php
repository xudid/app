<?php


namespace App\CoreModule\ManagerModule\Controller;


use App\App;
use App\Controller;
use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\SetupModule\Controller\SetupController;
use App\CoreModule\UserModule\Model\User;
use App\Module\Module as ApplicationModule;
use GuzzleHttp\Psr7\ServerRequest;
use ReflectionException;
use Ui\Handler\RequestHandler;
use Ui\Views\DataTableView;
use Ui\Views\EntityViewFactory;
use Ui\Views\FormFactory;
use Ui\Widgets\Button\AddButton;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;

class ModuleManagerController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		try {
			$dtv = $this->tableFactory(Module::class)
				->withBaseUrl('/modules');

			$legendTitle = new TableLegend("<h4>Liste des modules</h4>", TableLegend::TOP_LEFT);
			$addButton = new AddButton();
			$addButton->setOnClick("location.href='/modules/new'");
			$legendButton = new TableLegend($addButton, TableLegend::TOP_RIGHT);
			$dtv->addALegend($legendTitle);
			$dtv->addALegend($legendButton);
			return $this->render($dtv->getView($this->app));
		} catch (ReflectionException $e) {
			dump($e);
		}
	}

	public function new()
	{
		$formFactory = $this->formFactory(Module::class);
		$formFactory->withAction('/modules/create');
		return $this->render($formFactory->getForm());
	}

	public function create()
	{
		$handler = new RequestHandler(ServerRequest::fromGlobals());
		$module = new Module([]);
		$handler->handle($module);
		$moduleClass = $module->getModuleClass();
		if (ApplicationModule::exists($moduleClass)) {
			$moduleManager = $this->modelManager(Module::class);
			if (!$module->getName()) {
				$module->setName($moduleClass::getName());
			}
			if (!$module->getDescription()) {
				$module->setDescription($moduleClass::getDescription());
			}

			$moduleManager->insert($module);
			$setupController = $this->get(SetupController::class);
			$setupController->installModule($module->getModuleClass());
			$this->app->addModuleDeclaration($module->getModuleClass());
			$setupController->installModuleActions($module);
			$this->redirect('/modules/');
		} else {
			$this->redirect('/modules/new');
		}
	}

	public function show($id)
	{
		$factory = $this->entityViewFactory( Module::class, $id);
		return $this->render($factory->getView());
	}

	public function edit($id)
	{
		$module = $this->modelManager(Module::class)->findById($id);
		if ($module == false) {
			return $this->render("Module not found");
		} else {
			try {
				$factory = $this->formFactory($module)
					->withAction('/modules/update/' . $module->getId());
				return $this->render($factory->getForm());
			} catch (ReflectionException $e) {
				$this->processError($e->getMessage());
				//$this->app->internalError('Failure in Modules Managment subsystem');
			}
		}
	}

	public function update($id)
	{
		$manager = $this->modelManager(Module::class);
		$module = $manager->findById($id);
		$this->handleRequest($module);
		$manager->enableDebug();
		$this->modelManager(Module::class)->update($module);
		$this->routeTo('modules_show', ['id' => $module->getId()]);
	}

	public function actions(int $moduleId)
	{
		$factory = $this->tableFactory(Action::class);
		$factory->where(['modules_id' => $moduleId]);
		return $this->render($factory->getView($this->app));
	}

	public function newAction(int $moduleId)
	{
		$moduleManager = $this->app->getModelManager(Module::class);
		$module = $moduleManager->findById($moduleId);
		if ($module) {
			$factory = new FormFactory(Action::class);
			$factory->addHiddenInput((new HiddenInput('module_id'))->setValue($moduleId));
			$factory->setFormTitle('Add action for : ' . $module->getName());
			$factory->withAction("/modules/$moduleId/actions/create");
			$form = $factory->getForm($this->app);
			return App::render($form);
		} else {
			return App::render("Can't add action to unknown module with id : " . $moduleId);
		}

	}

	public function createAction($moduleId)
	{
		$handler = new RequestHandler(ServerRequest::fromGlobals());
		$action = new Action();
		$handler->handle($action);
		$actionManager = $this->app->getModelManager(Action::class);
		$actionManager->enableDebug();
		$moduleManager = $this->app->getModelManager(Module::class);
		$module = $moduleManager->findById($moduleId);
		$action->setModule($module);
		$actionManager->insert($action);
		$actionId = $action->getId();
		$this->app->redirectTo("/modules/$moduleId/actions/$actionId");
	}

	public function showAction(int $moduleId, int $actionId)
	{
		$actionManager = $this->app->getModelManager(Action::class);
		$moduleManager = $this->app->getModelManager(Module::class);
		$module = $moduleManager->findById($moduleId);
		if ($module) {
			$factory = new EntityViewFactory($actionManager, $actionId);
			$view = $factory->getView();
			return App::render($view);
		} else {
			return App::render("Can't find action or module");

		}
	}

	public function editAction(int $moduleId, int $actionId)
	{
		return App::render('edit action : ' . $actionId . ' of module : ' . $moduleId);
	}

	public function updateAction(int $moduleId, int $actionId)
	{
		$actionManager = $this->app->getModelManager(Action::class);
		$moduleManager = $this->app->getModelManager(Module::class);
		$module = $moduleManager->findById($moduleId);
		$action = $actionManager->findById($actionId);
		$action->setModule($module);
		$actionManager->update($action);
	}
}