<?php


namespace App\CoreModule\RouterModule\Controller;


use App\App;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;

class ModuleManagerController
{
    public function index(App $app)
    {
        ($app->get('logger'))->info('manager module index');
        return new DivTable(
            [new TableLegend('Modules activés')],
            [
                new TableColumn('name','Name'),
                new TableColumn('scope', 'Scope'),
            ],
            array_values($app->getModulesInstances())
        );
    }
}