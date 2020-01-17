<?php
namespace Brick\Roles\Model;
use Brick\Model\Entity;
use Brick\DBAL\Dao;


/**
 *
 */
class RolesEntity  implements Entity
{
  private $dao = null;
  function __construct($container)
  {
    $this->dao = new Dao($container,"\Brick\Roles\Model\Role");
  }

  public function findById($id):Role{

    $role = $this->dao->findById($id);
    if($role){
      return  $role ;
    }
    return null;

  }
  public function findBy(array $fields){
    $roles = $this->dao->findBy($fields);
    return $roles;
  }

  public function findAll():array{
    $roles = $this->dao->findAll();
    return $roles;
  }
  public function create($role):Role{
      $this->dao->save($role);
      return $role;
  }
  public function update($role):Role
  {
    $this->dao->update($role);
    return $role;
  }
  public function delete($id){
    $r =$this->dao->delete($id);
    return $r;
  }
}

?>
