<?php
session_start();
$message = ['message' => 'Κάτι δεν πήγε καλά', 'type' => 'error'];
if ( !empty($_POST) ){
    include('connect.php');
	$id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
	$cas = filter_var($_POST['cas'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$density = filter_var((double) $_POST['density']);
	$mw = filter_var((double) $_POST['mw']);
	$type = (intval($density) == 1) ? 'SOLID' : 'LIQUID';
	$sql = "UPDATE `materials` SET  `density` = $density, `mw` = $mw, `type` = '$type' WHERE `id` =:id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':density', $_POST['density'], PDO::PARAM_INT);       
    $stmt->bindParam(':mw', $_POST['mw'], PDO::PARAM_INT);    
    $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$_SESSION['post_message'] = ['message' => 'Επιτυχής επεξεργασία', 'type' => 'success'];
	header("Location: material2.php");
}
?>