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
	private AuthorizationController $authorizationController;
	private array $defaultAllowedRoutes;
	private ?ResponseInterface $response = null;

	function __construct(AuthController $authController, AuthorizationController $authorizationController, array $defaultAllowedRoutes)
	{
		$this->authController = $authController;
		$this->authorizationController = $authorizationController;
		$this->defaultAllowedRoutes = $defaultAllowedRoutes;

	}

	/**
	 * Process a Request  and return a Response
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 */
	function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$this->response = $handler->handle($request);
		$user = $this->authController->isloggedin();
		$route = $request->getAttribute('route');

		if (!$route) {
			$response = $this->response->withStatus("404");
			return $response;
		}

		if($route && in_array($route->getName(), $this->defaultAllowedRoutes)) {
			return $this->response;
		}

		$authorized = false;
		if ($user) {
			$authorized = $this->authorizationController
				->isAuthorize($user->getRoles(), $route->getName());
		}

		if (!$user ||!$authorized) {
			$this->authController->saveAskedUrl($request->getUri()->getPath());
			$this->response = $this->response->withStatus("302");
			$this->response = $this->response->withHeader("Location", "/login");
			return $this->response;
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
