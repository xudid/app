<?php

namespace App\CoreModule\ControllerDispatcher\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
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

	/**
	 * @var ResponseInterface
	 */
	private ResponseInterface $response;

	function __construct()
	{

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
		$view = null;
		if (sizeof($args) > 0) {
			$view = call_user_func_array($callback, array($args));
		} else {
			$view = call_user_func_array($callback, array());
		}
		$this->response->getBody()->write($view);
		return $this->response;

		/*if($action != "")
		{

		  $view =null;
		  if(\count($matches)>0)
		  {
			$view = "";
			if($controller != null && $action !=null)
			 {
			   $view = $controller->$action($matches);
			 }

			 else if(is_string($action))
			 {

			   $class = new ReflectionClass($action);
			   if($class->implementsInterface( 'Brick\Action\ActionInterface' )  )
			   {
				 $a = $class->newInstanceArgs([$this->container]);
				 $view = $a->call($matches);
			   }
			 }
			 call_user_func_array($callback, array($view,$matches[0]));
			 return $this->response;
		  }
		  //Matches count <=0
		  else
		  {
			if($controller)
			{
			  $view = $controller->$action();

			}
			else if(\is_string($action))
			{
			  $class = new \ReflectionClass($action);
			  if($class->implementsInterface( 'Brick\Action\ActionInterface' ) )
			  {
				$a = $class->newInstanceArgs([$this->container]);
				$view = $a->call();
			  }
			}
			call_user_func_array($callback, array($view));
			return $this->response;
		  }

		}
		//$action is an empty string
		else
		{
			$response =  call_user_func_array($controller,$matches);
			if(\is_null($response)){return $this->response;}
			return $response;
		}

	  }*/
	}
}
