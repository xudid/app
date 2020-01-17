<?php
namespace App\CoreModule\RoleModule\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="roles")
 **/
class Role
{
  /**
   * [private description]
   * @var int $id
   * @Id @Column(type="integer")
   * @GeneratedValue
   */
  private $id;

  /**
   * [private description]
   * @var string $name
   * @Column(type="string")
   */
  private $name;

  /**
   * [private description]
   * @var string $description
   * @Column(type="string")
   */
  private $description;

  function __construct()
  {

  }

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getName():string
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name=$name;
  }

  public function getDescription():string
  {
    return $this->description ;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function __toString()
  {
    return $this->name;
  }
}
