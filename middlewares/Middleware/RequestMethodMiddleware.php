<?php
namespace Brick\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *
 */
class RequestMethodMiddleware implements MiddlewareInterface
{

  public function __construct()
  {

  }

  /**
   * Process a Request  and return a Response
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $handler
   * @return ResponseInterface
   */
    function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
      $method = $request->getMethod();
      $response = $handler->handle($request);
      return $response;


      }
    }



 ?>
