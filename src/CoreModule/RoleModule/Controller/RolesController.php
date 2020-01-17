<?php
namespace Brick\Roles\Controller;
use Brick\Controller\ModuleController;
use Brick\Ui\Table\TableLegend;

use Brick\Roles\Model\{RolesEntity,Role};
use Brick\Views\{
	DataTableView,
	FormFactory,
	EntityViewFactory,
	SearchViewFactory
};

use Brick\Ui\Buttons\{AddButton};

/**
 *
 */
class RolesController implements ModuleController
{
  private $namespace="Brick\Roles\Model\Role";

  function __construct($container)
  {
		$this->container=$container;
  }

  /**
	 * Return a form to get new user datas
	 */
	public function new(){
		$ff = new FormFactory($this->namespace,"default","create","POST");
		return $ff->getForm();

	}
/**
 * @param array $params  : params to update a user
 */
	public function edit($params){
		$id = $params[0];
		$entity = new RolesEntity($this->container);
		$role = $entity->findById($id);
		if($role ==false){return $this->showError("entity not found");}

		$ettf = new FormFactory($role,"default","update","POST");
		$ettf->setFormTitle("Edit ");
		$ef = $ettf->getForm();

		return  $ef;
	}
/**
 * @param array $params  : params to show a user
 */
	public function show($params)
  {
		//print_r("show the role");
		$id = $params[0];
		$entity = new RolesEntity($this->container);
		$role = $entity->findById($id);
		if($role ==false){return $this->showError("entity not found");}
		$this->entityViewGenerator = new EntityViewFactory($role,null);
		$this->entityViewGenerator->setCurrentPath("/roles/:id");
	  $ev = $this->entityViewGenerator->getView();
	  return $ev;
	}

/**
 * Create a new role
 *only display the sentence  :
 *"creating user please wait"
 */
	public function create(){
		$role = new Role();
		$role->setName($_POST['role_name']);
		$role->setDescription($_POST['role_description']);
		var_dump($role);
		$entity = new RolesEntity($this->container);
		$entity->create($role);
	}
/**
 * @param array $params  : params to delete a user
 */
	public function delete($params)
	{
		$id = $params[0];
		$entity = new RolesEntity();
		$entity->delete($id);
		echo "You have deleted $this->namespace with id :$id";
	}
	/**
	 * Update user with
	 * @param array $params parametes to update user
	 */
	public function	update($params){
		$id = $params[0];

		$entity = new RolesEntity($this->container);
		$role = $entity->findById($id);
		if($role ==false){return $this->showError("entity not found");}
		$text = "Are you sure to update this $this->namespace with id :$id";
		$role->setName($_POST['role_name']);
		$role->setDescription($_POST['role_description']);
		$entity->update($role);
		return $text;
	}


/**
 * Search Users
 */
	public function searchform(){


		 $etsf = new SearchViewFactory($this->namespace,null,"searchresult","POST");
		 $etsf->setViewTitle("Search Role");
		 $sf = $etsf->getSearchView();
		 return $sf;
	}

	public function searchresult(){
		$params=[];
		$userEntity = new RolesEntity($this->container);
		$dtv = (new DataTableView($this->container,$this->namespace,null))->withClickableRows("/roles");
		if(isset($_POST["name"])&&$_POST["name"]!="")
		{
			$params["name"] = $_POST["name"];
			$dtv->where($params);
		}

		if(isset($_POST['description'])&&$_POST['description']!="")
		{
			$params["description"] = $_POST["description"];
			$dtv->where($params);

		}
		$dtv->setTitle("<h2>Resultat de la recherche</h2>");
		return $dtv->getView();
	}
/**
 * Return a view with the user list
 */
	public function index(){

		$dtv = (new DataTableView($this->container,$this->namespace,null))->withClickableRows("/roles");
		$legendTitle =new TableLegend("<h2>Liste des roles</h2>",TableLegend::TOP_LEFT);
		$addButton = new AddButton();
		$addButton->setOnClick("location.href='/roles/new'");
		$legendButton = new TableLegend($addButton,TableLegend::TOP_RIGHT);
		$dtv->addALegend($legendTitle);
		$dtv->addALegend($legendButton);

		return $dtv->getView();
	}


/**
 * Return an Error message
 * @param string $message the error message to display
 */
	public function showError($message){
		return $message;
	}
}

?>
