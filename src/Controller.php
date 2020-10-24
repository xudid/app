<?php


namespace App;


use Entity\Model\ManagerFactory;
use Entity\Model\Model;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Router\Router;
use Ui\Handler\RequestHandler;
use Ui\Views\DataTableView;
use Ui\Views\EntityViewFactory;
use Ui\Views\FormFactory;
use Ui\Views\SearchViewFactory;

class Controller
{
    protected Router $router;
    /**
     * @var App
     */
    protected App $app;

    protected RequestInterface $request;


    public function __construct()
    {
        $this->app = App::getInstance();
        $this->router = $this->app->get('router');
        $this->request = ServerRequest::fromGlobals();
        $this->requestHandler = new RequestHandler($this->request);
    }

    public function redirect(string $url)
    {
        $this->app->redirectTo($url);
    }

    public function routeTo(string $routeName, array $params)
    {
        App::redirectToRoute($routeName, $params);
    }



    public function modelManager(string $class, string $managerClass = '')
    {
        return $this->app->getModelManager($class, $managerClass);
    }

    /**
     * @return EntityViewFactory
     */
    public function entityViewFactory(string $class, int $id): EntityViewFactory
    {
        $factory = new EntityViewFactory($this->modelManager($class), $id);
        $factory->setRouter($this->router);
        return $factory;
    }

    /**
     * @return FormFactory
     */
    public function formFactory($class): FormFactory
    {
        $factory = new FormFactory($class);
        if($class instanceof Model) {
            $class = $class::getClass();
        }
        $factory->setRouter($this->router)
            ->setManager($this->modelManager($class));
        return $factory;
    }

    public function searchViewFactory($class)
    {
        return new SearchViewFactory($class);
    }

    /**
     * @return DataTableView
     */
    public function tableFactory(string $class): DataTableView
    {
        $factory = new DataTableView($class, $this->modelManager($class));
        $factory->setRouter($this->router);
        return $factory;
    }

    public function render($content)
    {
        return App::render($content);
    }
}