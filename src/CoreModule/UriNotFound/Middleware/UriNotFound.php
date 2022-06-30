<?php

namespace App\CoreModule\UriNotFound\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Server\RequestHandlerInterface;
use GuzzleHttp\Psr7\Response;
/**
 * Firewall filter urls
 */
class UriNotFoundMiddleware implements MiddlewareInterface
{


/**
 *
 */
  public function __construct(){

  }
/**
 * Process a Request  and return a Response
 * @param ServerRequestInterface $request
 * @param RequestHandlerInterface $handler
 * @return ResponseInterface
 */
  function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
  {

    $response = $handler->handle($request);
    $success = $request->getAttribute("success");
    if(!$success){
      $response = $response->withStatus("404");
      return $response;
    }
    else {
      return $response;
    }




  }
/**
 * @param string $url : url to filter if required by user
 */
  public function filter(string $url){
    if(isset($url)){$this->filteredUrls[]= $url;}
  }
}

?>
