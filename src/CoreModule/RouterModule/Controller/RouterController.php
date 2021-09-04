<?php

namespace App\CoreModule\RouterModule\Controller;

use App\App;
use App\Controller;
use Ui\Views\EntityView;
use Ui\Widgets\Table\DivTable;
use Ui\Widgets\Table\TableColumn;
use Ui\Widgets\Tabs;

class RouterController extends Controller
{

	/**
	 * RouterController constructor.
	 * @param App $app
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$view = new EntityView();
		$this->router = $this->app->get('router');
		$tabs = new Tabs();
		foreach ($this->router->getAuthorizedMethods() as $method) {
			$tabs->addTab($method, $method, $this->routeTableByMethod($method));
		}
		$view->setTitle('Routes');
		return $view->feed($tabs);
	}

	private function routeTableByMethod($method)
	{
		$nameColumn = new TableColumn('name', 'Nom');
		$patternColumn = new TableColumn('pattern', 'Pattern');
		$parametersColumn = new TableColumn('parameters', 'Parameters');

		$routesTable = [];
		$routes = $this->router->getRoutes()[$method];
		foreach ($routes as $route) {
			$parameters = $route->getParams();
			$parameters = array_map(function ($name, $pattern){
				return $name . ' : ' . $pattern;
			}, array_keys($parameters), array_values($parameters));
			$datas[]= [
				'name' => $route->getName(),
				'pattern' => $route->getPath(),
				'parameters' => implode(' , ',$parameters),
			];
		}
		$routesTable[] = (new DivTable([],[$nameColumn, $patternColumn, $parametersColumn],$datas))->setClass('mt-24');

		return $routesTable;
	}
}
