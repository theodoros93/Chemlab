<?php
include "checkuser.php";
include ('connect.php');
include ('libraries.php');
echo '<html>
<header>
<title>Delete Lab</title>

</header>
<body>';

$lid = filter_var($_GET['labid'],FILTER_SANITIZE_NUMBER_INT);
try {
$stmt = $pdo->prepare("SELECT * FROM `lab_experiments` WHERE `id` = '$lid'");
$stmt->bindParam(':lid', $_POST['id'], PDO::PARAM_INT); 
   $stmt->execute(array());
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $count = $stmt->rowCount();

echo '<div class="LoginBox2">
	<h3>Are you sure you want to delete the Lab report: ' .$row["name"]. '? </h3>
	<form method="post">
	<input type="submit" class="btn btn-success" name="yes" value="YES">
	<input type="submit" class="btn btn-danger" name="no" value="NO">
	</form>
</div>';

if (isset($_POST["yes"])){
	
		$sql = "DELETE FROM lab_experiments WHERE id = '$lid'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':lid', $_POST['id'], PDO::PARAM_INT);   
$stmt->execute();
		header("Location: labs.php ");
	}
	elseif(isset($_POST["no"])){
		header("Location: labs.php ");
	}
	}

  catch(PDOException $e){
   echo $e->getMessage();
  }


echo '</body>
</html>';
include ('footer.php');
?>