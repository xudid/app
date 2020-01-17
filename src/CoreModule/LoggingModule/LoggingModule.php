<?php

namespace App\CoreModule\LoggingModule;

use App\App;
use App\Module\Module;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class LoggingModule
 * @package App\CoreModule\LoggingModule
 */
class LoggingModule extends Module
{
    /**
     * LoggingModule constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->name = "logging";
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler($app->getTempDir().DIRECTORY_SEPARATOR.'app.log',Logger::DEBUG));
        $app->addContainerDefinition('logger', $logger);
        parent::__construct($app);
        $logger->info('logging module initialized');
    }
}