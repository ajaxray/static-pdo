<?php
require_once 'src/StaticPdo/Db.php';

use StaticPdo\Db;

// Set your connection info here
Db::setConnectionInfo('test', 'root', 'root*');

// Creating table for our testing
$query = "CREATE TABLE IF NOT EXISTS users(
			id INTEGER PRIMARY KEY,
			username varchar(100) NOT NULL,
			password varchar(100) NOT NULL,
			status   char(10)
		);";
		
Db::execute($query);

// Let's create some data
$data = array(
	array('name'=>'anis', 'pass'=>'ajaxray'),
	array('name'=>'emran', 'pass'=>'phpfour'),
	array('name'=>'raju', 'pass'=>'stylephp'),
);

foreach($data as $user){
	Db::execute('INSERT INTO users(username, password) VALUES(:name, :pass)', $user);
}

// Retriving a field
$name =  Db::getValue('SELECT username FROM users WHERE id = ?', 2);
echo 'Name of 2nd user : ' . $name . "<br />";

// Retriving a row
$row =  Db::getRow('SELECT * FROM users WHERE id = ?', 1);
echo 'Row of 1st user : <pre>';
print_r($row);
echo "</pre> <br />";

// Retriving resultset
$result =  Db::getResult('SELECT * FROM users limit 2');
echo '1st 3 users : <pre>';
print_r($result);
echo "</pre> <br />";
