<?php
namespace Brick\Users\Views;
use \Brick\Views\ViewFilter;

class UserFormFilter extends ViewFilter
{

  function __construct()
  {
     $this->viewables = ["name","email","password","role"];
     $this->viewablesfor = ["/users/:id"=>["name","email","password","role"]];
     $this->writables = ["name","email","password","role"];
     $this->writablesfor = [];
     $this->confirmables = ["password"];
	   $this->searchables = ["name","email","role"];
  }

 }
?>
