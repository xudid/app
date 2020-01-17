<?php
namespace Brick\Users;
use Brick\Db\QueryBuilder;
class UserSQL{

//TODO
//write a parent class with class name in constructor
//public static function findAll();
//public static function findBy();
//public static function new();
//public static function update();
//public static function delete();
//public static function deleteAll();
public static function getAll(){return ((new QueryBuilder())->from("users")->build("select"));}
public static function findById(){return ((new QueryBuilder())->from("users")->where("id = :id")->build("select"));}
public static function findByName(){return ((new QueryBuilder())->from("users")->where("name = :name")->build("select"));}
public static function findByEmail(){return ((new QueryBuilder())->from("users")->where("email =:email")->build("select")) ;}
public static function findBy(array $params)
{
  $qb = new QueryBuilder();
  $qb->from("users");
  foreach ($params as $key=>$value)
  {

    $qb->where($key." = :".$key);
  }


  return $qb->build("select");
}
public static function new(){return ((new QueryBuilder())->insert("users")->fields("name","email","role","password")->values(":name",":email",":role",":password")->build("insert"));}
public static function update(){return((new QueryBuilder())->update("users")->set("name = :name","email = :email","role = :role")->where("id = :id")->build("update"));}

//public $update='update users set name =":name" , email =":email",role=":role" where id=:id ;';
public static function delete(){return ((new QueryBuilder())->from("users")->where("id = :id")->build("delete"));}
//public $delete='delete from users where id=":id"';
//TODO move these to migrations
public $create_table="create table users ( id int  auto_increment,name varchar(50) not null , email varchar(100)not null, role enum('qis','n3','soa','none') default 'none' not null , password varchar(256),salt varchar(50),alghash varchar(10),primary key(id),unique(name));
";
public $delete_table="delete table users;";

}


?>
