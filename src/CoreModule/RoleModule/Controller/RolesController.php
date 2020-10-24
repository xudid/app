<?php

namespace App\CoreModule\RoleModule\Controller;


use App\Controller;
use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\RoleModule\Model\RolesManager;
use App\CoreModule\RoleModule\Views\ActionEditionForm;
use App\CoreModule\RoleModule\Views\ModuleEditionForm;
use Entity\SearchBinder;
use GuzzleHttp\Psr7\ServerRequest;
use ReflectionException;
use Ui\HTML\Elements\Bases\Span;
use Ui\HTML\Elements\Nested\A;
use Ui\Views\DataTableView;
use Ui\Views\EntityView;
use Ui\Views\FormFactory;
use Ui\Views\SearchViewFactory;
use Ui\Widgets\Button\AddButton;
use Ui\Widgets\Icons\MaterialIcon;
use Ui\Widgets\Table\ColumnsFactory;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\TableLegend;

/**
 * Class RolesController
 * @package App\CoreModule\RoleModule
 */
class RolesController extends Controller
{
    private $modelNamespace = 'App\CoreModule\RoleModule\Model\Role';

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
            $factory = new FormFactory(Role::class);
            $factory->setFormTitle('Nouveau role');
            return $factory->getForm($this->app);
        } catch (\ReflectionException $e) {
        }

    }

    /**
     * @param array $params : params to update a user
     */
    public function edit($id)
    {
        $manager = $this->app->getModelManager(Role::class);
        $role = $manager->findById($id);
        if ($role == false) {
            return $this->showError("Role not found");
        }

        try {
            $factory = new FormFactory($role);
            $factory->withAction('/roles/update');
            $factory->setFormTitle("Edition role ");
            return $factory->getForm($this->app);
        } catch (ReflectionException $e) {
            $this->app->internalError('Error in : ' . __CLASS__ . ', ' . __METHOD__);
        }
    }

    /**
     * @param array $params : params to show a user
     */
    public function show($params)
    {
        $id = $params[0];
        $viewFactory = $this->entityViewFactory(Role::class, $id);
        $viewFactory->basic();
        $manager = $this->modelManager(Role::class);
        $builder = $manager->builder();
        $request = $builder->select('modules.*')
            ->from('modules')
            ->join('roles_modules', 'modules.id', 'modules_id')
            ->where('roles_id', '=', $id)
            ->where('authorized', '=', 1);
        $results = $builder->execute($request);
        $url = $this->router->generateUrl('roles_modules',['id' => $id], 'GET' );
        $icon = new MaterialIcon('build');
        $icon->color('white')->size('xs');
        $span = new Span('Modules');
        $span->setAttribute('style', 'vertical-align:bottom;');
        $legendA = (new A($icon . ' ' . $span, $url))->setClass('btn btn-xs btn-success mb-1');
        $table = new DivTable(
            [new TableLegend($legendA)],
            ColumnsFactory::make(Module::class),
            $results ?: [],
            false,
            '/modules'

        );
        //$viewFactory->filter(Module::class)->where('authorized', '=', 1)->
        $viewFactory->addAssociationView($table);
        $viewFactory->setCurrentPath("/roles/:id");
        $ev = $viewFactory->getView();
        return $ev;
    }

    /**
     * Create a new role
     *only display the sentence  :
     *"creating user please wait"
     */
    public function create()
    {
        $role = new Role([]);
        $role->setName($_POST['role_name']);
        $role->setDescription($_POST['role_description']);
        $manager = $this->app->getModelManager(Role::class);
        $manager->insert($role);
        $this->app->redirectTo('/roles');
    }

    /**
     * @param array $params : params to delete a user
     */
    public function delete($params)
    {
        $id = $params[0];
        $manager = $this->app->getModelManager(Role::class);
        $manager->delete($id);
        $this->app->redirectTo('/roles/');
    }

    /**
     * Update user with
     * @param array $params parametes to update user
     */
    public function update($params)
    {
        $id = $params[0];

        $manager = $this->app->getModelManager(Role::class);
        $role = $manager->findById($id);
        if ($role == false) {
            return $this->showError("Role not found");
        }
        $text = "Are you sure to update this $this->modelNamespace with id :$id";
        $role->setName($_POST['role_name']);
        $role->setDescription($_POST['role_description']);
        $manager->update($role);
        return $text;
    }


    /**
     * Search Users
     */
    public function search()
    {

        $view = new EntityView();
        $searchFactory = new SearchViewFactory(Role::class);
        $searchFactory->withAction('search');
        $searchFactory->setTitle("Search Role");
        $searchView = $searchFactory->getView($this->app);

        try {
            $searchBinder = new SearchBinder(ServerRequest::fromGlobals());
            $params = $searchBinder->bind(Role::class);
            $dtv = (new DataTableView(Role::class, $this->app->getModelManager(Role::class)))
                ->withBaseUrl("/roles");
            $dtv->where($params);

        } catch (ReflectionException $e) {
            $this->app->internalError('Error in : ' . __CLASS__ . ', ' . __METHOD__);

        } catch (\Exception $e) {
            $this->app->internalError('Error in : ' . __CLASS__ . ', ' . __METHOD__);
        }

        $dtv->setTitle("<h2>Resultat de la recherche</h2>");
        return $view->feed($searchView, $dtv->getView($this->app));
    }


    /**
     * Return a view with the user list
     */
    public function index()
    {
        try {
            $dtv = ($this->tableFactory(Role::class))->withBaseUrl("/roles");
            $dtv->setRouter($this->router);
            $legendTitle = new TableLegend("<h2>Liste des roles</h2>", TableLegend::TOP_LEFT);
            $addButton = new AddButton();
            $addButton->size('xs')->setOnClick("location.href='/roles/new'");
            $legendButton = new TableLegend($addButton, TableLegend::TOP_RIGHT);
            $dtv->addALegend($legendTitle);
            $dtv->addALegend($legendButton);
            return $dtv->getView($this->app);
        } catch (ReflectionException $e) {
            $this->app->internalError('Error in : ' . __CLASS__ . ', ' . __METHOD__);

        }

    }

    public function editModules($id)
    {
        $form = new ModuleEditionForm($this->router, $this->modelManager(Role::class), $this->modelManager(Module::class), $id);
        return $this->render($form);
    }

    public function addModules($roleId)
    {
        $module = new Module();
        $this->requestHandler->handle($module, 'modules');
        $roleManager = $this->modelManager(Role::class, RolesManager::class);
        $role = $roleManager->findById($roleId);
        $roleManager->addModule($role, $module);
        $this->routeTo('roles_modules', ['id' => $roleId]);
    }

    public function deleteModules($roles_id, $modules_id)
    {
        $roleManager = $this->modelManager(Role::class, RolesManager::class);
        $roleManager->deleteModule($roles_id, $modules_id);
        $this->routeTo('roles_modules', ['id' => $roles_id]);
    }

    public function editActions($roleId, $moduleId)
    {
        $form = new ActionEditionForm($this->router, $this->modelManager(Role::class), $roleId, $moduleId);
        return $this->render($form);
    }

    public function addActions($roleId, $moduleId)
    {
        $action = new Action();
        $this->requestHandler->handle($action, 'actions');
        $roleManager = $this->modelManager(Role::class, RolesManager::class);
        $role = $roleManager->findById($roleId);
        $roleManager->addAction($role, $action);
        $this->routeTo('roles_actions', ['role_id' => $roleId, 'module_id' => $moduleId]);
    }

    public function deleteActions($roleId, $moduleId, $actionId)
    {
        $roleManager = $this->modelManager(Role::class, RolesManager::class);
        $roleManager->deleteModule($roleId, $moduleId);
        $this->routeTo('roles_modules', ['id' => $roleId]);
    }


    /**
     * Return an Error message
     * @param string $message the error message to display
     */
    public function showError($message)
    {
        return $message;
    }
}
