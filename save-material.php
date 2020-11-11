<?php
session_start();
$message = ['message' => 'Κάτι δεν πήγε καλά', 'type' => 'error'];
if ( !empty($_POST) ){
    include('connect.php');
    $base_table = 'materials';
	$id = filter_var($_POST['save']) == 'Αποθήκευση' ? filter_var((int) $_POST['id']) : null;
	#ACTION IS DELETE
	if ( !empty($_POST['delete_id']) ){
		$id = filter_var($_POST['delete_id']);
		$sql = "DELETE FROM $base_table WHERE id = $id";
		$message = ['message' => 'Επιτυχής διαγραφή', 'type' => 'success'];
	} else{
		$cas = filter_var($_POST['cas'],FILTER_SANITIZE_STRING);
	    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
        $density = filter_var((double) $_POST['density']);		
        $mw = filter_var((double) $_POST['mw']);
		$type = (intval($density) == 1) ? 'SOLID' : 'LIQUID';
		if ( !is_int($id) ){
			$sql = "INSERT INTO $base_table (`cas`, `name`, `density`, `mw`, `type`) VALUES ('$cas', '$name', $density, $mw, '$type')";
			$message = ['message' => 'Επιτυχής προσθήκη', 'type' => 'success'];
		} else{
			$sql = "UPDATE $base_table SET `cas` = '$cas', `name` = '$name', `density` = $density, `mw` = $mw, `type` = '$type' WHERE `id` = $id";
			$message = ['message' => 'Επιτυχής επεξεργασία', 'type' => 'success'];
		}
	}
	if ( !empty($sql) ){
        echo $sql;
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':density', $_POST['density'], PDO::PARAM_INT);       
        $stmt->bindParam(':mw', $_POST['mw'], PDO::PARAM_INT);    
        $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
	    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(':cas', $_POST['cas'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);  
		$stmt->execute();
		$sql = null;
		$_SESSION['post_message'] = $message;
	}
	
	header("Location: material.php");
}
?>