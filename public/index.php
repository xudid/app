<?php

use App\App;

/**
 * Application input point
 */
require '../vendor/autoload.php';
try {
    $app = App::getInstance();
    $app::setAppPageClass(\Ui\Widget\View\AppPage::class);
    $app->run();
} catch (Exception $exception) {
    dump($exception);
}

