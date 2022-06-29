<?php


namespace App;


use Core\Contracts\ManagerInterface;
use Core\Http\Handler\RequestHandler;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Router\Router;

class Controller
{
    protected Router $router;
    protected App $app;
    protected RequestInterface $request;
    protected RequestHandler $requestHandler;

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

    public function modelManager(string $class, string $managerClass = ''):ManagerInterface
    {
        return $this->app->modelManager($class, $managerClass);
    }

    public function render($content)
    {
        return App::render($content);
    }
}
