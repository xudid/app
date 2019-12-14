<?php
namespace App\Module;


/**
 * Class ModuleInfo
 * @package App\Module
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class ModuleInfo
{
    /**
     * @var string $navBarDisplayType
     */
    private string $navBarDisplayType = "text";

    /**
     * @var string $navBarDisplay
     */
    private string $navBarDisplay = "Home";

    /**
     * @var string $alternateDisplay
     */
    private string $alternateDisplay = "";

    /**
     * @var string $path
     */
    private string $path = "/";

    /**
     * @var string $displayside
     */
    private string $displayside = "left";

    /**
     * ModuleInfo constructor.
     * @param string $displayType text or icon
     * @param string $display
     * @param string $alternateDisplay
     * @param string $path
     */
    function __construct(string $displayType,
                         string $display,
                         string $alternateDisplay,
                         string $path
    )
    {
        $this->navBarDisplayType = $displayType;
        $this->navBarDisplay = $display;
        $this->alternateDisplay = $alternateDisplay;
        $this->path = $path;
    }

    /**
     * @return string getNavBarDisplayType()
     */
    public function getNavBarDisplayType(): string
    {
        return $this->navBarDisplayType;
    }

    /**
     * @method getNavBarDisplay()
     * @return string
     */
    public function getNavBarDisplay(): string
    {
        return $this->navBarDisplay;
    }

    /**
     * @method getAlternateDisplay()
     * @return string
     */
    public function getAlternateDisplay(): string
    {
        return $this->alternateDisplay;
    }

    /**
     * @method getPath()
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @method setDisplaySide
     * @param string $displayside
     */
    public function setDisplaySide(string $displayside)
    {
        $this->displayside = $displayside;
    }

    /**
     * @method getDisplaySide()
     * @return string
     */
    public function getDisplaySide(): string
    {
        return $this->displayside;
    }
}

