<?php
namespace App\CoreModule\AuthorizationModule\Controller;

/**
 * 
 */
class AuthorizationController 
{
	private $manager;
	public function __construct($dbname)
	{
		$this->manager = new AuthorizationManager($dbname);
        return $this;
	}

	public function getAuthorizedModules($status,$userId,$zoneId=null)
	{
		$authorizedModules = [];
		$modules = $this->manager->getAuthorizedModules($status,$userId,$zoneId);
		if ($modules) {
			$authorizedModules = $modules;

		}

		return $authorizedModules;
		

	}

	public function getAuthorizedAction($zoneId,$status,$userId)
	{

	}
}