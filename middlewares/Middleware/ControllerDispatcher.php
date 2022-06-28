<?php

namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ControllerDispatcher
 * @package Middleware
 */
class ControllerDispatcher implements MiddlewareInterface
{
    private ResponseInterface $response;

    function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->response = $handler->handle($request);
        $route = $request->getAttribute("route");
        $success = $request->getAttribute("success");
        if (is_null($route)) {
            $this->response = $this->response->withStatus(404);
            return $this->response;
        }

        if (!$success) {
            return $this->response;
        }

        $callback = $route->getCallback();
        $args = $route->getValues();

        if (sizeof($args) > 0) {
            $view = call_user_func_array($callback, array($args));
        } else {
            $view = call_user_func_array($callback, array());
        }

        $this->response->getBody()->write((string)$view);

        return $this->response;
    }
}
