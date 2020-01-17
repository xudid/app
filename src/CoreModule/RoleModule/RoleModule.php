<?php
namespace Brick\Roles;
//use Brick\Controller\ModuleController;
use Brick\Views\{
	DataTableView,
	FormFactory,
	EntityViewFactory,
	SearchViewFactory
};

use Brick\Db\Mapper;
use Brick\Users\{RolesEntity,Role};
use Brick\Router\Router;
use Brick\Views\Renderer;
use \Brick\Module\Module;
/**
 *Control an entity display and modifications
 */
class RoleModule extends Module
{

  protected $scope="roles";

	/*
	* @var UsersController $usersController
	*
	*/
	private $rolesController=null;
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
		$this->setModuleInfo("text","RoleModule","","/roles");
		$this->rolesController = new Controller\RolesController($container);
		$this->allowedLinks=["roles","roles/new","roles/search","roles/:id/edit","roles/:id","roles/:id/delete"];



		$firewall->withRule("IPV6","::1","/roles",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("IPV6","::1","/roles/new",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("IPV6","::1","/roles/search",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("IPV6","::1","/roles/:id",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("IP","127.0.0.1","/roles",["AUTH"=>["role"=>["admin"]]])
				 		 ->withRule("IP","127.0.0.1","/roles/new",["AUTH"=>["role"=>["admin"]]])
				 		 ->withRule("IP","127.0.0.1","/roles/search",["AUTH"=>["role"=>["admin"]]])
				 		 ->withRule("IP","127.0.0.1","/roles/:id",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("NETWORKV4","192.168.0.0/24","/roles/:id",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("NETWORKV4","192.168.0.0/24","/roles/:id",["LOG"=>"null"])
						 ->withRule("IP","127.0.0.1","/roles/:id/edit",["AUTH"=>["role"=>["admin"]]])
						 ->withRule("IPV6","::1","/roles/:id/edit",["AUTH"=>["role"=>["admin"]]]);


		$this->router->get("roles",'/roles',[$this->rolesController,'index',
																 function($view)use($renderer,$router)
																 {
																	 $matrix = $router->getUrlsMatrix("roles","roles",null,$this->allowedLinks);
																	 $renderer->render($view);
																	 $renderer->renderSideBar($matrix);
																 }

															 ],"Liste");

		$this->router->get("roles",'/roles/new',[$this->rolesController,'new',function($view)use($renderer,$router){

			$matrix = $router->getUrlsMatrix("roles","roles/new",null,$this->allowedLinks);
			$renderer->render($view);
			$renderer->renderSideBar($matrix);
		}],"Ajouter");

		$this->router->get("roles",'/roles/search',
			[
				$this->rolesController,
			  'searchform',
				function($view)use($renderer,$router)
				{
					$matrix = $router->getUrlsMatrix("roles","roles/search",null,$this->allowedLinks);
					$renderer->render($view);
					$renderer->renderScripts(["/js/project1.js"]);
					$renderer->renderSideBar($matrix);
				}
			],
			 "Rechercher");
		$router->post("roles",'/roles/searchresult',[$this->rolesController,'searchresult',function($view)use($renderer,$router)
		{
			$matrix = $router->getUrlsMatrix("roles","roles/searchresult",null,$this->allowedLinks);
			$renderer->render($view);
			$renderer->renderSideBar($matrix);
		}]);
		$router->get("roles",'/roles/:id/edit',[$this->rolesController,'edit',function($view,$id)use($renderer,$router){
			$matrix = $router->getUrlsMatrix("roles","roles/:id/edit",$id,$this->allowedLinks);
			$renderer->render($view,$id);
			$renderer->renderSideBar($matrix);
		}],"Editer")->with('id','[0-9]+')->with('edit','[a-z]+');

		$router->get("roles",'/roles/:id',[$this->rolesController,'show',function($view,$id)use($renderer,$router){
			$matrix = $router->getUrlsMatrix("roles","roles/:id",$id,$this->allowedLinks);
			$page = $renderer->render($view,$id);
			$renderer->renderSideBar($matrix);
		}],"Voir")->with('id','[0-9]+');


		$router->get("roles",'/roles/:id/delete',
									[
										$this->rolesController,
										'delete',
										function($view,$id)use($renderer,$router)
										{
											$renderer->redirectTo('/roles/');
										}
									],
									"Supprimer"
								);


		$router->post("roles",'/roles/create',
									[
										$this->rolesController,
										'create',
										function($view)use($renderer)
										{
											$renderer->redirectTo('/roles');
										}
									]
								);

		$router->post("roles",'/roles/:id/update',
									[
										$this->rolesController,
										'update',
										function($view,$id)use($renderer)
										{
											$renderer->redirectTo('/roles/'.$id);
										}
									]
								);

		$router->post("roles",'/roles/:id/delete',
									[
										$this->rolesController,
										'delete',
										function($view)use($renderer)
										{
											$renderer->redirectTo('/roles/');
										}
									]
								);
		$router->get("roles",'/categorie/:id/type/:id',[$this->rolesController,'category',function($view)use($renderer){
	  $renderer->render($view);
	  }]);

	}



}
?>
