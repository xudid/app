<?php

namespace App;

use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use App\Module\Module;
use App\Pipeline\Pipeline;
use Core\Contracts\DaoInterface;
use Core\Contracts\ManagerInterface;
use Core\Http\Http;
use Core\Session\Session;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Renderer\Renderer;

/**
 * Class App
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class App
{
    public static $configDirectory;
    private static string $appPageClass;
    private array $moduleClassNames = [];
    private array $errors = [];
    private array $infos = [];
    private static ContainerInterface $container;
    private string $appName;
    private Pipeline $pipeline;
    private static string $root;
    private static string $temp;
    private static string $cache;
    public static array $modules;

    private static array $config;
    private static array $databaseConfiguration;
    private static array $routes = [];
    private static $instance;
    private ContainerBuilder $containerBuilder;

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
        self::$cache = self::$config['cache_dir'];
        self::$modules = require_once(self::$config['site_modules']);
        $this->appName = self::$config['app_name'];
        // Gestion des loaders pour le cache du code généré
        //		Autoloader::register(self::$cache);
        $this->appConfigDir = self::$config['config_dir'];
        self::$configDirectory = self::$config['config_dir'];
        $this->containerBuilder = new ContainerBuilder();
        $this->moduleClassNames = array_merge(
            self::getCoreModules(),
            self::getApplicationModules()
        );

        if (!file_exists('../config/di.php')) {
            throw new Exception('No injection dependency definitions file');
        }
        $injenctionDependencyDefinitions = require_once('../config/di.php');
        $injenctionDependencyDefinitions['request'] = ServerRequest::fromGlobals();
        $this->containerBuilder->addDefinitions($injenctionDependencyDefinitions);
        foreach ($this->moduleClassNames as $moduleClassName) {
            if (is_string($moduleClassName)) {
                $this->loadContainerDefinitions($moduleClassName);
            }
        }

        $this->logger = $this->get(LoggerInterface::class);
        try {
            self::$container = $this->containerBuilder->build();
            $this->loadRoutes(self::getApplicationRoutes());

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
            // do a LoggerFacade
            $this->log($e->getMessage(), 'critical');
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
            } catch (DependencyException|NotFoundException $e) {
                echo '<pre>' . var_dump(__CLASS__ . __METHOD__);
            }
        }

    }

    /**
     * @param string $moduleClass
     * @param array $types
     * @return array
     */
    public static function autorize(string $moduleClass, array $types = []): array
    {
        // un utilisateur est autorisé a faire 0 a N actions appartenant a un module
        // Table User Table des modules Tables actions Table des actions
        // table Module primary key id  Module namespace
        // table Action id pk module_id fk name
        // table Autorization id User id Action fk
        $actions = AuthorizationController::authorizedAction($moduleClass);
        $moduleClass = explode('\\', $moduleClass);

        $moduleClass = $moduleClass[array_key_last($moduleClass)];
        $moduleClass = str_replace('Module', '', $moduleClass) . 's';
        $expectedRoutes = [];
        foreach ($types as $type) {
            if ($type == 'LIST') {
                $expectedRoutes[] = strtolower($moduleClass);
            } else {
                $expectedRoutes[] = strtolower($moduleClass . '_' . $type);
            }
        }
        $actionsKeys = array_column($actions, 'route_name');
        $actions = array_combine($actionsKeys, array_values($actions));
        $autorizations = [];
        foreach ($expectedRoutes as $expectedRoute) {
            if (array_key_exists($expectedRoute, $actions)) {
                $action = $actions[$expectedRoute];
                if ((count($types) == 0)) {
                    $autorizations[$action['type']] = $action['route_name'];
                } elseif (in_array($action['type'], $types)) {
                    $autorizations[$action['type']] = $action['route_name'];
                }
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
     * @return mixed
     */
    public static function getEnvironment()
    {
        return self::$config['environment'];
    }

    /**
     * @return array
     */
    public static function getApplicationRoutes(): array
    {
        return self::$routes;
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
        $response = $this->pipeline->process(App::get('request'), $response);
        if ($response) {
            $statuscode = $response->getStatusCode();
            switch ($statuscode) {
                case 403:
                    //if AuthModule loaded render login
                    // else render 403.html
                    /*$view = $renderer->render("403.html",null,'../vendor/Brick/src/ErrorPage');
                    $response->getBody()->write($view);
                    send($response);*/
                    break;

                case 404:
                    // Todo render 404.html
                    $response->getBody()->rewind();
                    $response->getBody()->write('404 Not Found');
                    self::send($response);
                    break;

                default:
                    Http::send($response);
                    break;
            }

        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function get(string $key)
    {
        if (self::$container && self::$container->has($key)) {
            return self::$container->get($key);
        } elseif (array_key_exists($key, self::$config)) {
            return self::$config[$key];
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

        $messages = Session::get('messages') ?? [];
        if ($messages) {
            if (array_key_exists('infos', $messages)) {
                $appPage->setInfos($messages['infos']);
                unset($messages['infos']);
                Session::set('messages', $messages);
            }
            if (array_key_exists('errors', $messages)) {
                $appPage->setErrors($messages['errors']);
                unset($messages['errors']);
                Session::set('messages', $messages);
            }
        }
        $renderer->setAppPage($appPage);
        return $renderer->renderAppPage($view);
    }

    /**
     * @return mixed
     */
    public static function getConfigDir()
    {
        return self::$config['config_dir'];
    }

    public static function setConfigDir(string $dir)
    {
        if (file_exists($dir)) {
            self::$config['config_dir'] = $dir;
        }
    }

    public static function tempDir()
    {
        return self::$temp;
    }

    public function getModuleDeclarations()
    {
        $filename = self::getConfigDir() . DIRECTORY_SEPARATOR . 'modules.php';
        if (file_exists($filename)) {
            $modules = require $filename;
        }
        return $modules ?? [];
    }

    public function addModuleDeclaration($module)
    {
        $existingModules = $this->getModuleDeclarations();
        $modules = array_merge($existingModules, [$module]);
        $filename = self::getConfigDir() . DIRECTORY_SEPARATOR . 'modules.php';
        $fileContent = '<?php' . PHP_EOL . 'return [';
        foreach ($modules as $module) {
            $fileContent .= "'$module'" . PHP_EOL;
        }
        $fileContent .= '];';

        file_put_contents($filename, $fileContent);

    }

    public function log(string $message, $priority, array $context = [])
    {
        if ($this->isInDevelopment()) {
            $this->logger->debug($message, $context);
        } else {
            switch ($priority) {
                /**
                 * System is unusable.
                 */
                case 'emergency':
                    $this->logger->emergency($message, $context);
                    break;
                /**
                 * Action must be taken immediately.
                 * Example: Entire website down, database unavailable, etc. This should
                 * trigger the SMS alerts and wake you up.
                 */
                case 'alert':
                    $this->logger->alert($message, $context);
                    break;

                /**
                 * Critical conditions.
                 * Example: Application component unavailable, unexpected exception.
                 */
                case 'critical':
                    $this->logger->critical($message, $context);
                    break;

                /**
                 * Runtime errors that do not require immediate action but should typically
                 * be logged and monitored.
                 */
                case 'error':
                    $this->logger->error($message, $context);
                    break;

                /**
                 * Exceptional occurrences that are not errors.
                 * Example: Use of deprecated APIs, poor use of an API, undesirable things
                 * that are not necessarily wrong.
                 */
                case 'warning':
                    $this->logger->warning($message, $context);
                    break;

                /**
                 * Normal but significant events.
                 */
                case 'notice':
                    $this->logger->notice($message);
                    break;

                /**
                 * Interesting events.
                 * Example: User logs in, SQL logs.
                 */
                case 'info':

                    $this->logger->info($message, $context);
                    break;
            }
        }
    }

    protected function isInDevelopment()
    {
        $environment = $this->getEnvironment();
        return $environment == 'development';
    }

    public static function send($content)
    {
        $response = new Response();
        $response->getBody()->rewind();

        $response->getBody()->write((string)$content);
        Http::send($response);
        exit();
    }

    public function redirectTo(string $url)
    {
        Http::send((new Response())->withStatus(302)->withHeader('Location', $url));
        exit();
    }

    public static function redirectToRoute(string $routeName, array $params = [])
    {
        $router = self::get('router');
        $url = $router->generateUrl($routeName, $params);
        Http::send((new Response())->withStatus(302)->withHeader('Location', $url));
        exit();
    }

    public function modelManager(string $classNamespace, string $managerInterfaceName = ''): ?ManagerInterface
    {
        try {
            if (!class_exists($classNamespace)) {
                    return null;
            }

            $factory = self::get(ManagerFactory::class);
            $factory->setProxyCachePath(self::$cache);

            if (class_exists($managerInterfaceName))  {
                $factory->setManagerInterface($managerInterfaceName);
                return $factory->getManager($classNamespace);
            }

            $factory->setManagerInterface(ManagerInterface::class);
            return $factory->getManager($classNamespace);

        } catch (Exception $e) {
            App::render('Failed to create model manager for : ' . $classNamespace);
            return null;
        }
    }

    /**
     * @param string $class
     * @return object|null
     * @throws Exception
     * @deprecated
     */

    public function showError(string $message)
    {
        $this->errors[] = $message;
    }

    /**
     * @param string $message
     * @deprecated
     */
    public function showInfo(string $message)
    {
        $this->infos[] = $message;
    }

    private function loadContainerDefinitions($className)
    {
        if (!class_exists($className) || !is_subclass_of($className, Module::class)) {
            return;
        }

        $dir = $className::getDir();
        $diFileName = $dir . DIRECTORY_SEPARATOR . 'di.php';
        if (!file_exists($diFileName)) {
            return;
        }

        $definitions = require $diFileName;
        $this->containerBuilder->addDefinitions($definitions);
    }

    private function loadRoutes()
    {
        $routes = [];
        $router = static::get('router');
        $routesFileNames = [static::$configDirectory . DIRECTORY_SEPARATOR . 'routes.php'];
        foreach ($this->moduleClassNames as $moduleClassName) {
            if (is_string($moduleClassName)) {
                $routesFileNames[] = $this->getModuleRouteFileNames($moduleClassName);
            }
        }
        foreach ($routesFileNames as $routesFileName) {
            $routeDatas = require $routesFileName;
            foreach ($routeDatas as $routeData) {
                $method = $routeData['method'] ?: '';

                if ($router->authorize($method)) {
                    $router->addRoute($routeData['method'], $routeData['path'], $routeData['name'], $routeData['callback']);
                }
            }
        }
    }

    private function getModuleRouteFileNames(string $moduleClassName)
    {
        if (!class_exists($moduleClassName) || !is_subclass_of($moduleClassName, Module::class)) {
            return '';
        }

        $dir = $moduleClassName::getDir();
        $routesFileName = $dir . DIRECTORY_SEPARATOR . 'routes.php';
        if (!file_exists($routesFileName)) {
            return '';
        }

        return $routesFileName;
    }

    public static function getCoreModules()
    {
        return self::$config['core_modules'];
    }

    public function installModule($moduleClassName)
    {
        if (!class_exists($moduleClassName) || !is_subclass_of($moduleClassName, Module::class)) {
            return;
        }
        $dir = $moduleClassName::getDir();
        $migrationsDir = $dir . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($migrationsDir)) {
            return;
        }

        try {
            $dao = $this->get(DaoInterface::class);
            $moduleClassName::install($dao, 'development');
        } catch (Exception $e) {
        }
    }

    public static function getApplicationModules()
    {
        return self::$modules ?? [];
    }
}
