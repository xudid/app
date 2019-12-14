<?php namespace App\Module;

use Psr\Container\ContainerInterface;

/**
 * Class MetaModule
 * @package Brick\Module
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class MetaModule extends Module
{

  /**
   * @
   * @var array $submodulesclasses
   */
  protected array $submodulesclasses = [];

  /**
   * MetaModule constructor.
   * @param ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->container = $container;
    $this->isMetamodule = true;
  }

  /**
   * @method getSubModuleClassNames()
   * @return array
   */
  public function getSubModuleClassNames():array
  {
    return $this->submodulesclasses;
  }
}
