<?php

// connection information
$db_host = 'localhost';
$db_name= 'chemlab';
$db_user= 'root';
$db_pass= '';
// connect to database or return error
try
{
 $pdo = new PDO("mysql:unix_socket=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
 $pdo ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 $pdo ->query('set character_set_client=utf8');
 $pdo ->query('set character_set_connection=utf8');
 $pdo ->query('set character_set_results=utf8');
 $pdo ->query('set character_set_server=utf8');
 }

catch(PDOException $e)
{
die('Connection error:' . $pe->getmessage()); 
}
return $pdo ;
?>