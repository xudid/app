<?php

namespace App\CoreModule\RouterModule\Controller;

use App\Controller;
use Ui\HTML\Element\Base\H3;
use Ui\HTML\Element\Nested\Div;
use Ui\HTML\Element\Nested\Section;
use Ui\Translation\FileSource;
use Ui\Translation\Translator;
use Ui\Widget\Table\ArrayTableFactory;
use Ui\Widget\Table\Column\Column;
use Ui\Widget\Table\Legend\TableLegend;

class RouterController extends Controller
{
    public function __construct()
    {
		parent::__construct();
    }

    public function index()
    {
        $section = new Section();
        $this->router = $this->app->get('router');
       return $section->feed(
           ...$this->routesTables()
        );
    }

    public function show(string $method, string $routeName)
    {
        $this->router = $this->app->get('router');
        $route = $this->router->getRoute($method, $routeName);
        $routeView = new Div();

        if ($route) {
            $routeView->setTitle('Route ' . $route->getName())->setClass('large-30');
			$parameters = $route->getParams();
			$parameters = array_map(function ($name, $pattern){
                return ['name' => $name, 'pattern' => $pattern];
            }, array_keys($parameters), array_values($parameters));
            $source = new FileSource(sys_get_temp_dir() . 'translations.php');
            $translator = new Translator($source);
            $tableFactory = new ArrayTableFactory($translator);
            $tableFactory->setColumns(
                new Column('name', 'Name'),
                new Column('pattern', 'Pattern')
            )->setLegends([new TableLegend('Parameters', TableLegend::TOP_LEFT)])->useData($parameters);
            return $routeView->feed($tableFactory->getTable())
                ->setClass('large-50');
        } else {
            $routeView->setTitle('Route not found');
            return $routeView->feed(new H3('No route with this name'));
        }
    }

    private function routesTables(): array
    {
        $scopesTables = [];
        foreach ($this->router->getAuthorizedMethods() as $method) {
            $routes = $this->router->getRoutes()[$method] ?? [];
            $scopes = [];
            foreach ($routes as $route) {
                $scopes[]= [
                    'name' => $route->getName(),
                    'path' => $route->getPath()
                ];
            }

            $legend = new TableLegend($method, TableLegend::TOP_LEFT);
            $source = new FileSource(sys_get_temp_dir() . 'translations.php');
            $translator = new Translator($source);
            $tableFactory = new ArrayTableFactory($translator);
            $nameColumn = new Column('name', 'Nom');
            $pathColumn = new Column('path', 'Chemin');

            $tableFactory->setColumns($nameColumn, $pathColumn)
                         ->setLegends([$legend])
                         ->useData($scopes);

            $scopesTables[] = $tableFactory->getTable();
        }

        return $scopesTables;
    }
}
