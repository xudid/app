<?php
namespace App\CoreModule\UserModule\Views;

use Ui\Views\ViewFilter;

class UserAuthFormFilter extends ViewFilter
{
    /**
     * UserAuthFormFilter constructor.
     */
    public function __construct()
    {
        //Read right
        $this->viewables = array("name","email");//,"password","role"
        //Write right
        $this->writables = array("name","password");
        $this->confirmables = array("password");
        $this->searchables = array("name","email","role");

    }

}
