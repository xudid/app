<?php
namespace App\CoreModule\UserModule\Entities;

use App\CoreModule\UserModule\Model\User;
use Entity\Database\DaoInterface;
use Entity\Entity;

/**
 * Class UserEntity
 * @package Brick\Users\Model
 */
class UserEntity  implements Entity
{
  private $dao = null;
  function __construct(DaoInterface $dao)
  {
    $this->dao = $dao;
  }

  public function findById($id):User{

    $user = $this->dao->findById($id);
    if($user){
      return  $user ;
    }
    return null;

  }
  public function findBy(array $fields){
    $users = $this->dao->findBy($fields);
    return $users;
  }

  public function findAll(){

    $result = $this->dao->findAll();
    if(is_int($result)){$result=[];}
    return $result;
  }
  public function create($user):User{
      $this->dao->save($user);
      return $user;
  }
  public function update($user):User
  {
    $this->dao->update($user);
    return $user;
  }
  public function delete($id){
    $r =$this->dao->delete($id);
    return $r;
  }
}

?>
