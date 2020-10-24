<?php


namespace App\Module;


interface ModuleInterface
{
    /**
     * @return bool
     */
    public function isMetaModule();

    /**
     * @return string
     */
    public function getScope();

    public function getModuleInfo();

    /**
     * @param $displayType
     * @param string $display
     * @param string $alternateDisplay
     * @param string $path
     * @param string $displayside
     */
    public function setModuleInfo($displayType, string $display, string $alternateDisplay, string $path, string $displayside = "left");

    /**
     * @return bool
     */
    public function hasDependencies(): bool;

    /**
     * @method heckDependencies(array $modulesInstances):bool
     * @param array $modulesInstances
     * @return bool|array : true if all dependencies are already loaded , an
     * array of missing dependencies else
     */
    public function checkDependencies(array $modulesInstances);

    /**
     * @return array
     */
    public function getDependencies(): array;

    public function getSubModuleClassNames():array;

    public function install();
    public function update();
}