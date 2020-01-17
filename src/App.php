<?php

namespace App;

use App\Pipeline\Pipeline;
use App\Module\Module;
use App\Module\ModuleInterface;
use DI\ContainerBuilder;
use Entity\Database\Dao;
use Entity\Database\Mysql\MysqlDataSource;
use Entity\EntityFactory;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Middleware\ControllerDispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use ReflectionClass;
use ReflectionException;
use Ui\HTML\Elements\Bases\h1;
use Ui\Views\Page;
use function Http\Response\send;


/**
 * Class App
 * Container must define keys :app_name , "app_name"_modules
 * @package App
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class App
{
    private array $moduleClassNames = [];
    private array $modulesInstances = [];
    private array $modulesInfos = [];
    private array $errors = [];
    private ContainerInterface $container;
    private string $appName;
    private array $containerDefinitions = [];
    private Pipeline $pipeline;
    private string $tempDir;
    private string $appConfigDir;

    /**
     * App constructor.
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */

    function __construct()
    {
        $config = require_once('../config/config.php');
        $this->appName = $config['app_name'];
        $this->appConfigDir = $config['config_dir'];
        $this->tempDir = $config['temp_dir'];
        $this->containerBuilder = new ContainerBuilder();
        $this->moduleClassNames = $config['modules'] ?? [];

        foreach ($this->moduleClassNames as $moduleClassName) {
            if (is_string($moduleClassName)) {
                //Loading module
                $this->loadModule($moduleClassName);
            }
        }
        $controllerDispatcher = new ControllerDispatcher($this);
        $this->addContainerDefinition('controller_dispatcher', $controllerDispatcher);
        $this->containerBuilder->addDefinitions($this->containerDefinitions);
        $this->container = $this->containerBuilder->build();

        // create middleware pipeline
        $this->pipeline = new Pipeline();
        $pipes = $config['pipeline'];
        foreach ($pipes as $pipe) {
            $pipe = $this->container->get($pipe);
            if ($pipe instanceof MiddlewareInterface) {
                $this->pipeline->pipe($pipe);
            }
        }

        // init request and store it in container

        return $this;
        //We don't have a ContainerInterFace  as __construct param throw an Exception
    }

    /**
     * @return array
     */
    public function getModuleConfiguration(string $moduleName)
    {
        $configFileName = $this->appConfigDir . '/modules/' . $moduleName . '.php';
        return file_exists($configFileName) ? require_once $configFileName : [];
    }

    private function loadModule($moduleClassName)
    {
        try {
            $moduleReflectionClass = null;
            try {
                $moduleReflectionClass = new ReflectionClass($moduleClassName);
                $module = $moduleReflectionClass->newInstance($this);
                //Loading module info
                if (($module != null) && ($module instanceof ModuleInterface)) {

                    $this->loadModuleInfo($module);
                    //Loading module dependencies

                    if ($module->hasDependencies() && !($missingDependencies = $module->checkDependencies($this->modulesInstances))) {
                        foreach ($missingDependencies as $missingDependency) {
                            $this->loadModule($missingDependency);
                        }
                    }
                    //Loading submodules for MetaModules
                    if ($module->isMetaModule()) {
                        $subModuleClassNames = $module->getSubModuleClassNames();

                        foreach ($subModuleClassNames as $subModuleClassName) {

                            if ($moduleClassName != $subModuleClassName) {
                                $subModule = $this->loadModule($subModuleClassName);
                                if ($subModule && $subModule instanceof Module) {
                                    $this->loadModuleInfo($subModule);
                                    $this->modulesInstances[$subModuleClassName] = $subModule;
                                }

                            } else {
                                throw new Exception("Circular reference was detected in submodule referencies");
                            }
                        }
                    }
                    $this->modulesInstances[$moduleClassName] = $module;
                    return $module;
                }
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }

        } catch (ReflectionException $e) {
            $this->errors[] = "Module $moduleClassName not Found";
        }
    }

    //todo populate the navbar in AppPage

    private function loadModuleInfo($module)
    {
        $infos = $module->getModuleInfo();
        if (!is_null($infos)) {
            $this->modulesInfos[$module->getScope()] = $infos;
            /*$this->renderer->addNavBarItem($infos->getNavBarDisplayType(),
                $infos->getPath(),
                $infos->getNavBarDisplay(),
                $infos->getAlternateDisplay(),
                $infos->getDisplaySide()
            );*/
        }
    }
    /**
     * @return array|mixed
     */
    public function getModuleClassNames()
    {
        return $this->moduleClassNames;
    }

    /**
     * @return array
     */
    public function getModulesInstances(): array
    {
        return $this->modulesInstances;
    }

    /**
     * @return array
     */
    public function getModulesInfos(): array
    {
        return $this->modulesInfos;
    }

    /**
     * @return mixed|string|app_name
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addContainerDefinition($key, $definition)
    {
        $this->containerDefinitions[$key] = $definition;
        return $this;
    }

    public function addRoute()
    {

    }


    public function addFirtewallRule()
    {

    }

    public function run()
    {
        $response = new Response();
        $request = ServerRequest::fromGlobals();
        $response = $this->pipeline->process($request,$response);

        if($response)
        {
            $statuscode = $response->getStatusCode();
            switch ($statuscode) {
                case 200:
                    send($response);
                    break;

                case 403:
                    /*$view = $renderer->render("403.html",null,'../vendor/Brick/src/ErrorPage');
                    $response->getBody()->write($view);
                    send($response);*/
                    break;

                case 404:

                    /*$view = $renderer->render("404.html",null,"../vendor/Brick/src/ErrorPage");

                    $response->getBody()->rewind();

                    $response->getBody()->write($view);*/



                    send($response);
                    break;

                default:
                    send($response);
                    break;
            }

        }
    }

    public function get(string $key)
    {
        if(array_key_exists($key, $this->containerDefinitions)) {
            return $this->containerDefinitions[$key];
        } elseif (!is_null($this->container) && $this->container->has($key)) {
            return $this->container->get($key);
        } else {
            return null;
        }
    }

    public function render($view)
    {
        $page = new Page();
        $page->importCss(
            '/css/ui.css',
            "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        );
        $page->feedBody((new H1('Application setup'))->setClass('ml-4'), $view);
        return $page;
    }

    /**
     * @return mixed
     */
    public function getAppConfigDir()
    {
        return $this->appConfigDir;
    }

    /**
     * @return mixed
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    public function redirectTo(string $url)
    {
        send(new Response(302,['Location' => $url]));
    }

    public function getEntity(string $class)
    {
        $entity = null;
        $dataSource = new MysqlDataSource('mysql', [
            'mysql.server' => 'localhost',
            'mysql.port' => '3306',
            'mysql.database' => 'brickdb',
            'mysql.user' => 'root',
            'mysql.password' => '',
            'mysql.attributes' => [],

        ]);
        $dao = new Dao($dataSource,$class);
        $entityFactory = new EntityFactory($dao);
        try {
            $entity = $entityFactory->getEntity($class);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            // logging something if in debug mode
            // else return http 500 response
        }
        return $entity;
    }


}

