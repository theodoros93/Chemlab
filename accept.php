<?php
include('connect.php');
include('libraries.php');
$id= $_GET['id'];
$stmt = $pdo->prepare('INSERT INTO web_users (username, password, email) 
                       SELECT username, password, email FROM requests 
                       WHERE id=:id');
$id = filter_var($id,FILTER_SANITIZE_NUMBER_INT);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute([':id' => $id]);

$sql .= "DELETE FROM `requests` WHERE `requests`.`id` = :id";

$sql = "DELETE FROM requests WHERE id =  :id";
$stmt2 = $pdo->prepare($sql);
$id = filter_var($id,FILTER_SANITIZE_NUMBER_INT);
$stmt2->bindParam(':id', $id, PDO::PARAM_INT);   
$stmt2->execute();

$pdo = null;

header("Location: request.php ");
?>


