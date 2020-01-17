<?php
namespace Brick\Users;
use Brick;

use Brick\DBAL\DBAL;
use Brick\Users\User;
use Psr\Container\ContainerInterface;


/**
 *
 */
class UserDao
{
/**
 * @var \Psr\Container\ContainerInterface $container
 */
 private $container=null;
/**
 * the namespace use to as param by doctrine
 * @var string $namespace
 */
  private $namespace ="";

  /**
   * Construct a UserDao object
   * @param \Psr\Container\ContainerInterface $container the
   * DI container to inject object and values
   * @param string $namespace namespace to  use as param by doctrine
   * repository
   */
   public function __construct(\Psr\Container\ContainerInterface $container,string $namespace)
   {
     $this->container = $container;
     $this->namespace = $namespace;
   }

    /**
     * [create description]
     * @param  User   $user [description]
     * @return [type]       [description]
     */
        public function create(User $user)
        {
          $dbal = new DBAL($this->container->get('dbconfig'),
                                "/entities/");
          $entityManager = $dbal->getEntityManager();

        }
    /**
     * [save description]
     * @param  User   $user [description]
     * @return [type]       [description]
     */
        public function save(User $user) {
          $dbal = new DBAL($this->container->get('dbconfig'),
                                "/entities/");
          $entityManager = $dbal->getEntityManager();
          try{
            $entityManager->persist($user);
          $entityManager->flush();
          return $user;
          }
          catch(\Doctrine\DBAL\DBALException $ex)
          {
            print_r(__FILE__.__LINE__."<br>".$ex->getPrevious()->getCode());

          }

        }
/**
 * @param User $user to update
 */
    public function update(User $user)
    {
      $dbal = new DBAL($this->container->get('dbconfig'),
                            "/entities/");
      $entityManager = $dbal->getEntityManager();
      $entityManager->merge($user);
      $entityManager->flush();
      return $user;
    }
/**
 * [delete description]
 * @param  int $id [description]
 *
 */
    public function delete($id):void
    {
      $dbal = new DBAL($this->container->get('dbconfig'),
                            "/entities/");
      $entityManager = $dbal->getEntityManager();
      $userrepo = $entityManager->getRepository($this->namespace);
      $user = $userrepo->find($id);
      $entityManager->remove($user);
      $entityManager->flush();

    }

/**
 * Return all Articles in the database
 */
    public function findAll() {
      $dbal = new DBAL($this->container->get('dbconfig'),
                            "/entities/");
      $entityManager = $dbal->getEntityManager();
      $result =  ($entityManager->getRepository($this->namespace))->findAll();
      return $result;
    }
/**
 * @param mixed $id
 */
    public function findById($id)
    {
      $dbal = new DBAL($this->container->get('dbconfig'),
                            "/entities/");
      $entityManager = $dbal->getEntityManager();
      $result =  $entityManager->find($this->namespace, $id);
      return $result;
    }
/**
 * [findBy description]
 * @param  array $params [description]
 * @return array         [description]
 */
    public function findBy($params)
    {
      $dbal = new DBAL($this->container->get('dbconfig'),
                            "/entities/");
      $entityManager = $dbal->getEntityManager();
      $result =  $entityManager->getRepository($this->namespace)->findBy($params);
      return $result;
    }



}

?>
