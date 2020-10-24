<?php


namespace App\CoreModule\RouterModule\Controller;


use App\App;
use Router\Route;
use Router\Router;
use SebastianBergmann\CodeCoverage\Node\File;
use Ui\HTML\Elements\Bases\H3;
use Ui\HTML\Elements\Nested\Section;
use Ui\Views\EntityView;
use Ui\Widgets\FieldInfo;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Table\TableLegend;
use Ui\Widgets\Views\Modal;
use function foo\func;

class RouterController
{
    /**
     * @var App
     */
    private App $app;
    private Router $router;

    /**
     * RouterController constructor.
     * @param App $app
     */
    public function __construct()
    {
        $this->app = App::getInstance();
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
        $routeView = new EntityView();

        if ($route) {
            $routeView->setTitle('Route ' . $route->getName())->setClass('large-30');
            $parameters = $route->getParams();
            $parameters = array_map(function ($name, $pattern){
                return ['name' => $name, 'pattern' => $pattern];
            }, array_keys($parameters), array_values($parameters));
            return $routeView->feed(
                (new FieldInfo('Path', $route->getPath()))->setClass('large-50'),
                (new DivTable(
                    [new TableLegend('Parameters', TableLegend::TOP_LEFT)],
                    [
                        new TableColumn('name', 'Name'),
                        new TableColumn('pattern', 'Pattern')
                    ],
                    $parameters
                ))->setClass('large-50')
            );
        } else {
            $routeView->setTitle('Route not found');
            return $routeView->feed(new H3('No route with this name'));
        }
    }

    private function routesTables()
    {
        $nameColumn = new TableColumn('name', 'Nom');
        $scopesTables = [];
        foreach ($this->router->getAuthorizedMethods() as $method) {
            $routes = $this->router->getRoutes()[$method];
            $methodScopes = array_keys($routes);
            $scopes = [];
            foreach ( $methodScopes as $getScope) {
                $scopes[]= ['name' => $getScope];
            }
            $legend = new TableLegend($method,TableLegend::TOP_LEFT);
            $scopesTables[] = new DivTable([$legend],[$nameColumn],$scopes);
        }

        return $scopesTables;
    }
}