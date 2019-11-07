<?php
namespace App;

use App\Module\Module;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;


/**
 * Class App
 * @package App
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class App
{
    private array $modules = [];
    private array $modulesInstances = [];
    private ContainerInterface $container;
    private $router;
    private $renderer;
    private $firewall;

  /**
     * App constructor.
     * @param ContainerInterface $container
     * @param array $modules
     */
    function __construct(ContainerInterface $container, array $modules)
    {
        if ($container != null && $container instanceof ContainerInterface) {
            $this->container = $container;
            $this->router = $container->get('router');
            $this->renderer = $container->get('renderer');
            $this->firewall = $container->get('firewall');
            $this->modules =$modules??[];
            foreach ($this->modules as $module) {
              if ($module instanceof Module) {
                $m = $this->loadModule($module);
                $this->loadModuleInfo($m);
                if ($m->hasDependencies()) {
                  $this->checkDependencies($m);
                }
                $ismeta = $m->isMetaModule();
                if ($ismeta) {
                  $submodulesclasses = $m->getSubModules();
                  foreach ($submodulesclasses as $value) {
                    $m = $this->loadModule($value);
                    $this->loadModuleInfo($m);
                    $this->modulesInstances[$value] = $m;
                  }

                } else {
                  $this->modulesInstances[$module] = $m;
                }
              }
            }
          return $this;
        } else {
          throw new InvalidArgumentException();
        }
    }

    private function loadModule($classname)
    {
      try {
        $r = new ReflectionClass($classname);
        $m = $r->newInstance($this->container);
        return $m;
      } catch (ReflectionException $e) {
      }

    }
     //todo populate the navbar in AppPage
    private function loadModuleInfo($module)
    {
        $infos = $module->getModuleInfo();
        if (!is_null($infos)) {
            $this->renderer->addNavBarItem($infos->getNavBarDisplayType(),
                $infos->getPath(),
                $infos->getNavBarDisplay(),
                $infos->getAlternateDisplay(),
                $infos->getDisplaySide()
            );
        }
    }
//Todo move this function in Module class
    private function checkDependencies($module)
    {
        $dependencies = $module->getDependencies();
        foreach ($dependencies as $dependence) {
            if (!\array_key_exists($dependence, $this->modulesInstances)) {
                echo "<br>" . "$dependence is not loaded yet please load it first <br>";
            }
        }
    }
}

?>
