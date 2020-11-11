<?php
if ( !empty($_POST) ){
    include('connect.php');
    $base_table = 'material_price';
    $material_id = filter_var((int) $_POST['id']);
    $date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$price = filter_var((double) $_POST['price']);
	#CHECK IF MATERIAL AND DATE ASSOCIATION EXIST
	$sql = "SELECT * FROM $base_table WHERE `material_id` = $material_id AND `date` = '$date'";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':material_id', $_POST['material_id'], PDO::PARAM_INT);       
    $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR);    
	$stmt->execute();
	$material = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( !empty($material) ){
		$id = $material['id'];
		$sql = "UPDATE $base_table SET `price` = $price WHERE `id` = $id";
	} else{
		$sql = "INSERT INTO $base_table (`material_id`, `date`, `price`) VALUES ($material_id, '$date', $price)";	
	}
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':material_id', $_POST['material_id'], PDO::PARAM_INT);       
    $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR); 
    $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_INT);       
	$stmt->execute();
	header("Location:material_price.php?mid='.$material_id.'&date='.$date");
}
?>