<?php
namespace App\CoreModule\UserModule\Views;

class UserAuthFormFilter
{
//Read right
//    private $viewables = array("name","email");//,"password","role"
    //Write right
    private $writables = array("name","password");
    private $confirmables = array("password");
    private $searchables = array("name","email","role");

    public function getViewables()
    {
        return $this->viewables ;
    }
    public function getWritables()
    {
        return $this->writables ;
    }

    public function getConfirmables()
    {
    }

    public function getSearchables()
    {
        return $this->searchables ;
    }
}
