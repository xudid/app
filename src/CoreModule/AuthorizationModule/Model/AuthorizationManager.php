<?php


namespace App\CoreModule\AuthorizationModule\Model;


use Entity\Database\DaoInterface;
use Entity\Model\ModelManager;

class AuthorizationManager extends ModelManager
{
    public function __construct(DaoInterface $dao, string $className)
    {
        parent::__construct($dao, $className);
    }
}