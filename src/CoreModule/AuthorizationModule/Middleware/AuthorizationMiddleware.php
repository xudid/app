<?php

namespace App\CoreModule\AuthorizationModule\Middleware;

use App\CoreModule\AuthModule\AuthController;
use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthorizationMiddleware
 * @package App\CoreModule\AuthorizationModule\Middleware
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    private AuthController $authController;
    private $url;

    function __construct($authController)
    {
        $this->authController = $authController;
    }

    /**
     * Process a Request  and return a Response
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $defaultAllowedRoutes = [
            'default',
            'login',
            'logout',
            'auth',
            'get_reset_token',
            'mail_reset_token',
            'recovery_password',
            'setup',// todo remove this as soon as possible
        ];
        $this->response = $handler->handle($request);
        $user = $this->authController->isloggedin();
        $route = $request->getAttribute('route');
        if (!$route) {
            $response = $handler->handle($request);
            $response = $response->withStatus("404");
            return $response;
        }
        if($route && in_array($route->getName(), $defaultAllowedRoutes)) {
            $response = $handler->handle($request);
            return $response;
        }
        // if user has role superadmin go on any existing ressource
        if (!$user) {
            $this->authController->saveAskedUrl($request->getUri()->getPath());
            $response = $handler->handle($request);
            $response = $response->withStatus("302");
            $response = $response->withHeader("Location", "/login");
            return $response;
        } else {
            $authorizationController = new AuthorizationController();
            $authorized = $authorizationController->isAuthorize($user->getRoles(), $route->getName());
            if($authorized) {
                $response = $handler->handle($request);
                return $response;
            } else {
                $response = $handler->handle($request);
                $response = $response->withStatus("302");
                $response = $response->withHeader("Location", "/login");
                return $response;
            }
        }


    }

    /*
    * @param ServerRequestInterface $request
    * @param array $rights
    * @return ResponseInterface;
    */
    private function processRights($handler, $request, $dest, $rights)
    {
        $response = $handler->handle($request);
        return $response;

    }
}

