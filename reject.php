<?php
    include('connect.php');
    $id = $_GET['id'];
    
$sql = "DELETE FROM requests WHERE id =  :id";
$stmt2 = $pdo->prepare($sql);
$id = filter_var($id);
$stmt2->bindParam(':id', $id, PDO::PARAM_INT);   
$stmt2->execute();

$pdo = null;

header("Location: request.php ");
?>