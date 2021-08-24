<?php

 require_once "./vendor/autoload.php";
require_once "./src/FlezyORM.php";

use Oboynitro\FlezyORM\FlezyORM;

$db = new FlezyORM();
$db->createConnection("localhost", "root", "", "test");


echo "<pre>";

// TODO: selecting data
// methods {distinct, }

//$db->table("users");

// fetching all data
// print_r($db->all());
// print_r($db->table("users")->limit(2)->all());
// print_r($db->table("users")->all());
// print_r($db->table("users")->count());
// print_r($db->table("products")->all());

// fetch custom fields
// print_r($db->select("id, username, phone")->get());
// print_r($db->table("users")->select("id, username, phone")->get());

// ordering data
// print_r($db->orderBy("id")->get());
// print_r($db->orderByDesc("id")->get());
// print_r($db->table("users")->orderBy("id")->get());
// print_r($db->table("users")->orderByDesc("id")->get());

// fetching single data

// fetch by a column (username)
// print_r($db->where("username", "user1")->get());
// print_r($db->table("users")->where("phone", "123456")->get());
// print_r($db->table("users")->whereLike("phone", "12345")->all());
// print_r($db->table("users")->select("username, phone")->where("phone", "1234")->get());
// $user = $db->table("users")->select("username, phone")->where("phone", "1234")->get();
// print_r($user->username);

// fetch by id (3)
// print_r($db->find(10));
// print_r($db->table("users")->find(3));
// print_r($db->table("users")->select("username, phone")->find(3));


// Manipulating data
//echo($db->table("users")->create([
//    "username" => "test",
//    "phone" => "0000",
//    "password" => "test123"
//]));
//echo($db->table("users")->update(5, ["phone" => "0001"]));
//echo($db->table("users")->destroy(5));

echo "</pre>";