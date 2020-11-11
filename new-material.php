<?php
session_start();
$message = ['message' => 'Κάτι δεν πήγε καλά', 'type' => 'error'];
if ( !empty($_POST) ){
    include('connect.php');
	$cas = filter_var($_POST['cas'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$density = filter_var((double) $_POST['density']);
	$mw = filter_var((double) $_POST['mw']);
	$type = (intval($density) == 1) ? 'SOLID' : 'LIQUID';
	$sql = "INSERT INTO `materials` (`cas`, `name`, `density`, `mw`, `type`) VALUES ('$cas', '$name', $density, $mw, '$type')";
	$_SESSION['post_message'] = ['message' => 'Επιτυχής προσθήκη', 'type' => 'success'];
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':density', $_POST['density'], PDO::PARAM_INT);       
    $stmt->bindParam(':mw', $_POST['mw'], PDO::PARAM_INT);    
    $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
    $stmt->bindParam(':cas', $_POST['cas'], PDO::PARAM_STR);
    $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);          
	$stmt->execute();
	header("Location: material.php");
}
