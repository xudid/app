<?php
namespace App\Pipeline;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *
 */
class Pipeline implements RequestHandlerInterface
{

  /**
   * @var $middlewares
   */
   private $middlewares=[];
/**
 * @var int index : the middlewares current index
 */
   private $index = 0;
   /**
    * @var ResponseInterface $response
    */
   private $response;
   /**
    * @var ServerRequestInterface $request
    */
   private $request;

  /**
   *
   */
  function __construct()
  {
    $this->response = new Response();
  }
/**
* @param ServerRequestInterface $request
* @return ResponseInterface
*/
  public function handle(ServerRequestInterface $request):ResponseInterface{

    $this->request = $request;
    return $this->response;
  }
/**
 * @param MiddlewareInterface $middleware
 */
  public function pipe(MiddlewareInterface $middleware){
    $this->middlewares[]=$middleware;
  }
/**
 * @param ServerRequestInterface $request : the incomming Request
 * @param ResponseInterface $response : the incomming response
 * @return ResponseInterface
 *
 */
  public function process(ServerRequestInterface $request , ResponseInterface $response): ResponseInterface{
    $this->request = $request;
    if(isset($this->middlewares[$this->index]))
    {
      $middleware = $this->middlewares[$this->index];
      $this->response = $middleware->process($this->request,$this);


      $statuscode = $this->response->getStatusCode();
     if($statuscode>200)
     {
       return $this->response ;
     }

     $success = $request->getAttribute("success");

    if($success && $this->index == (count($this->middlewares))-1)
    {
      //die();
      return $this->response ;
    }
      $this->index++;

    $this->response =  $this->process($this->request,$response);
  }

      return $this->response;


  }
}
