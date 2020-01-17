<?php
namespace Brick\Users;
//use Brick\Controller\ModuleController;
use Brick\Views\{
	DataTableView,
	FormFactory,
	EntityViewFactory,
	SearchViewFactory
};

use Brick\Db\Mapper;
use Brick\Users\{UsersEntity,User};
use Brick\Router\Router;
use Brick\Views\Renderer;
use \Brick\Module\Module;
/**
 *Control an entity display and modifications
 */
class UserModule extends Module
{

  protected $scope="users";

	/*
	* @var UsersController $usersController
	*
	*/
	private $usersController=null;
/**
 * @param  Router  $router
 * @param Renderer $renderer
 * @param Firewall $firewall
 */
	public function __construct($container)
	{
		parent::__construct($container);
		$renderer = $this->renderer;
		$router = $this->router;
		$firewall = $this->firewall;
		$this->setModuleInfo("text","UserModule","","/users");
		$this->usersController = new Controller\UsersController($container);
		//add a methode to module to get allowed links for each http method :GET POST PUSH DELETE 
		//like getAllowedLinks($method
		//create a class MenuGenerator and remove menu generation from router)
		$this->allowedLinks=["users","users/new","users/search","users/:id/edit","users/:id","users/:id/delete"];
		//$mapper = new Mapper($this->namespace);
		//$relation  = $mapper->getRelationwith("Brick\UserModule\Role");
		// \print_r("<br>mapped :".$relation."<br>");


		$firewall->withRule("IPV6","::1","/users",[])
						 ->withRule("IPV6","::1","/users/new",[])
						 ->withRule("IPV6","::1","/users/create",[])
						 ->withRule("IPV6","::1","/users/search",[])
						 ->withRule("IPV6","::1","/users/:id",[])
						 ->withRule("IP","127.0.0.1","/users",[])
						 ->withRule("IP","127.0.0.1","/users/create",[])
				 		 ->withRule("IP","127.0.0.1","/users/new",[])
				 		 ->withRule("IP","127.0.0.1","/users/search",[])
				 		 ->withRule("IP","127.0.0.1","/users/:id",[])
						 ->withRule("NETWORKV4","192.168.0.0/24","/users/:id",[])
						 ->withRule("NETWORKV4","192.168.0.0/24","/users/:id",["LOG"=>[]])
						 ->withRule("IP","127.0.0.1","/users/:id/edit",[])
						 ->withRule("IPV6","::1","/users/:id/edit",[]);


		$this->router->get("users",'/users',[$this->usersController,'index',
																 function($view)use($renderer,$router)
																 {
																	 $matrix = $router->getUrlsMatrix("users","users",null,$this->allowedLinks);
																	 $renderer->render($view);
																	 $renderer->renderSideBar($matrix);
																 }

															 ],"Liste");

		$this->router->get("users",'/users/new',[$this->usersController,'new',function($view)use($renderer,$router){

			$matrix = $router->getUrlsMatrix("users","users/new",null,$this->allowedLinks);
			$renderer->render($view);
			$renderer->renderSideBar($matrix);
		}],"Ajouter");

		$this->router->get("users",'/users/search',
			[
				$this->usersController,
			  'searchform',
				function($view)use($renderer,$router)
				{
					$matrix = $router->getUrlsMatrix("users","users/search",null,$this->allowedLinks);
					$renderer->render($view);
					$renderer->renderSideBar($matrix);
				}
			],
			 "Rechercher");
		$router->post("users",'/users/searchresult',[$this->usersController,'searchresult',function($view)use($renderer,$router)
		{
			$matrix = $router->getUrlsMatrix("users","users/searchresult",null,$this->allowedLinks);
			$renderer->render($view);
			$renderer->renderSideBar($matrix);
		}]);
		$router->get("users",'/users/:id/edit',[$this->usersController,'edit',function($view,$id)use($renderer,$router){
			$matrix = $router->getUrlsMatrix("users","users/:id/edit",$id,$this->allowedLinks);
			$renderer->render($view,$id);
			$renderer->renderSideBar($matrix);
		}],"Editer")->with('id','[0-9]+')->with('edit','[a-z]+');

		$router->get("users",'/users/:id',[$this->usersController,'show',function($view,$id)use($renderer,$router){
			$matrix = $router->getUrlsMatrix("users","users/:id",$id,$this->allowedLinks);
			$page = $renderer->render($view,$id);
			$renderer->renderSideBar($matrix);
		}],"Voir")->with('id','[0-9]+');


		$router->get("users",'/users/:id/delete',[$this->usersController,'delete',function($view,$id)use($renderer,$router){

			$renderer->render($view,$id);
			//$renderer->renderSideBar($matrix);
	}],"Supprimer");


		$router->post("users",'/users/create',[$this->usersController,'create',function($view)use($renderer){$renderer->render($view);}]);
		$router->post("users",'/users/:id/update',[$this->usersController,'update',function($view)use($renderer){$renderer->render($view);}]);
		$router->post("users",'/users/:id/delete',[$this->usersController,'delete',function($view)use($renderer){$renderer->render($view);}]);

	  $router->post("users","/users/roles/:id/edit",
									[
											$this->usersController,
											'list_user_roles',
											function($view,$id)use($router, $renderer)
											{
												$matrix = $router->getUrlsMatrix("users","/users/roles/:id/edit",$id,$this->allowedLinks);
												$page = $renderer->render($view,$id);
												$renderer->renderSideBar($matrix);
											}
									],"Editer RoleModule Utilisateur");

	}



}
?>
