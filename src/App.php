<?php

namespace App;

use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use App\Module\Module;
use App\Pipeline\Pipeline;
use Core\Contracts\ManagerInterface;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Core\Contracts\DaoInterface;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Renderer\Renderer;
use function Http\Response\send;

/**
 * Class App
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class App
{
    public static $configDirectory;
    private static string $appPageClass;
    private array $moduleClassNames = [];
    private array $modulesInstances = [];
    private array $modulesInfos = [];
    private array $errors = [];
    private static ContainerInterface $container;
    private string $appName;
    private Pipeline $pipeline;
    private static string $root;
    private static string $temp;
    public static string $modules;
    private static $config;
    private static $instance;
    /**
     * @var ContainerBuilder
     */
    private ContainerBuilder $containerBuilder;
    private string $rootDir;

    /**
     * App constructor.
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws Exception
     */

    private function __construct()
    {
        // make convention on definitions path
        if (!file_exists('../config/config.php')) {
            throw new Exception('No config file');
        }
        self::$instance = $this;
        self::$config = require_once('../config/config.php');
        self::$root = self::$config['root_dir'];
        self::$temp = self::$config['temp_dir'];
        self::$modules = self::$config['site_modules'];
        $this->appName = self::$config['app_name'];
        $this->appConfigDir = self::$config['config_dir'];
        self::$configDirectory = self::$config['config_dir'];
        $this->containerBuilder = new ContainerBuilder();
        $this->moduleClassNames = array_merge(
            self::$config['core_modules'] ?? [],
            self::$config['app_modules'] ?? []
        );
        spl_autoload_register(function ($class) {
            $fileClassName = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $fileName = dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $fileClassName . '.php';
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        });
        $this->containerBuilder->addDefinitions(require self::$config['config_dir'] . DIRECTORY_SEPARATOR . 'di.php');
        foreach ($this->moduleClassNames as $moduleClassName) {
            if (is_string($moduleClassName)) {
                $this->loadContainerDefinitions($moduleClassName);
            }
        }

        try {
            self::$container = $this->containerBuilder->build();
            foreach ($this->moduleClassNames as $moduleClassName) {
                if (is_string($moduleClassName)) {
                    $this->loadRoutes($moduleClassName);
                }
            }
            /*foreach ($this->moduleClassNames as $moduleClassName) {
                if (is_string($moduleClassName)) {
                    $this->installModule($moduleClassName);
                }
            }*/
            $authorizationcontroller = self::$container->get(AuthorizationController::class);
            $modules = $authorizationcontroller->getAuthorizedModules();

            // create middleware pipeline
            $this->pipeline = self::$container->get(Pipeline::class);
            $pipes = self::$config['pipeline'];
            foreach ($pipes as $pipe) {
                $pipe = self::$container->get($pipe);
                if ($pipe instanceof MiddlewareInterface) {
                    $this->pipeline->pipe($pipe);
                }
            }
        } catch (Exception $e) {
            dump($e);
        }

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    public static function __callStatic($name, $args)
    {
        if ($name == 'container') {
            return self::$container;
        }
        if (self::$container->has($name)) {
            try {
                return self::$container->get($name);
            } catch (DependencyException $e) {
                dump(__CLASS__ . __METHOD__);
            } catch (NotFoundException $e) {
                dump(__CLASS__ . __METHOD__);

            }
        }

    }

    public static function autorize(string $moduleClass, array $types = [])
    {
        // un utilisateur est autorisé a faire 0 a N actions appartenant a un module
        // Table User Table des modules Tables actions Table des actions
        // table Module primary key id  Module namespace
        // table Action id pk module_id fk name
        // table Autorization id User id Action fk

        $actions = AuthorizationController::authorizedAction($moduleClass);
        $autorizations = [];
        foreach ($actions as $action) {
            if ((count($types) == 0)) {
                $autorizations[$action['type']] = $action['route_name'];
            } elseif (in_array($action['type'], $types)) {
                $autorizations[$action['type']] = $action['route_name'];
            }

        }
        return $autorizations;

    }

    /**
     * @return array
     */
    public function getModuleConfiguration(string $moduleName)
    {
        $configFileName = self::$config . '/modules/' . $moduleName . '.php';
        return file_exists($configFileName) ? require_once $configFileName : [];
    }

    /*private function loadModule($moduleClassName)
    {

        try {
            $moduleReflectionClass = null;
            try {
                    if (class_exists($moduleClassName) && is_subclass_of($moduleClassName, Module::class)) {
                        $this->containerBuilder->addDefinitions($moduleClassName::getDefinitions());
                        foreach ($moduleClassName::getRoutes() as $route) {
                            dump($route);
                        }
                    }
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
    }*/

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

    public function run()
    {
        $response = new Response();
        $request = ServerRequest::fromGlobals();
        $response = $this->pipeline->process($request, $response);
        if ($response) {
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

                    //$view = $renderer->render("404.html",null,"../vendor/Brick/src/ErrorPage");

                    $response->getBody()->rewind();

                    $response->getBody()->write('404 Not Found');


                    send($response);
                    break;

                default:
                    send($response);
                    break;
            }

        }
    }

    public static function get(string $key)
    {
        if (self::$container && self::$container->has($key)) {
            return self::$container->get($key);
        } else {
            return null;
        }
    }

    public static function setAppPageClass($class)
    {
       static::$appPageClass = $class;
    }

    public static function render($view)
    {
        $renderer = self::get(Renderer::class);
        $appPage = new static::$appPageClass;
        $appPage->importCss(...static::get('css'));
        $appPage->importScript(...static::get('js'));
        return $renderer->setAppPage($appPage)
            ->renderAppPage($view);
    }

    /**
     * @return mixed
     */
    public static function getConfigDir()
    {
        return self::$config['config_dir'];
    }

    public static function setCongigDir(string $dir)
    {
        if (file_exists($dir)) {
            self::$config['config_dir'] = $dir;
        }
    }

    public static function tempDir()
    {
        return self::$temp;
    }

    public static function send($content)
    {
        $response = new Response();
        $response->getBody()->rewind();

        $response->getBody()->write((string)$content);
        send($response);
        exit();
    }

    public function redirectTo(string $url)
    {
        send((new Response())->withStatus(302)->withHeader('Location', $url));
        exit();
    }

    public static function redirectToRoute(string $routeName, array $params = [])
    {
        $router = self::get('router');
        $url = $router->generateUrl($routeName, $params);
        send((new Response())->withStatus(302)->withHeader('Location', $url));
        exit();
    }

    public function modelManager(string $classNamespace, string $managerInterfaceName = ''): ?ManagerInterface
    {
        try {
            $factory = self::get('model_manager_factory');
            if (class_exists($managerInterfaceName)) {
                $factory->setManagerInterface($managerInterfaceName);

                return $factory->getManager($classNamespace);
            } else {
                return $factory->getManager($classNamespace);
            }

        } catch (Exception $e) {
            App::render('Failed to create model manager for : ' . $classNamespace);
        }
        return null;
    }

    /**
     * @param string $class
     * @return object|null
     * @throws Exception
     */

    public function internalError(string $message)
    {
        dump($message);
    }

    public function showInfo(string $message)
    {
        // Todo return a 200 response displaying info $message
    }

    private function loadContainerDefinitions($className)
    {
        if (class_exists($className) && is_subclass_of($className, Module::class)) {
            $dir =$className::getDir();
            if (file_exists($dir)) {
                $diFileName = $dir . DIRECTORY_SEPARATOR . 'di.php';
                if (file_exists($diFileName)) {
                    $definitions = require $diFileName;
                    $this->containerBuilder->addDefinitions($definitions);
                }
            }
        }
    }

    private function loadRoutes(string $moduleClassName)
    {
        $router = self::$container->get('router');
        $router->get('/','default', function(){
            return self::render('Root page');
        });
        $router->get('/home','home', function(){
            return self::render('Home sweet home');
        });
        if (class_exists($moduleClassName) && is_subclass_of($moduleClassName, Module::class)) {
            $dir =$moduleClassName::getDir();
            if (file_exists($dir)) {
                $routesFileName = $dir . DIRECTORY_SEPARATOR . 'routes.php';
                if (file_exists($routesFileName)) {
                    $routes = require $routesFileName;
                    $routes = is_array($routes) ? $routes : [];
                    foreach ($routes as $route) {
                        $method = $route['method'] ?: '';
                        if ($router->authorize($method)) {
                            $routes[$method][$route['name']] = Route::hydrate($route);
                        }
                    };
                    $router->setRoutes($routes);
                }
            }

        }
    }

    private function installModule(string $moduleClassName)
    {
        if (class_exists($moduleClassName) && is_subclass_of($moduleClassName, Module::class)) {
            $dir = $moduleClassName::getDir();
            if (file_exists($dir)) {
                $migrationsDir = $dir . DIRECTORY_SEPARATOR . 'migrations';
                if (file_exists($migrationsDir)) {
                    try {
                        $dao = $this->get(DaoInterface::class);
                        $moduleClassName::install($dao, 'development');
                    } catch (Exception $e) {
                    }
                }
            }
        }
    }
}
