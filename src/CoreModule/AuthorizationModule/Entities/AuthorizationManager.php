<?php
namespace Brick\Security\Model;

use Brick\Model\Manager;

/**
 * 
 */
class AuthorizationManager extends Manager
{
	
	public function __construct($dbname)
	{
		parent::__construct($dbname);
	}

	public function getAuthorizedModules($status,$userId,$zoneId=null)
	{
		$modules = [];
		$sql = "SELECT name , url from application INNER JOIN application_acces ON idApplication = id where application_acces.idZone = ? and  application_acces.idStatus = ?";
		$this->connect();
		$request  = $this->sgbd->prepare($sql);
		$result = $request->execute([$zoneId,$status]);
		
		while ($data = $request->fetch(\PDO::FETCH_ASSOC)) {
			$modules[] = $data;
			
		}
		return $modules;

	}

	public function getAuthorizedAction($zoneId,$status,$userId)
	{
		$sql = "";
		$request  = $this->sgbd->prepare($sql);
		$result = $request->execute([$status, $userId,$zoneId]);
	}
}