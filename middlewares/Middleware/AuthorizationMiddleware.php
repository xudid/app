<?php
namespace Brick\Middleware;
use Interop\Http\Server\{MiddlewareInterface,RequestHandlerInterface};
use Psr\Http\Message\{ServerRequestInterface,ResponseInterface};
use Brick\Users\User  ;
use Brick\Utils\BString;
/**
 *
 */
class AutorizationMiddleware implements MiddlewareInterface
{

  /*
  * @var array $rights
  */
  private $rights=[];
  private $authController;
  private $url;

  /*
  *
  */
  function __construct($authController, $logger=null)
  {
    $this->authcontroller = $authController;
  }
  /**
   * Process a Request  and return a Response
   * @param string $ressource
   * @param string $role
   * @param array $rights
   * @return self
   */
  public function withAuthorization($ressource,$role,$rights=[])
  {
    $currentAuthorizations = [];

    if(\array_key_exists($ressource,$this->rights)&&
        array_key_exists($role,$this->rights[$ressource]))
    {
      $currentAuthorizations = $this->rights[$ressource][$role];
    }
    $currentAuthorizations = array_merge($currentAuthorizations, $rights);

    $this->rights[$ressource][$role] = $currentAuthorizations;
    return $this;

  }
  /**
   * Process a Request  and return a Response
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $handler
   * @return ResponseInterface
   */
    function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
      $this->response = $handler->handle($request);
      $this->cleanUrl($request->getUri()->getPath());
      $destination="";
      foreach ($this->rights as $key => $value) {
        $dest = new \Brick\Security\Destination($key);
        if($dest->match($this->url)){
          $destination = $key;

          break;
        }

      }
      $user = $this->authController->isloggedin();

      if(!$user)
      {
        $response = $handler->handle($request);
        $response = $response->withStatus("302");
        $response = $response->withHeader("Location","/login");

        return $response;
      }
      else
      {
        $role = $user->getRole();
        if(\array_key_exists($role,$this->rights[$destination]))
        {
          $rights = $this->rights[$destination][$role];
          return $this->processRights($handler, $request, $dest, $rights);
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
     private function cleanUrl($url)
     {
       $this->url = trim($url,'/');

       while(BString::startsWith($this->url, '/'))
       {
         $this->url = trim($this->url,'/');
       }
       $this->url = '/'.$this->url;
     }
}

