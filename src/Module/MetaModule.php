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
   * @var bool $isMetamodule
   */
  protected bool $isMetamodule = true;

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
  }

  /**
   * @method getSubModules()
   * @return array
   */
  public function getSubModules():array
  {
    return $this->submodulesclasses;
  }
}
