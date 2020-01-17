<?php
namespace Brick\Users\Controller;
use Brick\Controller\ModuleController;
use Brick\Ui\Table\TableLegend;

use Brick\Users\Model\{UserEntity,User};
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
class UsersController implements ModuleController
{
  private $namespace="Brick\Users\Model\User";
	private $container=null;
  function __construct($container)
  {
		$this->container = $container;
  }

  /**
	 * Return a form to get new user datas
	 */
	public function new(){
		$ff = new FormFactory($this->namespace,"default","create","POST");
		$ff->setFormTitle("Nouvel utilisateur");
		return $ff->getForm();

	}
/**
 * @param array $params  : params to update a user
 */
	public function edit($params){
		$id = $params[0];
		$entity = new UserEntity($this->container);
		$user = $entity->findById($id);
		if($user ==false){return $this->showError("entity not found");}

		$ettf = new FormFactory($user,"default","update","POST");
		$ettf->setFormTitle("Edition utilisateur");
		$ef = $ettf->getForm();

		return  $ef;
	}
/**
 * @param array $params  : params to show a user
 */
	public function show($params){
		$id = $params[0];
		$entity = new UserEntity($this->container);
		$user = $entity->findById($id);
		if($user ==false){return $this->showError("entity not found");}
		$this->entityViewGenerator = new EntityViewFactory($user,null);
		$this->entityViewGenerator->setCurrentPath("/users/:id");
	  $ev = $this->entityViewGenerator->getView();

	  return $ev;

	}
	/**
	 * @param array $params  : params to show category with type
	 */
	public function category($params){
		return "Get category $params[0] with type $params[1]";
	}
/**
 * Create a new user
 *only display the sentence  :
 *"creating user please wait"
 */
	public function create(){
		$user = new User();
		$user->setName($_POST['user_name']);
		\print_r("Password entered pass:".$_POST['user_password']." :".strlen($_POST['user_password']));
		$user->initPassword($_POST['user_password']);
		$user->setEmail($_POST['user_email']);
		$user->setRole($_POST['user_role']);
		$entity = new UserEntity($this->container);
		$entity->create($user);
	}
/**
 * @param array $params  : params to delete a user
 */
	public function delete($params)
	{
		$id = $params[0];
		$entity = new UserEntity($this->container);
		$entity->delete($id);
		echo "You have deleted $this->namespace with id :$id";
	}
	/**
	 * Update user with
	 * @param array $params parametes to update user
	 */
	public function	update($params){
		$id = $params[0];

		$entity = new UserEntity($this->container);
		$user = $entity->findById($id);
		if($user ==false){return $this->showError("entity not found");}
		$text = "Are you sure to update this $this->namespace with id :$id";
		$user->setName($_POST['user_name']);
		//\print_r("Password entered pass:".$_POST['password']." :".strlen($_POST['password']));
		$user->initPassword($_POST['user_password']);
		$user->setEmail($_POST['user_email']);
		$user->setRole($_POST['user_role']);
		//var_dump($user);
		//print_r("<br>");
		$entity->update($user);
		return $text;
	}


/**
 * Search UserModule
 */
	public function searchform(){


		 $etsf = new SearchViewFactory($this->namespace,null,"searchresult","POST");
		 $etsf->setViewTitle("Search User");
		 $sf = $etsf->getSearchView();
		 return $sf;
	}

	public function searchresult(){
		$userEntity = new UserEntity($this->container);
		$dtv = (new DataTableView($this->container,$this->namespace,null))->withClickableRows("/users");
		if(isset($_POST["name"])&&$_POST["name"]!="")
		{
			$params["name"] = $_POST["user"];
			$dtv->where($params);
		}

		if(isset($_POST["email"])&&$_POST["email"]!="")
		{
			$params["email"] = $_POST["user"];
			$dtv->where($params);

		}

		if(isset($_POST["role"])&&$_POST["role"]!="")
		{
			$params["role"] = $_POST["role"];
			$dtv->where($params);
		}
		$dtv->setTitle("<h2>Resultat de la recherche</h2>");
		return $dtv->getView();
	}
/**
 * Return a view with the user list
 */
	public function index(){

		$dtv = (new DataTableView($this->container,$this->namespace,null))->withClickableRows("/users");

		$legendTitle =new TableLegend("<h2>Liste des Utilisateurs</h2>",TableLegend::TOP_LEFT);
		$addButton = new AddButton();
		$addButton->setOnClick("location.href='/users/new'");
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
