<?php
session_start();
$message = ['message' => 'Κάτι δεν πήγε καλά', 'type' => 'error'];
if ( !empty($_POST) ){
    include('connect.php');
	$id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
	$sql = "DELETE FROM `materials` WHERE `id` =:id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$_SESSION['post_message'] = ['message' => 'Επιτυχής διαγραφή', 'type' => 'success'];
	header("Location: material2.php");
}
?>