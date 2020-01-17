<?php
namespace Brick\Roles;
use Brick\Db\QueryBuilder;
class RoleSQL{

//TODO
//write a parent class with class name in constructor
//public static function findAll();
//public static function findBy();
//public static function new();
//public static function update();
//public static function delete();
//public static function deleteAll();
public static function getAll(){return ((new QueryBuilder())->from("roles")->build("select"));}
public static function findById(){return ((new QueryBuilder())->from("roles")->where("id = :id")->build("select"));}
public static function findBy(array $params)
{
  $qb = new QueryBuilder();
  $qb->from("roles");
  foreach ($params as $key=>$value)
  {

    $qb->where($key." = :".$key);
  }


  return $qb->build("select");
}
public static function new(){return ((new QueryBuilder())->insert("roles")->fields("name","description")->values(":name",":description")->build("insert"));}
public static function update(){return((new QueryBuilder())->update("roles")->set("name = :name","description = :description")->where("id = :id")->build("update"));}

//public $update='update users set name =":name" , email =":email",role=":role" where id=:id ;';
public static function delete(){return ((new QueryBuilder())->from("roles")->where("id = :id")->build("delete"));}
//public $delete='delete from users where id=":id"';
//TODO move these to migrations
public $create_table="create table roles ( id int  auto_increment,name varchar(50) not null ,description varchar(280) ,primary key(id),unique(name));
";
public $delete_table="delete table users;";

}


?>
